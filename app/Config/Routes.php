<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// API ROUTES
$routes->group('api', function ($routes) {
    $routes->post("login", "AuthController::doLogin");
});

$routes->group('api', ['filter' => 'authFilter'], function ($routes) {
    $routes->resource('employees', ['controller' => 'EmployeeController']);
    $routes->resource('users', ['controller' => 'UserController']);
});
