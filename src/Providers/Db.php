<?php

namespace Jose\Providers;

use Psr\Container\ContainerInterface;
use Interop\Container\ServiceProviderInterface;
use SimpleCrud\Database;
use PDO;

class Db implements ServiceProviderInterface
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

            'db' => function (ContainerInterface $container): Database {
                return new Database($container->get('pdo'));
            }
        ];
    }

    public function getExtensions()
    {
        return [];
    }
}
