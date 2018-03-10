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

    public function __invoke(int $page = 1, bool $saved = false, int $category = null, int $feed = null): RowCollection
    {
        $query = $this->db->entry
            ->select()
            ->leftJoin('feed')
            ->leftJoin('image')
            ->where('feed.isEnabled = 1')
            ->where('entry.isHidden = 0')
            ->page($page, 50)
            ->orderBy('publishedAt', 'DESC');

        if ($saved) {
            $query->where('entry.isSaved = 1');
        }

        if ($category) {
            $query->where('feed.category_id = :category', [':category' => $category]);
        }

        if ($feed) {
            $query->where('feed.id = :feed', [':feed' => $feed]);
        }

        return $query->run();
    }
}
