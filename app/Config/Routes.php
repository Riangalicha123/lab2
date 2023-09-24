<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('test', 'MainController::test');
$routes->get('delete/(:any)', 'MainController::delete/$1');
$routes->post('save', 'MainController::save');
$routes->get('update/(:any)', 'MainController::update/$1');
//$routes->post('updates', 'MainController::updates');



