<?php

namespace Jose\Controllers;

use Jose\Actions\LatestEntries;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class ListEntries extends Controller
{
    public function __invoke(ServerRequestInterface $request)
    {
        $db = $this->app->get('db');

        $latestEntries = new LatestEntries(
            $db,
            $this->app->get('logger')
        );

        $query = $request->getQueryParams();
        $page = (int) ($query['page'] ?? 1);
        $category = isset($query['category']) ? (int) $query['category'] : null;
        $feed = isset($query['feed']) ? (int) $query['feed'] : null;
        $saved = (bool) ($query['saved'] ?? false);
        $search = $query['q'] ?? null;

        $entries = $latestEntries($page, $saved, $category, $feed, $search);
        $entries->feed->category;

        echo $this->app->get('templates')->render('entries', [
            'entries' => $entries,
            'categories' => $db->category->select()->run(),
            'page' => $page,
            'saved' => $saved,
            'category' => $category,
            'feed' => $feed
        ]);
    }
}
