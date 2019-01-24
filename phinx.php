<?php

include __DIR__.'/vendor/autoload.php';

//Init .env variables
(new Dotenv\Dotenv(__DIR__))->load();

Env::init();

//Execute the app
$app = new Jose\App();

return [
    'paths' => [
        'migrations' => __DIR__.'/db/migrations'
    ],

    'environments' => [
        'default_database' => 'production',
        'production' => [
            'name' => 'jose',
            'connection' => $app->get('pdo'),
        ],
    ],
];
