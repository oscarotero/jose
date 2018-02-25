<?php

namespace Jose;

use SimplePie;
use SimplePie_Item;
use Embed\Embed;
use Embed\Http\Response;
use SimpleCrud\Row;
use Symfony\Component\CssSelector\CssSelectorConverter;
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
        $embed = Embed::create($item->get_link());

        return [
            'url' => $embed->url,
            'title' => $embed->title,
            'description' => $embed->description,
            'publishedAt' => $item->get_date('Y-m-d H:i:s') ?? $embed->publishedDate,
            'body' => $this->extractBody($feed, $embed->getResponse()) ?: $item->get_content(true)
        ];
    }

    private function extractBody(Row $feed, Response $response)
    {
        $contentSelector = $feed->contentSelector;
        $document = $response->getHtmlContent();

        if (!$contentSelector || !$document) {
            return;
        }

        $xpath = new DOMXPath($document);

        //Remove ignored
        if ($feed->ignoredSelector) {
            $elements = $this->select($xpath, $feed->ignoredSelector);

            foreach ($elements as $element) {
                $element->parentNode->removeChild($element);
            }
        }
        
        //Get content
        $element = $this->select($xpath, $contentSelector, true);

        if ($element) {
            $this->cleanCode($xpath, $element);

            $html = '';

            foreach ($element->childNodes as $child) {
                $html .= $child->ownerDocument->saveHTML($child);
            }

            return $html;
        }
    }

    /**
     * @return DOMElement|array|null
     */
    private function select(DOMXPath $xpath, string $selector, bool $returnFirst = false)
    {
        $entries = $xpath->query($this->converter->toXpath($selector));

        if ($entries->length) {
            return $returnFirst ? $entries->item(0) : iterator_to_array($entries, false);
        }

        return $returnFirst ? null : [];
    }

    private function cleanCode(DOMXPath $xpath, DOMNode $context)
    {
        foreach ($this->select($xpath, '[class],[id],[style]') as $element) {
            $element->removeAttribute('class');
            $element->removeAttribute('id');
            $element->removeAttribute('style');
        }

        foreach ($this->select($xpath, '[aria-hidden],[hidden],meta,style,canvas,svg') as $element) {
            $element->parentNode->removeChild($element);
        }
    }
}
