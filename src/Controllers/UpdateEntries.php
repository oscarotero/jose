<?php

namespace Jose\Controllers;

use Jose\Actions\FetchNewEntries;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Middlewares\Utils\Factory;

class UpdateEntries extends Controller
{
    public function __invoke(ServerRequestInterface $request)
    {
        $this->app->update();
        
        return Factory::createResponse(302)
            ->withHeader('Location', (string) $this->app->getUri());
    }
}
