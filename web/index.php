<?php

require_once __DIR__ . '/../app/bootstrap.php';

$app = new Silex\Application();

$app->get('/', function() use ($app) {
    return 'Homepage';
});

$app->run();
