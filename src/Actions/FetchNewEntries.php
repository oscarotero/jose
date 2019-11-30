<?php

namespace Jose\Actions;

use Datetime;
use Imagecow\Image;
use Jose\Parser;
use Psr\Log\LoggerInterface;
use SimpleCrud\Database;
use SimpleCrud\Row;
use Throwable;

class FetchNewEntries
{
    private $db;
    private $parser;
    private $logger;

    public function __construct(Database $db, LoggerInterface $logger = null, Parser $parser = null)
    {
        $this->db = $db;
        $this->logger = $logger;
        $this->parser = $parser ?: new Parser();
    }

    public function __invoke()
    {
        $feeds = $this->db->feed
            ->select()
            ->where('isEnabled = 1')
            ->run();

        foreach ($feeds as $feed) {
            $this->updateFeed($feed);
        }
    }

    private function updateFeed(Row $feed)
    {
        $parsed = $this->parser->parseFeed($feed->feed);

        if (!$feed->lastCheckAt) {
            $feed->title = $parsed['title'];
            $feed->url = $parsed['url'];
        }

        $feed->lastCheckAt = new Datetime();

        $feed->save();

        foreach ($parsed['entries'] as $item) {
            $exists = $this->db->entry
                ->selectAggregate('COUNT')
                ->where('guid = ', $item->get_id())
                ->limit(1)
                ->run();

            if (!$exists) {
                try {
                    $data = $this->parser->parseEntry($item, $feed);
                    $data['feed_id'] = $feed->id;

                    if ($data['image']) {
                        $data['image_id'] = $this->saveImage($data['image']);
                    }

                    unset($data['image']);

                    $this->db->entry
                        ->insert($data)
                        ->run();
                } catch (Throwable $e) {
                    if (!$this->logger) {
                        throw $e;
                    }

                    $this->logger->error($e->getMessage(), [
                        'exception' => $e,
                        'file' => __FILE__,
                        'line' => __LINE__,
                        'data' => $data ?? null,
                    ]);
                }
            }
        }
    }

    private function saveImage(string $url)
    {
        $image = $this->db->image
            ->select()
            ->one()
            ->where('url = ', $url)
            ->run();

        if ($image) {
            return $image->id;
        }

        try {
            return $this->db->image
                ->insert([
                    'url' => $url,
                    'data' => Image::fromFile($url)
                                ->resizeCrop(100, 100)
                                ->format('jpg')
                                ->base64(),
                ])
                ->run();
        } catch (Throwable $e) {
        }
    }
}
