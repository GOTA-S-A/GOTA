<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::attemptLogin');
$routes->get('/logout', 'Auth::logout');
$routes->post('/logout', 'Auth::logout');


// Dashboard route with authentication filter
$routes->get('/dashboard', 'Dashboard::index', ['filter' => 'auth']);

// CRUD de Tarifas
$routes->group('tarifas', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Tarifas::index');
    $routes->get('nuevo', 'Tarifas::nuevo');
    $routes->post('crear', 'Tarifas::crear');
    $routes->get('editar/(:num)', 'Tarifas::editar/$1');
    $routes->post('actualizar/(:num)', 'Tarifas::actualizar/$1');
    $routes->post('desactivar/(:num)', 'Tarifas::desactivar/$1');
});

// CRUD de Pagos
$routes->group('pagos', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Pagos::index');
    $routes->get('pendientes', 'Pagos::pendientes');
    $routes->get('registrar/(:num)', 'Pagos::registrar/$1');
    $routes->post('guardar/(:num)', 'Pagos::guardar/$1');
    $routes->post('anular/(:num)', 'Pagos::anular/$1');
});

$routes->get('/', 'Home::index');
