<?php

namespace Jose\Actions;

use Jose\Parser;
use SimpleCrud\SimpleCrud;
use Psr\Log\LoggerInterface;
use Exception;
use Throwable;
use Datetime;

class FetchNewEntries
{
    private $db;
    private $parser;
    private $logger;

    public function __construct(SimpleCrud $db, LoggerInterface $logger = null, Parser $parser = null)
    {
        $this->db = $db;
        $this->logger = $logger;
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
                $data = $this->parser->parseEntry($item, $feed);
                $data['feed_id'] = $feed->id;

            try {
                $this->db->entry
                    ->insert()
                    ->duplications()
                    ->data($data)
                    ->run();
                } catch (Throwable $e) {
                        throw $e;
                    if (!$this->logger) {
                    }

                    $this->logger->error($e->getMessage(), [
                        'exception' => $e,
                        'data' => $data
                    ]);
                }
            }
        }
    }
}