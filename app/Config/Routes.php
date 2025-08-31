<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/login', 'Login::index');
$routes->post('/login/process', 'Login::process');
$routes->get('/logout', 'Login::logout');
$routes->post('/logout', 'Login::logout');

// Dashboard routes for all 8 user roles
$routes->group('super-admin', ['filter' => 'auth:super_admin'], function($routes) {
    $routes->get('dashboard', 'SuperAdmin\\Dashboard::index');
});

$routes->group('doctor', ['filter' => 'auth:doctor'], function($routes) {
    $routes->get('dashboard', 'Doctor\\Dashboard::index');
});

$routes->group('nurse', ['filter' => 'auth:nurse'], function($routes) {
    $routes->get('dashboard', 'Nurse\\Dashboard::index');
});

$routes->group('receptionist', ['filter' => 'auth:receptionist'], function($routes) {
    $routes->get('dashboard', 'Receptionist\\Dashboard::index');
});

$routes->group('laboratory', ['filter' => 'auth:laboratory_staff'], function($routes) {
    $routes->get('dashboard', 'Laboratory\\Dashboard::index');
});

$routes->group('pharmacist', ['filter' => 'auth:pharmacist'], function($routes) {
    $routes->get('dashboard', 'Pharmacist\\Dashboard::index');
});

$routes->group('accountant', ['filter' => 'auth:accountant'], function($routes) {
    $routes->get('dashboard', 'Accountant\\Dashboard::index');
});

$routes->group('it', ['filter' => 'auth:it_staff'], function($routes) {
    $routes->get('dashboard', 'ITStaff\\Dashboard::index');
});
