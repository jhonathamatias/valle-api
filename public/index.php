<?php

use Slim\Factory\AppFactory;
use Slim\Views\TwigMiddleware;
use Slim\Middleware\OutputBufferingMiddleware;

require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/../src/config.php'; 

$container = require __DIR__ . '/../src/dependecies.php'; 

AppFactory::setContainer($container);

$app = AppFactory::create();

require __DIR__ . '/../src/stateless.config.php';

$app->addBodyParsingMiddleware();

$app->add(TwigMiddleware::createFromContainer($app));

$app->add('Cors.middleware');

$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

require __DIR__ . '/../routes/main.php';
require __DIR__ . '/../routes/api.php';

$app->run();