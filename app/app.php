<?php

require __DIR__ . '/bootstrap.php';

use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

$app->get('/', function() use ($app) {
    return new Response('Homepage');
});

return $app;
