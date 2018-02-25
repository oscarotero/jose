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
        $latestEntries = new LatestEntries($this->app->get('db'));
        
        echo $this->app->get('templates')->render('list-entries', [
            'entries' => $latestEntries()
        ]);
    }
}
