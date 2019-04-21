<?php

namespace Jose;

use SimplePie;
use SimplePie_Item;
use Embed\Embed;
use Embed\Http\Response;
use Embed\Http\Url;
use SimpleCrud\Row;
use SimpleCrud\Table;
use Symfony\Component\CssSelector\CssSelectorConverter;
use HtmlParser\Parser as HtmlParser;
use DOMDocument;
use DOMXPath;
use DOMNode;

class Parser
{
    private $converter;

    public function __construct()
    {
        $this->converter = new CssSelectorConverter();
    }

    public function parseFeed(string $id): array
    {
        $simplePie = new SimplePie();
        $simplePie->set_feed_url($id);
        $simplePie->set_cache_location(dirname(__DIR__).'/data');
        $simplePie->init();

        return [
            'url' => $simplePie->get_link(),
            'feed' => $simplePie->feed_url,
            'title' => $simplePie->get_title(),
            'entries' => $simplePie->get_items()
        ];
    }

    public function parseEntry(SimplePie_Item $item, Row $feed): array
    {
        $db = $feed->getTable()->getDatabase();

        $data = $this->runScrapper($item->get_link(), $item, $db->scrapper);

        return $data;
    }

    public function runScrapper(string $url, SimplePie_Item $item, Table $scrappers, $redirect = true): array
    {
        $embed = Embed::create($url);

        $url = $embed->getResponse()->getUrl()->getAbsolute('/');

        $scrapper = $scrappers->select()
                        ->one()
                        ->where('url LIKE ', "%{$url}%")
                        ->run();

        if ($scrapper) {
            $body = $this->extractBody($embed->getResponse(), $scrapper);

            if (filter_var($body, FILTER_VALIDATE_URL)) {
                if ($redirect) {
                    return $this->runScrapper($body, $item, $scrappers, false);
                }
    
                var_dump($url);
    
                die();
            }
        } else {
            $body = $embed->code ?: $item->get_content(true);
        }

        if ($body) {
            $body = HtmlParser::parseFragment($body);
            $this->cleanCode($body, $embed->getResponse()->getUrl());
        }

        return [
            'url' => $embed->url,
            'title' => $embed->title,
            'description' => $embed->description,
            'publishedAt' => $item->get_date('Y-m-d H:i:s') ?: $embed->publishedDate,
            'image' => $embed->image,
            'body' => $body ? HtmlParser::stringify($body) : null,
            'guid' => $item->get_id(),
        ];
    }

    private function extractBody(Response $response, Row $scrapper): ?string
    {
        $contentSelector = $scrapper->contentSelector;
        $document = $response->getHtmlContent();

        if (!$contentSelector || !$document) {
            return null;
        }

        $xpath = new DOMXPath($document);

        //Remove ignored
        if ($scrapper->ignoredSelector) {
            $elements = $this->select($xpath, $scrapper->ignoredSelector);

            foreach ($elements as $element) {
                $element->parentNode->removeChild($element);
            }
        }
        
        //Get content
        $content = array_map(
            function ($element) use ($xpath, $response) {
                if ($element->tagName === 'a') {
                    return $element->getAttribute('href');
                }
        
                $html = '';
        
                foreach ($element->childNodes as $child) {
                    $html .= $child->ownerDocument->saveHTML($child);
                }
        
                return trim($html);
            },
            $this->select($xpath, $contentSelector)
        );

        return implode('', $content) ?: null;
    }

    private function select(DOMXPath $xpath, string $selector, DOMNode $context = null): array
    {
        $entries = $xpath->query($this->converter->toXpath($selector), $context);

        return $entries->length ? iterator_to_array($entries, false) : [];
    }

    private function cleanCode(DOMNode $context, Url $url)
    {
        $xpath = new DOMXPath($context->ownerDocument);

        foreach ($this->select($xpath, '[class],[id],[style]', $context) as $element) {
            $element->removeAttribute('class');
            $element->removeAttribute('id');
            $element->removeAttribute('style');
        }

        foreach ($this->select($xpath, '[aria-hidden],[hidden],meta,style,canvas,svg,form,script,template,link,.hidden') as $element) {
            $element->parentNode->removeChild($element);
        }

        $this->resolveUrls($xpath, $context, $url);
    }

    private function resolveUrls(DOMXPath $xpath, DOMNode $context, Url $url)
    {
        foreach ($this->select($xpath, '[href]', $context) as $element) {
            $href = $element->getAttribute('href');
            $element->setAttribute('href', $url->getAbsolute($href));

            if ($element->nodeName === 'a') {
                $element->setAttribute('target', '_blank');
            }
        }

        foreach ($this->select($xpath, '[src]', $context) as $element) {
            if (in_array($element->tagName, ['img', 'video', 'audio', 'ul', 'ol'])) {
                $this->resolveSrc($element, $url);
            }
        }
    }

    private function resolveSrc(DOMNode $element, Url $url)
    {
        $src = $element->getAttribute('src');

        if ($src) {
            $proxied = 'proxy.php?'.http_build_query(['url' => $url->getAbsolute($src)]);
            $element->setAttribute('src', $proxied);
        }

        if ($element->hasAttribute('srcset')) {
            $element->removeAttribute('srcset');
        }
    }
}
