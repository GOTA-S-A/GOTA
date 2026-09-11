<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::attemptLogin');
$routes->get('/logout', 'Auth::logout');
$routes->post('/logout', 'Auth::logout');


// Dashboard route with authentication filter
$routes->get('/dashboard', 'Dashboard::index', ['filter' => 'auth']);

// Administración de usuarios y roles
$routes->group('usuarios', ['filter' => 'auth,role:Administrador,Desarrollador'], function ($routes) {
    $routes->get('/', 'UsuariosController::index');
    $routes->get('nuevo', 'UsuariosController::nuevo');
    $routes->post('crear', 'UsuariosController::crear');
    $routes->get('editar/(:num)', 'UsuariosController::editar/$1');
    $routes->post('actualizar/(:num)', 'UsuariosController::actualizar/$1');
});

// Rutas del modulo de Clientes
$routes->group('clientes', ['filter' => 'auth,role:Administrador,Desarrollador,Secretaria'], function ($routes) {
    $routes->get('/', 'ClientesController::index');
    $routes->get('nuevo', 'ClientesController::nuevo');
    $routes->post('crear', 'ClientesController::crear');
    $routes->get('editar/(:num)', 'ClientesController::editar/$1');
    $routes->post('actualizar/(:num)', 'ClientesController::actualizar/$1');
    $routes->post('desactivar/(:num)', 'ClientesController::desactivar/$1');
    $routes->post('activar/(:num)', 'ClientesController::activar/$1');
});

// Rutas del modulo de Contadores
$routes->get('/contadores', 'ContadoresController::index', ['filter' => 'auth,role:Administrador,Desarrollador,Lector']);
$routes->get('/contadores/nuevo', 'ContadoresController::nuevo', ['filter' => 'auth,role:Administrador,Desarrollador']);
$routes->post('/contadores/crear', 'ContadoresController::crear', ['filter' => 'auth,role:Administrador,Desarrollador']);
$routes->get('/contadores/editar/(:num)', 'ContadoresController::editar/$1', ['filter' => 'auth,role:Administrador,Desarrollador']);
$routes->post('/contadores/actualizar/(:num)', 'ContadoresController::actualizar/$1', ['filter' => 'auth,role:Administrador,Desarrollador']);
$routes->post('/contadores/desactivar/(:num)', 'ContadoresController::desactivar/$1', ['filter' => 'auth,role:Administrador,Desarrollador']);
$routes->post('/contadores/activar/(:num)', 'ContadoresController::activar/$1', ['filter' => 'auth,role:Administrador,Desarrollador']);

// Rutas del modulo de Lecturas + Recibo
$routes->get('/lecturas/nueva/(:num)', 'Lecturas::nueva/$1', ['filter' => 'auth,role:Administrador,Desarrollador,Lector']);
$routes->post('/lecturas/guardar', 'Lecturas::guardar', ['filter' => 'auth,role:Administrador,Desarrollador,Lector']);
$routes->get('/lecturas/recibo/(:num)', 'Lecturas::recibo/$1', ['filter' => 'auth,role:Administrador,Desarrollador,Lector,Secretaria']);

// Rutas del modulo de Tipos de Servicio
$routes->group('tipos-servicio', ['filter' => 'auth,role:Administrador,Desarrollador'], function ($routes) {
    $routes->get('/', 'TiposServicio::index');
    $routes->get('nuevo', 'TiposServicio::nuevo');
    $routes->post('crear', 'TiposServicio::crear');
    $routes->get('editar/(:num)', 'TiposServicio::editar/$1');
    $routes->post('actualizar/(:num)', 'TiposServicio::actualizar/$1');
    $routes->post('desactivar/(:num)', 'TiposServicio::desactivar/$1');
    $routes->post('activar/(:num)', 'TiposServicio::activar/$1');
});

// CRUD de Tarifas
$routes->group('tarifas', ['filter' => 'auth,role:Administrador,Desarrollador'], function ($routes) {
    $routes->get('/', 'Tarifas::index');
    $routes->get('nuevo', 'Tarifas::nuevo');
    $routes->post('crear', 'Tarifas::crear');
    $routes->get('editar/(:num)', 'Tarifas::editar/$1');
    $routes->post('actualizar/(:num)', 'Tarifas::actualizar/$1');
    $routes->post('desactivar/(:num)', 'Tarifas::desactivar/$1');
});

// CRUD de Pagos
$routes->group('pagos', ['filter' => 'auth,role:Administrador,Desarrollador,Secretaria'], function ($routes) {
    $routes->get('/', 'Pagos::index');
    $routes->get('pendientes', 'Pagos::pendientes');
    $routes->get('registrar/(:num)', 'Pagos::registrar/$1');
    $routes->post('guardar/(:num)', 'Pagos::guardar/$1');
    $routes->post('anular/(:num)', 'Pagos::anular/$1');
});

$routes->get('/', 'Auth::login');