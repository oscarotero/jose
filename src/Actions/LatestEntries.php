<?php

namespace Jose\Actions;

use SimpleCrud\Database;
use SimpleCrud\RowCollection;

class LatestEntries
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function __invoke(int $page = 1, bool $saved = false, int $category = null, int $feed = null, string $search = null): RowCollection
    {
        $query = $this->db->entry
            ->select()
            ->joinRelation($this->db->feed)
            ->where('feed.isEnabled = 1')
            ->where('entry.isHidden = 0')
            ->page($page)
            ->perPage(50)
            ->orderBy('entry.id DESC');

        if ($saved) {
            $query->where('entry.isSaved = 1');
        }

        if ($category) {
            $query->where('feed.category_id = ', $category);
        }

        if ($feed) {
            $query->where('feed.id = ', $feed);
        }

        if ($search) {
            $query->where('entry.title LIKE ', "%{$search}%")
                  ->orWhere('entry.description LIKE ', "%{$search}%");
        }

        $result = $query->run();
        $page = $query->getPageInfo();
        $result->setData(compact('page'));

        return $result;
    }
}
