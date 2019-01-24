<?php

namespace Jose\Actions;

use SimpleCrud\SimpleCrud;
use SimpleCrud\RowCollection;
use SimpleCrud\Row;
use Psr\Log\LoggerInterface;
use FlyCrud\Document;
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

    public function __invoke(Document $document, Row $category)
    {
        foreach ($document as $feed) {
            $this->updateFeed((array) $feed, $category);
        }
    }

    private function updateFeed(array $data, Row $category)
    {
        $feed = $this->db->feed
                ->select()
                ->one()
                ->by('feed', $data['feed'])
                ->run();

        if ($feed) {
            return $feed->edit($data)->relate($category)->save();
        }

        try {
            $this->db->feed->create($data)
                ->relate($category)
                ->save();
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
