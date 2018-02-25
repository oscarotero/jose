<?php

namespace Jose;

use Fol\App as FolApp;
use Middlewares;
use Middlewares\Utils\Factory;
use Middlewares\Utils\Dispatcher;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class App extends FolApp
{
    public function __construct()
    {
        parent::__construct(dirname(__DIR__), Factory::createUri('http://localhost:8000'));

        $this->addServiceProvider(new Providers\Database);
        $this->addServiceProvider(new Providers\Router);
        $this->addServiceProvider(new Providers\Templates);
    }

    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        $container = new Middlewares\Utils\RequestHandlerContainer([$this]);

        return Dispatcher::run([
            new Middlewares\ContentType(),
            new Middlewares\FastRoute($this->get('router')),
            new Middlewares\RequestHandler($container),
        ], $request);
    }
}