<?php

namespace Jose\Providers;

use Jose\App;
use Interop\Container\ServiceProviderInterface;
use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\ErrorLogHandler;

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
                    $logger->pushHandler(new StreamHandler($app->getPath('/data/logs.txt')));
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
