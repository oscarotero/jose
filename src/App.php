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
        parent::__construct(dirname(__DIR__), Factory::createUri(env('JOSE_URL')));

        $this->addServiceProvider(new Providers\Database);
        $this->addServiceProvider(new Providers\Router);
        $this->addServiceProvider(new Providers\Templates);
        $this->addServiceProvider(new Providers\Logger);
    }

    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        $container = new Middlewares\Utils\RequestHandlerContainer([$this]);

        return Dispatcher::run([
            new Middlewares\ContentType(),
            new Middlewares\BasePath($this->getUri()->getPath()),
            new Middlewares\ErrorHandler(),
            new Middlewares\FastRoute($this->get('router')),
            new Middlewares\RequestHandler($container),
        ], $request);
    }
}
