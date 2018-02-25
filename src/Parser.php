<?php

namespace Jose;

use SimplePie;
use SimplePie_Item;
use Embed\Embed;

class Parser
{
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

    public function parseEntry(SimplePie_Item $item): array
    {
        $embed = Embed::create($item->get_link());

        return [
            'url' => $embed->url,
            'title' => $embed->title,
            'description' => $embed->description,
            'publishedAt' => $item->get_date('Y-m-d H:i:s') ?? $embed->publishedDate,
            'body' => $item->get_content(true)
        ];
    }
}
