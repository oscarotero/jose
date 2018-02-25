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
                    'mysql:dbname=jose;host=localhost;charset=utf8mb4',
                    'root',
                    ''
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
