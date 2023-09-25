<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/main', 'MainController::main');
$routes->post('/createPlaylist', 'MainController::createPlaylist');
$routes->get('/editPlaylist/(:any)', 'MusicController::editPlaylist/$1');
$routes->get('/deletePlaylist/(:any)', 'MusicController::deletePlaylist/$1');

