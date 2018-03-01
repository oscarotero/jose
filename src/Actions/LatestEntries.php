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

    public function __invoke(int $page = 1, bool $saved = false): RowCollection
    {
        $query = $this->db->entry
            ->select()
            ->leftJoin('feed')
            ->leftJoin('image')
            ->where('feed.isEnabled = 1')
            ->page($page, 50)
            ->orderBy('publishedAt', 'DESC');

        if ($saved) {
            $query->where('entry.isSaved = 1');
        }

        return $query->run();
    }
}