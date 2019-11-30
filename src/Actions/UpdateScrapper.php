<?php

namespace Jose\Actions;

use Psr\Log\LoggerInterface;
use SimpleCrud\Database;
use Throwable;

class UpdateScrapper
{
    private $db;
    private $logger;

    public function __construct(Database $db, LoggerInterface $logger = null)
    {
        $this->db = $db;
        $this->logger = $logger;
    }

    public function __invoke(array $data)
    {
        foreach ($data as $feed) {
            $this->updateScrapper($feed);
        }
    }

    private function updateScrapper(array $data)
    {
        $scrapper = $this->db->scrapper
                ->select()
                ->one()
                ->where('url = ', $data['url'])
                ->run();

        if ($scrapper) {
            return $scrapper->edit($data)->save();
        }

        try {
            $this->db->scrapper
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
                'data' => $data,
            ]);
        }
    }
}
