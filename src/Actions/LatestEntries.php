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

    public function __invoke(): RowCollection
    {
        return $this->db->entry
            ->select()
            ->leftJoin('feed')
            ->limit(50)
            ->run();
    }
}