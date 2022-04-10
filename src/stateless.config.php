<?php

use Valle\Sessions\EduOSSessionHandler;
use Psr\Container\ContainerInterface;

$container = $app->getContainer();

$container->set('session.handler', function (ContainerInterface $c) {
    return new EduOSSessionHandler($c->get('repository.factory'));
});

$sessionHandler = $container->get('session.handler');

session_set_save_handler($sessionHandler, true);
session_start();
