<?php

namespace Jose\Controllers;

use Jose\App;
use Jose\Actions\LatestEntries;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class ListEntries
{
    private $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $db = $this->app->get('db');

        $latestEntries = new LatestEntries(
            $db,
            $this->app->get('logger')
        );

        $query = $request->getQueryParams();
        $page = (int) ($query['page'] ?? 1);
        $category = (int) ($query['category'] ?? null);
        $saved = (bool) ($query['saved'] ?? false);
        
        $entries = $latestEntries($page, $saved, $category);
        $entries->feed->category;

        echo $this->app->get('templates')->render('entries', [
            'entries' => $entries,
            'categories' => $db->category->select()->run(),
            'page' => $page,
            'saved' => $saved,
            'category' => $category
        ]);
    }
}
