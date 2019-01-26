<?php

include __DIR__.'/vendor/autoload.php';

//Init .env variables
Dotenv\Dotenv::create(__DIR__)->load();

Env::init();

//Execute the app
$app = new Jose\App();

return [
    'paths' => [
        'migrations' => __DIR__.'/db/migrations',
        'seeds' => __DIR__.'/db/seeds',
    ],

    'environments' => [
        'default_database' => 'production',
        'production' => [
            'name' => 'jose',
            'connection' => $app->get('pdo'),
        ],
    ],
];
