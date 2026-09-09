<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::attemptLogin');
$routes->get('/logout', 'Auth::logout');
$routes->post('/logout', 'Auth::logout');


// Dashboard route with authentication filter
$routes->get('/dashboard', 'Dashboard::index', ['filter' => 'auth']);

// Rutas del modulo de Lecturas + Recibo
$routes->get('/lecturas/nueva/(:num)', 'Lecturas::nueva/$1', ['filter' => 'auth']);
$routes->post('/lecturas/guardar', 'Lecturas::guardar', ['filter' => 'auth']);
$routes->get('/lecturas/recibo/(:num)', 'Lecturas::recibo/$1', ['filter' => 'auth']);

// Rutas del modulo de Tipos de Servicio
$routes->get('/tipos-servicio', 'TiposServicio::index', ['filter' => 'auth']);
$routes->get('/tipos-servicio/nuevo', 'TiposServicio::nuevo', ['filter' => 'auth']);
$routes->post('/tipos-servicio/crear', 'TiposServicio::crear', ['filter' => 'auth']);
$routes->get('/tipos-servicio/editar/(:num)', 'TiposServicio::editar/$1', ['filter' => 'auth']);
$routes->post('/tipos-servicio/actualizar/(:num)', 'TiposServicio::actualizar/$1', ['filter' => 'auth']);
$routes->post('/tipos-servicio/desactivar/(:num)', 'TiposServicio::desactivar/$1', ['filter' => 'auth']);
$routes->post('/tipos-servicio/activar/(:num)', 'TiposServicio::activar/$1', ['filter' => 'auth']);

$routes->get('/', 'Home::index');
