<?php

namespace Jose;

use Fol\App as FolApp;
use Middlewares;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class App extends FolApp
{
    public function __construct()
    {
        parent::__construct(dirname(__DIR__), Factory::createUri(env('JOSE_URL')));

        $this->addServiceProvider(new Providers\Db());
        $this->addServiceProvider(new Providers\Router());
        $this->addServiceProvider(new Providers\Templates());
        $this->addServiceProvider(new Providers\Logger());
    }

    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        $container = new Middlewares\Utils\RequestHandlerContainer([$this]);

        return Dispatcher::run([
            new Middlewares\Emitter(),
            new Middlewares\ResponseTime(),
            new Middlewares\JsonPayload(),
            (new Middlewares\ReportingLogger($this->get('logger')))
                ->message('JS Error'),
            new Middlewares\DigestAuthentication([
                env('JOSE_USERNAME') => env('JOSE_PASSWORD'),
            ]),
            // new Middlewares\ErrorHandler(),
            new Middlewares\GzipEncoder(),
            new Middlewares\ContentType(),
            new Middlewares\BasePath($this->getUri()->getPath()),
            function ($request, $next) {
                $query = $request->getQueryParams();

                if (!empty($query['path'])) {
                    $uri = $request->getUri()->withPath($query['path']);
                    $request = $request->withUri($uri);
                }

                return $next->handle($request);
            },
            new Middlewares\FastRoute($this->get('router')),
            new Middlewares\RequestHandler($container),
        ], $request);
    }
}
