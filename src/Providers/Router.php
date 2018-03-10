<?php

namespace Jose\Providers;

use Psr\Container\ContainerInterface;
use Interop\Container\ServiceProviderInterface;
use FastRoute;
use FastRoute\Dispatcher;
use Jose\Controllers;

class Router implements ServiceProviderInterface
{
    public function getFactories()
    {
        return [
            'router' => function (ContainerInterface $container): Dispatcher {
                return FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
                    $r->addRoute('GET', '/', Controllers\ListEntries::class);
                    $r->addRoute('POST', '/', Controllers\UpdateEntries::class);
                    $r->addRoute('POST', '/save', Controllers\ToggleSave::class);
                    $r->addRoute('POST', '/hide', Controllers\ToggleHide::class);
                });
            },
        ];
    }

    public function getExtensions()
    {
        return [];
    }
}
