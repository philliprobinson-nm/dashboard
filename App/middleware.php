<?php

use Slim\App;
use App\Factory\LoggerFactory;
use Slim\Views\TwigMiddleware;
use Selective\BasePath\BasePathMiddleware;

return function (App $app) {
    $app->add(TwigMiddleware::class);
    $app->addRoutingMiddleware();
    $app->add(BasePathMiddleware::class);
    
    $loggerFactory = $app->getContainer()->get(LoggerFactory::class);
    $logger = $loggerFactory->addFileHandler('error.log')->createLogger();
    $errorMiddleware = $app->addErrorMiddleware(true, true, true, $logger);
};