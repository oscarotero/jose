<?php

include __DIR__.'/vendor/autoload.php';

use Jose\Actions;
use Symfony\Component\Yaml\Yaml;

//Init .env variables
(new Dotenv\Dotenv(__DIR__))->load();

Env::init();

$app = new Jose\App();
$app->update();
