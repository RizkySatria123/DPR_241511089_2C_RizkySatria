<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Auth
$routes->get('login', 'Auth::index');
$routes->post('login', 'Auth::login');
$routes->post('logout', 'Auth::logout');

$routes->group('admin', ['filter' => 'auth'], static function ($routes) {
	$routes->get('/', 'Admin::index');
	$routes->get('dashboard', 'Admin::index');

	$routes->get('anggota', 'AnggotaController::index');
	$routes->get('anggota/create', 'AnggotaController::create');
	$routes->post('anggota', 'AnggotaController::store');
	$routes->get('anggota/edit/(:num)', 'AnggotaController::edit/$1');
	$routes->post('anggota/update/(:num)', 'AnggotaController::update/$1');
	$routes->post('anggota/delete/(:num)', 'AnggotaController::delete/$1');
});