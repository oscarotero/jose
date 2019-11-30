<?php

namespace Jose\Providers;

use Interop\Container\ServiceProviderInterface;
use Jose\App;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;

class Logger implements ServiceProviderInterface
{
    public function getFactories()
    {
        return [
            'logger' => function (App $app): MonologLogger {
                $logger = new MonologLogger('access');

                if (php_sapi_name() === 'cli') {
                    $logger->pushHandler(new ErrorLogHandler());
                } else {
                    $logger->pushHandler(new StreamHandler($app->getPath('data/logs.txt')));
                }

                return $logger;
            },
        ];
    }

    public function getExtensions()
    {
        return [];
    }
}
