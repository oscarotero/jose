<?php

namespace Jose\Providers;

use Psr\Container\ContainerInterface;
use Interop\Container\ServiceProviderInterface;
use SimpleCrud\SimpleCrud;
use PDO;
use FlyCrud\Directory;
use FlyCrud\Formats\Yaml;

class Database implements ServiceProviderInterface
{
    public function getFactories()
    {
        return [
            'pdo' => function (): PDO {
                return new PDO(
                    env('JOSE_DB_DSN'),
                    env('JOSE_DB_USERNAME'),
                    env('JOSE_DB_PASSWORD')
                );
            },

            'db' => function (ContainerInterface $container): SimpleCrud {
                return new SimpleCrud($container->get('pdo'));
            },

            'subscriptions' => function (ContainerInterface $container): Directory {
                return Directory::make($container->getPath('subscriptions'), new Yaml());
            }
        ];
    }

    public function getExtensions()
    {
        return [];
    }
}
