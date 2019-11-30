<?php

namespace Jose\Providers;

use Interop\Container\ServiceProviderInterface;
use Jose\App;
use League\Plates\Engine;

class Templates implements ServiceProviderInterface
{
    public function getFactories()
    {
        return [
            'templates' => function (App $app): Engine {
                return new Engine($app->getPath('templates'));
            },
        ];
    }

    public function getExtensions()
    {
        return [];
    }
}
