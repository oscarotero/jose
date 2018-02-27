<?php

namespace Jose\Actions;

use SimpleCrud\SimpleCrud;
use SimpleCrud\RowCollection;

class LatestEntries
{
    private $db;

    public function __construct(SimpleCrud $db)
    {
        $this->db = $db;
    }

    public function __invoke(int $page = 1): RowCollection
    {
        return $this->db->entry
            ->select()
            ->leftJoin('feed')
            ->where('feed.isEnabled = 1')
            ->page($page, 50)
            ->orderBy('publishedAt', 'DESC')
            ->run();
    }
}