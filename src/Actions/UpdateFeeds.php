<?php

namespace Jose\Actions;

use SimpleCrud\SimpleCrud;
use SimpleCrud\RowCollection;
use Psr\Log\LoggerInterface;
use Throwable;

class UpdateFeeds
{
    private $db;
    private $logger;

    public function __construct(SimpleCrud $db, LoggerInterface $logger = null)
    {
        $this->db = $db;
        $this->logger = $logger;
    }

    public function __invoke(array $data)
    {
        foreach ($data as $feed) {
            $this->updateFeed($feed);
        }
    }

    private function updateFeed(array $data)
    {
        $feed = $this->db->feed
                ->select()
                ->one()
                ->by('feed', $data['feed'])
                ->run();

        if ($feed) {
            return $feed->edit($data)->save();
        }

        try {
            $this->db->feed
                ->insert()
                ->data($data)
                ->run();
        } catch (Throwable $e) {
            if (!$this->logger) {
                throw $e;
            }

            $this->logger->error($e->getMessage(), [
                'exception' => $e,
                'data' => 'data'
            ]);
        }
    }
}