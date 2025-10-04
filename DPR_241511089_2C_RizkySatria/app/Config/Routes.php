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

	$routes->get('komponen-gaji', 'KomponenGajiController::index');
	$routes->get('komponen-gaji/create', 'KomponenGajiController::create');
	$routes->post('komponen-gaji', 'KomponenGajiController::store');
	$routes->get('komponen-gaji/edit/(:num)', 'KomponenGajiController::edit/$1');
	$routes->post('komponen-gaji/update/(:num)', 'KomponenGajiController::update/$1');
	$routes->post('komponen-gaji/delete/(:num)', 'KomponenGajiController::delete/$1');

	$routes->get('penggajian', 'PenggajianController::index');
	$routes->get('penggajian/anggota/(:num)', 'PenggajianController::manage/$1');
	$routes->post('penggajian/anggota/(:num)', 'PenggajianController::store/$1');
	$routes->post('penggajian/anggota/(:num)/hapus/(:num)', 'PenggajianController::delete/$1/$2');
});