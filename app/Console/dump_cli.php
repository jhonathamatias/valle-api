<?php

use Slim\Factory\AppFactory;

// Use the application settings
require dirname(__DIR__, 2) . '/src/config.php';

$container = require dirname(__DIR__, 2) . '/src/dependecies.php';

// Instantiate the application
AppFactory::setContainer($container);

$app = AppFactory::create();

require dirname(__DIR__, 2) . '/src/stateless.config.php';   

// Set up dependencies

$repository = $container->get('repository.factory');
