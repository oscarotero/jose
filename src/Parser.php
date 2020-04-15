<?php

namespace Jose;

use DOMNode;
use DOMXPath;
use Embed\Embed;
use Embed\Document;
use Embed\Extractor;
use HtmlParser\Parser as HtmlParser;
use SimpleCrud\Row;
use SimpleCrud\Table;
use SimplePie;
use SimplePie_Item;
use Symfony\Component\CssSelector\CssSelectorConverter;

class Parser
{
    private $converter;
    private $embed;

    public function __construct()
    {
        $this->converter = new CssSelectorConverter();
        $this->embed = new Embed();
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
            'entries' => $simplePie->get_items(),
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
        $info = $this->embed->get($url);

        $url = $info->resolveUri('/');

        $scrapper = $scrappers->select()
                        ->one()
                        ->where('url LIKE ', "%{$url}%")
                        ->run();

        $this->cleanCode($info);

        if ($scrapper) {
            $body = $this->extractBody($info->getDocument(), $scrapper);

            if (filter_var($body, FILTER_VALIDATE_URL)) {
                if ($redirect) {
                    return $this->runScrapper($body, $item, $scrappers, false);
                }

                var_dump($url);

                die();
            }
        } else {
            $body = $info->getDocument()->select('.//body')->node();
        }

        return [
            'url' => $info->url,
            'title' => $info->title,
            'description' => $info->description,
            'publishedAt' => $item->get_date('Y-m-d H:i:s') ?: $info->publishedDate,
            'image' => $info->image,
            'body' => $body ? str_ireplace(['<noscript>', '</noscript>'], '', HtmlParser::stringify($body)) : null,
            'guid' => $item->get_id(),
        ];
    }

    private function extractBody(Document $document, Row $scrapper): ?string
    {
        if (!$scrapper->contentSelector) {
            return null;
        }

        //Remove ignored
        if ($scrapper->ignoredSelector) {
            $document->remove($this->converter->toXpath($scrapper->ignoredSelector));
        }

        //Get content
        $content = array_map(
            function ($element) {
                if ($element->tagName === 'a') {
                    return $element->getAttribute('href');
                }

                $html = '';

                foreach ($element->childNodes as $child) {
                    $html .= $child->ownerDocument->saveHTML($child);
                }

                return trim($html);
            },
            $document->select($this->converter->toXpath($scrapper->contentSelector))->nodes()
        );

        return implode('', $content) ?: null;
    }

    private function cleanCode(Extractor $info)
    {
        $document = $info->getDocument();

        foreach ($document->select($this->converter->toXpath('[class],[id],[style]'))->nodes() as $element) {
            $element->removeAttribute('class');
            $element->removeAttribute('id');
            $element->removeAttribute('style');
        }

        $document->remove($this->converter->toXpath('[aria-hidden],[hidden],meta,style,canvas,svg,form,script,template,link,.hidden'));

        $this->resolveUrls($info);
    }

    private function resolveUrls(Extractor $info)
    {
        $document = $info->getDocument();

        foreach ($document->select($this->converter->toXpath('[href]'))->nodes() as $element) {
            $href = $element->getAttribute('href');
            $element->setAttribute('href', $info->resolveUri($href));

            if ($element->nodeName === 'a') {
                $element->setAttribute('target', '_blank');
            }
        }

        foreach ($document->select($this->converter->toXpath('[src]'))->nodes() as $element) {
            if (!in_array($element->tagName, ['img', 'video', 'audio', 'ul', 'ol'])) {
                continue;
            }

            $src = $element->getAttribute('src');

            if ($src) {
                $proxied = 'proxy.php?'.http_build_query(['url' => $info->resolveUri($src)]);
                $element->setAttribute('src', $proxied);
            }

            if ($element->hasAttribute('srcset')) {
                $element->removeAttribute('srcset');
            }
        }
    }
}
