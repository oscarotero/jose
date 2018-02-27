<?php

namespace Jose\Providers;

use Psr\Container\ContainerInterface;
use Interop\Container\ServiceProviderInterface;
use SimpleCrud\SimpleCrud;
use PDO;

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
            }
        ];
    }

    public function getExtensions()
    {
        return [];
    }
}
