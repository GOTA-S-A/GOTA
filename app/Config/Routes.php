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

$routes->get('/', 'Home::index');
