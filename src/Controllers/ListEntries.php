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
        $latestEntries = new LatestEntries(
            $this->app->get('db'),
            $this->app->get('logger')
        );

        $query = $request->getQueryParams();
        $page = (int) ($query['page'] ?? 1);
        $saved = (bool) ($query['saved'] ?? false);
        
        echo $this->app->get('templates')->render('entries', [
            'entries' => $latestEntries($page, $saved),
            'page' => $page,
            'saved' => $saved
        ]);
    }
}
