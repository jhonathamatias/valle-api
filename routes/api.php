<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (\Slim\Routing\RouteCollectorProxy $group) {
    /**
     * Users routes
     */
    $group->post('/users', 'Users.controller:create');
    $group->get('/users/{id}', 'Users.controller:get');
    $group->get('/users', 'Users.controller:search');

    /**
     * Users routes
     */
    $group->post('/products', 'Products.controller:create');
    $group->get('/products', 'Products.controller:search');

    /**
     * Services routes
     */
    $group->post('/services', 'Services.controller:create');
    $group->get('/services', 'Services.controller:search');
    $group->get('/services/{id}', 'Services.controller:findOne');

    $group->post('/service_order', 'Service.controller:create');
    
    /**
     * Company routes
     */
    $group->post('/company', 'Company.controller:create');
    $group->get('/comapny', 'Company.controller:search');

    /**
     * Collaborators routes
     */
    $group->post('/collaborators', 'Collaborators.controller:create');
    $group->get('/collaborators', 'Collaborators.controller:search');
    $group->get('/collaborators/occupation', 'Collaborators.controller:getOccupation');

    /**
     * Users Roles routes
     */
    $group->get('/users/roles/{user_id}', 'UsersRoles.controller:roles');
};