<?php

namespace Jose\Actions;

use Psr\Log\LoggerInterface;
use SimpleCrud\Database;
use Throwable;

class ToggleHide
{
    private $db;
    private $logger;

    public function __construct(Database $db, LoggerInterface $logger = null)
    {
        $this->db = $db;
        $this->logger = $logger;
    }

    public function __invoke(int $id)
    {
        try {
            $entry = $this->db->entry[$id];
            $entry->isHidden = !$entry->isHidden;
            $entry->save();

            return $entry->isHidden;
        } catch (Throwable $e) {
            if (!$this->logger) {
                throw $e;
            }

            $this->logger->error($e->getMessage(), [
                'exception' => $e,
                'file' => __FILE__,
                'line' => __LINE__,
                'data' => $id,
            ]);
        }
    }
}
