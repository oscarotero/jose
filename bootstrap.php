<?php

include __DIR__.'/vendor/autoload.php';

//Init .env variables
Dotenv\Dotenv::create(__DIR__)->load();

Env::init();
