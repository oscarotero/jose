<?php

use Laminas\Diactoros\ServerRequestFactory;

//Error configuration and security
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'/data/php');
ini_set('expose_php', 0);

//Init composer
include dirname(__DIR__).'/vendor/autoload.php';

if (php_sapi_name() === 'cli-server' && $file = Server::run(__DIR__)) {
    if (substr($file, -9) === 'proxy.php') {
        require 'proxy.php';
        return;
    }

    return false;
}

//Init .env variables
Dotenv\Dotenv::createMutable(dirname(__DIR__))->load();

Env::init();

//Execute the app
$app = new Jose\App();
$app->dispatch(ServerRequestFactory::fromGlobals());
