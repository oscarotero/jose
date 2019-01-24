<?php

include __DIR__.'/bootstrap.php';

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
