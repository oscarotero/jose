<?php

namespace Jose;

use Fol\App as FolApp;
use Middlewares;
use Middlewares\Utils\Factory;
use Middlewares\Utils\Dispatcher;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Yaml\Yaml;

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
            new Middlewares\JsonPayload(),
            (new Middlewares\ReportingLogger($this->get('logger')))
                ->message('JS Error'),
            new Middlewares\DigestAuthentication([
                env('JOSE_USERNAME') => env('JOSE_PASSWORD')
            ]),
            new Middlewares\ErrorHandler(),
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

    public function update()
    {
        $db = $this->get('db');
        $logger = $this->get('logger');
        
        $updateFeeds = new Actions\UpdateFeeds($db, $logger);
        
        foreach ($this->get('subscriptions')->getAllDirectories() as $directory) {
            foreach ($directory->getAllDocuments() as $id => $document) {
                $category = $db->category->select()->one()->by('title', $id)->run();
    
                if (!$category) {
                    $category = $db->category->create(['title' => $id])->save();
                }

                $updateFeeds($document, $category);
            }
        }

        $newEntries = new Actions\FetchNewEntries($db, $logger);
        $newEntries();
        
        // $updateScrapper = new Actions\UpdateScrapper($db, $logger);
        // $updateScrapper(Yaml::parseFile($this->getPath('scrapper.yaml')));
    }
}
