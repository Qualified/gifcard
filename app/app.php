<?php

require __DIR__ . '/bootstrap.php';

use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../src/views',
));

$app->get('/', function() use ($app) {
    return $app['twig']->render('index.twig');
});

return $app;
