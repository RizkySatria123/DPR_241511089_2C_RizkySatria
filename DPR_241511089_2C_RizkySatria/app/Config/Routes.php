<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
// Auth routes
$routes->get('login', 'Auth::index');
$routes->post('login', 'Auth::login');
$routes->get('logout', 'Auth::logout');

// Admin routes
$routes->get('admin', 'Admin::index');
// Backward compat if any existing links use /admin/dashboard
$routes->get('admin/dashboard', 'Admin::index');

// Anggota (Admin-only)
$routes->get('admin/anggota', 'Anggota::index');
$routes->get('admin/anggota/create', 'Anggota::create');
$routes->post('admin/anggota/store', 'Anggota::store');