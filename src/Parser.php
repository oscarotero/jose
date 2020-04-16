<?php

namespace Jose;

use Embed\Document;
use Embed\Embed;
use Embed\Extractor;
use function Embed\isHttp;
use SimpleCrud\Row;
use SimpleCrud\Table;
use SimplePie;
use SimplePie_Item;

class Parser
{
    private $embed;

    public function __construct()
    {
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

        $data = [
            'url' => $info->url,
            'title' => $info->title,
            'description' => $info->description,
            'publishedAt' => $item->get_date('Y-m-d H:i:s') ?: $info->publishedDate,
            'body' => null,
            'image' => $info->image,
            'guid' => $item->get_id(),
        ];

        $url = $info->resolveUri('/');

        $scrapper = $scrappers->select()
                        ->one()
                        ->where('url LIKE ', "%{$url}%")
                        ->run();

        if ($scrapper) {
            $data['body'] = $this->extractBody($info->getDocument(), $scrapper);

            if (filter_var($data['body'], FILTER_VALIDATE_URL)) {
                if ($redirect) {
                    return $this->runScrapper($data['body'], $item, $scrappers, false);
                }

                var_dump($url);

                die();
            }
        }

        return $data;
    }

    private function extractBody(Extractor $info, Row $scrapper): ?string
    {
        $document = $info->getDocument();

        if (!$scrapper->contentSelector) {
            return null;
        }

        //Remove ignored
        if ($scrapper->ignoredSelector) {
            $document->removeCss($scrapper->ignoredSelector);
        }

        //Clean code
        $this->cleanCode($info);

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
            $document->selectCss($scrapper->contentSelector)->nodes()
        );

        return implode('', $content) ?: null;
    }

    private function cleanCode(Extractor $info)
    {
        $document = $info->getDocument();

        foreach ($document->selectCss('[class],[id],[style]')->nodes() as $element) {
            $element->removeAttribute('class');
            $element->removeAttribute('id');
            $element->removeAttribute('style');
        }

        $document->removeCss('[aria-hidden],[hidden],meta,style,canvas,svg,form,script,template,link,.hidden');

        $this->resolveUrls($info);
    }

    private function resolveUrls(Extractor $info)
    {
        $document = $info->getDocument();

        foreach ($document->selectCss('[href]')->nodes() as $element) {
            $href = $element->getAttribute('href');

            if (isHttp($href)) {
                $element->setAttribute('href', $info->resolveUri($href));
            }

            if ($element->nodeName === 'a') {
                $element->setAttribute('target', '_blank');
            }
        }

        foreach ($document->selectCss('[src]')->nodes() as $element) {
            if (!in_array($element->tagName, ['img', 'video', 'audio', 'ul', 'ol'])) {
                continue;
            }

            $src = $element->getAttribute('src');

            if ($src && isHttp($src)) {
                $proxied = 'proxy.php?'.http_build_query(['url' => $info->resolveUri($src)]);
                $element->setAttribute('src', $proxied);
            }

            if ($element->hasAttribute('srcset')) {
                $element->removeAttribute('srcset');
            }
        }
    }
}
