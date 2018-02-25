<?php

use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response\SapiEmitter;

//Error configuration and security
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'/data/php');
ini_set('expose_php', 0);

//Init composer
include dirname(__DIR__).'/vendor/autoload.php';

if (php_sapi_name() === 'cli-server' && Server::run(__DIR__)) {
    return false;
}

//Execute the app
$app = new Jose\App();
$response = $app->dispatch(ServerRequestFactory::fromGlobals());
(new SapiEmitter())->emit($response);
