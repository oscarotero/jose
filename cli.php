<?php

include __DIR__.'/vendor/autoload.php';

use Jose\Actions;
use Symfony\Component\Yaml\Yaml;

$app = new Jose\App();

$updateFeeds = new Actions\UpdateFeeds(
    $app->get('db'),
    $app->get('logger')
);
$newEntries = new Actions\FetchNewEntries(
    $app->get('db'),
    $app->get('logger')
);

$updateFeeds(Yaml::parseFile('subscriptions.yaml'));
$newEntries();
