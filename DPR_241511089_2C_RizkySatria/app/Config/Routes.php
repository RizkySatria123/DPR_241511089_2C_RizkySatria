<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Auth
$routes->get('login', 'Auth::index');
$routes->post('login', 'Auth::login');
$routes->post('/logout', 'Auth::logout');

// Admin
$routes->get('/admin', 'Admin::index', ['filter' => 'auth']);
$routes->get('/admin/dashboard', 'Admin::index', ['filter' => 'auth']);

// Anggota (Admin-only)
$routes->get('/admin/anggota', 'Anggota::index', ['filter' => 'auth']);
$routes->get('/admin/anggota/create', 'Anggota::create', ['filter' => 'auth']);
$routes->post('/admin/anggota/store', 'Anggota::store', ['filter' => 'auth']);