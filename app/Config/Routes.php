<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// API ROUTES
$routes->resource('employee', ['controller' => 'EmployeeController']);
$routes->resource('user', ['controller' => 'UserController']);
