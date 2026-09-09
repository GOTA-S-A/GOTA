<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::attemptLogin');
$routes->get('/logout', 'Auth::logout');
$routes->post('/logout', 'Auth::logout');


// Dashboard route with authentication filter
$routes->get('/dashboard', 'Dashboard::index', ['filter' => 'auth']);

// Rutas del modulo de Clientes
$routes->get('/clientes', 'ClientesController::index', ['filter' => 'auth']);
$routes->get('/clientes/nuevo', 'ClientesController::nuevo', ['filter' => 'auth']);
$routes->post('/clientes/crear', 'ClientesController::crear', ['filter' => 'auth']);
$routes->get('/clientes/editar/(:num)', 'ClientesController::editar/$1', ['filter' => 'auth']);
$routes->post('/clientes/actualizar/(:num)', 'ClientesController::actualizar/$1', ['filter' => 'auth']);
$routes->post('/clientes/desactivar/(:num)', 'ClientesController::desactivar/$1', ['filter' => 'auth']);
$routes->get('/', 'Home::index');
