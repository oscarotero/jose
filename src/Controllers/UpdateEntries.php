<?php

namespace Jose\Controllers;

use Jose\Actions\FetchNewEntries;
use Middlewares\Utils\Factory;
use Psr\Http\Message\ServerRequestInterface;

class UpdateEntries extends Controller
{
    public function __invoke(ServerRequestInterface $request)
    {
        $newEntries = new FetchNewEntries(
            $this->app->get('db'),
            $this->app->get('logger')
        );

        $newEntries();

        return Factory::createResponse(302)
            ->withHeader('Location', (string) $this->app->getUri());
    }
}
