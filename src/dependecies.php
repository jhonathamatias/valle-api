<?php

use Slim\Views\Twig;
use Psr\Container\ContainerInterface;

$container = new \DI\Container;

$container->set('settings', function() {
    return require __DIR__ . '/settings.php';
});

$container->set('view', function(ContainerInterface $c) {
    $settings = $c->get('settings')['renderer'];
    $view = Twig::create($settings['template_path'], [
        'cache' => $settings['cache'],
        'debug' => $settings['debug']
    ]);

    return $view;
});

$container->set('dbal', function (ContainerInterface $c) {
    $settings = $c->get('settings')['dbal'];
    return \Doctrine\DBAL\DriverManager::getConnection($settings, new \Doctrine\DBAL\Configuration());
});

$container->set('repository.factory', function (ContainerInterface $c) {
    return new \Valle\Factorys\RepositoryFactory($c->get('dbal'));
});

/**
 * Middlewares
 */
$container->set('Auth.middleware', function () {
    return new \Valle\Middlewares\AuthMiddleware;
});

$container->set('JsonBodyParser.middleware', function () {
    return new \Valle\Middlewares\JsonBodyParserMiddleware;
});

$container->set('Cors.middleware', function () {
    return new \Valle\Middlewares\CorsMiddleware;
});

$container->set('SignInVerify.middleware', function () {
    return new \Valle\Middlewares\SignInVerifyMiddleware;
});

/**
 * Controllers
 */
$container->set('Users.controller', function () {
    return new \Valle\Controllers\User;
});

$container->set('SignIn.controller', function(ContainerInterface $c) {
    return new \Valle\Controllers\SignIn($c->get('SignIn.model'));
});

$container->set('Company.controller', function (ContainerInterface $c) {
    return new \Valle\Controllers\Company($c->get('view'), $c->get('Company.model'));
});

$container->set('Users.controller', function (ContainerInterface $c) {
    return new \Valle\Controllers\Users($c->get('view'), $c->get('Users.model'));
});

$container->set('Products.controller', function (ContainerInterface $c) {
    return new \Valle\Controllers\Products($c->get('Products.model'));
});

$container->set('ServiceOrder.controller', function (ContainerInterface $c) {
    return new \Valle\Controllers\Users($c->get('view'), $c->get('ServiceOrder.model'));
});

$container->set('Collaborators.controller', function (ContainerInterface $c) {
    return new \Valle\Controllers\Collaborators($c->get('view'), $c->get('Collaborators.model'));
});

$container->set('UsersRoles.controller', function (ContainerInterface $c) {
    return new \Valle\Controllers\UsersRoles($c->get('UsersRoles.model'));
});

/**
 * Models
 */
$container->set('SignIn.model', function(ContainerInterface $c) {
    return new \Valle\Models\SignIn($c->get('repository.factory'));
});

$container->set('Users.model', function (ContainerInterface $c) {
    return new \Valle\Models\Users($c->get('repository.factory'));
});

$container->set('Products.model', function (ContainerInterface $c) {
    return new \Valle\Models\Products($c->get('repository.factory'), $c->get('File.service'));
});

/**
 * Services
 */
$container->set('File.service', function (ContainerInterface $c) {
    return new \Valle\Services\File();
});

return $container;