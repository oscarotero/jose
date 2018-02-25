<?php

namespace Jose\Providers;

use Jose\App;
use Interop\Container\ServiceProviderInterface;
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
