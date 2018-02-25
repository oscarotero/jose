<?php

namespace Jose\Controllers;

use Jose\App;
use Jose\Actions\FetchNewEntries;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Middlewares\Utils\Factory;

class UpdateEntries
{
    private $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $newEntries = new FetchNewEntries(
            $this->app->get('db'),
            $this->app->get('logger')
        );

        $newEntries();
        
        return Factory::createResponse(302)
            ->withHeader('Location', '/');
    }
}
