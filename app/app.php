<?php

require __DIR__ . '/bootstrap.php';

use Symfony\Component\HttpFoundation\Response;
use Imagine\Image\Box;
use Imagine\Imagick\Font;
use Imagine\Image\Point;

$app = new Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../src/views',
));

$app['imagine'] = $app->share(function(Silex\Application $app) {
    return new Imagine\Imagick\Imagine();
});
$app['imagick'] = $app->share(function(Silex\Application $app) {
    return new \Imagick();
});

$app->get('/', function() use ($app) {
    return $app['twig']->render('index.twig');
});

$app->get('/gifcard', function() use ($app) {
    set_time_limit(200);

    $path = function($p) {
        return __DIR__ . '/../src/images/' . $p;
    };

    $imageName = uniqid() . '.gif';
    $imageLocation = __DIR__ . '/../web/images/' . $imageName;

    // Configurable parameters
    $width = 500;
    $height = 500;
    $text1 = 'You just got gifcarded';
    $text1Size = 28;
    $text2 = '$15';
    $text2Size = 80;

    $fontColor = '000000';
    $font = 'amble';

    $image = $app['imagine']->create(new Box($width, $height));
    $image->layers()
        ->add($app['imagine']->open($path('osky-logo.gif')))
        ->add($app['imagine']->open($path('pbj.gif')))
        ->add($app['imagine']->open($path('smiling-dog.gif')))
        //->add($app['imagine']->open($path('paddle-ball.gif')))
        ->add($app['imagine']->open($path('cat.gif')))
        ->add($app['imagine']->open($path('puppy.gif')))
        ->add($app['imagine']->open($path('cat2.gif')))
        ->add($app['imagine']->open($path('aww-snap.gif')))
        ->add($app['imagine']->open($path('dance.gif')))
        //->add($app['imagine']->open($path('cat-licking.gif')))
        ->add($app['imagine']->open($path('train-wreck.gif')))
        ->add($app['imagine']->open($path('cat-fail.gif')))
        ->add($app['imagine']->open($path('dog.gif')))
        ->add($app['imagine']->open($path('nyan_cat.gif')))
        ->add($app['imagine']->open($path('rabbit.gif')))
        ->add($app['imagine']->open($path('hiss.gif')))
        ->add($app['imagine']->open($path('puppies.gif')))
        ->add($app['imagine']->open($path('osky-logo.gif')))
    ;

    $fontDir = __DIR__ . '/../src/fonts/';
    $color = $image->palette()->color($fontColor);

    switch ($font) {
        case 'amble':
            $fontPath = 'amble/Amble-Bold.ttf';
            break;
        case 'pt-sans':
            $fontPath = 'PT-Sans/PTS75F.ttf';
            break;
    }

    $font1 = new Font(
        $app['imagick'],
        $fontDir . $fontPath,
        $text1Size,
        $color
    );
    $font2 = new Font(
        $app['imagick'],
        $fontDir . $fontPath,
        $text2Size,
        $color
    );
    foreach ($image->layers() as $layer) {
        $layer->resize(new Box($width, $height));
        if ($text1) {
            $point = new Point(100, 100);
            $layer
                ->draw()
                ->text($text1, $font1, $point);
        }

        if ($text2) {
            $point = new Point(150, 300);
            $layer
                ->draw()
                ->text($text2, $font2, $point);
        }
    }
    $image->layers()->animate('gif', 100, 0);
    $image->layers()->coalesce();

    $image->save($imageLocation, array(
        'animated' => true,
        'animated.loops' => 0,
    ));


    return $app['twig']->render('image.twig', array(
        'imagePath' => 'images/' . $imageName,
    ));
});

return $app;
