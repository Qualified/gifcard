<?php

require __DIR__ . '/bootstrap.php';

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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

$app->get('/gifcard', function(Request $request) use ($app) {
    $q = $request->query;

    $path = function($p) {
        return __DIR__ . '/../src/images/' . $p;
    };

    $imageName = uniqid() . '.gif';
    $imageLocation = __DIR__ . '/../web/images/' . $imageName;

    // Configurable parameters
    $width = $q->get('w', 500);
    $height = $q->get('h', 500);
    $text1 = $q->get('t1', 'You just got gifcarded');
    $text1Size = $q->get('ts1', 28);
    $text2 = $q->get('t2', '$15');
    $text2Size = $q->get('ts2', 80);

    $fontColor = $q->get('fc', '000000');
    $font = $q->get('f', 'amble');
    $img = sprintf('%s.gif', $q->get('i', 'cat'));

    $image = $app['imagine']->open($path('osky-logo.gif'));
    $image->layers()
        //->add($app['imagine']->open($path('osky-logo.gif')))
        ->add($app['imagine']->open($path($img)))
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
    //$image->layers()->animate('gif', 100, 0);
    $image->layers()->coalesce();

    return $image->show('gif', array(
        'animated' => true,
        'animated.loops' => 0,
        'animated.delay' => 100,
    ));
});

return $app;
