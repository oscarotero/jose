<?php

namespace Jose\Actions;

use Jose\Parser;
use SimpleCrud\SimpleCrud;
use Exception;
use Datetime;

class FetchNewEntries
{
    private $db;
    private $parser;

    public function __construct(SimpleCrud $db, Parser $parser = null)
    {
        $this->db = $db;
        $this->parser = $parser ?: new Parser();
    }

    public function __invoke()
    {
        $feeds = $this->db->feed
            ->select()
            ->run();

        foreach ($feeds as $feed) {
            $this->updateFeed($feed->feed);
        }
    }

    private function updateFeed(string $url)
    {
        $feed = $this->db->feed
            ->select()
            ->one()
            ->by('feed', $url)
            ->run();

        if (!$feed) {
            throw new Exception("Invalid feed '$url'");
        }

        $parsed = $this->parser->parseFeed($url);
        
        if (!$feed->lastCheckAt) {
            $feed->title = $parsed['title'];
            $feed->url = $parsed['url'];
        }

        $feed->lastCheckAt = new Datetime();

        $feed->save();

        foreach ($parsed['entries'] as $item) {
            $exists = $this->db->entry
                ->count()
                ->by('url', $item->get_link())
                ->limit(1)
                ->run();

            if (!$exists) {
                $data = $this->parser->parseEntry($item);
                $data['feed_id'] = $feed->id;

                $this->db->entry
                    ->insert()
                    ->duplications()
                    ->data($data)
                    ->run();
            }
        }
    }
}