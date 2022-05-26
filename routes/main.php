<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;
use Slim\Routing\RouteCollectorProxy;

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add('Cors.middleware');

$app->get('/', function (Request $request, Response $response) {
    return $response->withHeader('Location', '/signin');
});

$app->get('/signin', function (Request $request, Response $response) {
    session_destroy();

    $renderer = $this->get('view');

    return $renderer->render($response, 'sign_in.twig');
});

/**
 * Login backoffice
 */
$app->post('/signin/auth', 'SignIn.controller:doSignIn');

$app->get('/logout', 'SignIn.controller:logout');

/**
 * Screens render
 */
$app->get('/users', 'Users.controller:render')->add("SignInVerify.middleware");
$app->get('/company', 'Company.controller:render')->add("SignInVerify.middleware");
$app->get('/collaborators', 'Collaborators.controller:render')->add("SignInVerify.middleware");

/**
 * API routes
 */
$app->group('/api/v1', function (RouteCollectorProxy $group) {
    $api = require __DIR__ . '/api.php';
    $api($group);
})
    ->add('Auth.middleware')
    ->add('JsonBodyParser.middleware');


$app->post('/api/v1/auth', 'SignIn.controller:doSignIn')
    ->add('JsonBodyParser.middleware');

$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function ($request, $response) {
    throw new HttpNotFoundException($request);
});