<?php

use Slim\App;
use Slim\Views\Twig;
use Slim\Factory\AppFactory;
use App\Factory\LoggerFactory;
use Slim\Views\TwigMiddleware;
use Twig\Extension\DebugExtension;
use Psr\Container\ContainerInterface;
use Laminas\Config\Config;

return [
    'settings' => function () {
        return require __DIR__ . '/../settings.php';
    },
    
    App::class => function (ContainerInterface $container) {
        AppFactory::setContainer($container);

        return AppFactory::create();
    },

    Config::class => function () {
        return new Config(require __DIR__ . '/../settings.php');
    },
    
    MeekroDB::class => function (ContainerInterface $container) {
        $settings = $container->get(Config::class)->get('database');
        
        $db = new MeekroDB(
            $settings->get('host'), 
            $settings->get('user'), 
            $settings->get('pass'), 
            $settings->get('database'), 
            $settings->get('port'),
            $settings->get('encoding')
        );

        $db->usenull = false;

        return $db;
    },

    Twig::class => function (ContainerInterface $container) {
        $settings = $container->get(Config::class)->get('view');
        $twig = Twig::create(
            $settings->get('template_path'), 
            [
                'cache'=>$settings->get('twig')->cache, 
                'debug'=>$settings->get('twig')->debug
            ]);
        $twig->addExtension(new DebugExtension());

        //$twig->getEnvironment()->addGlobal('session', $_SESSION);

        return $twig;
    },

    TwigMiddleware::class => function (ContainerInterface $container) {
        return TwigMiddleware::createFromContainer(
            $container->get(App::class),
            Twig::class
        );
    },

    LoggerFactory::class => function (ContainerInterface $container) {
        $settings = $container->get(Config::class);

        return new LoggerFactory($settings->get('logger'));
    },
];