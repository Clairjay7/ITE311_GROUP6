<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('login', 'Login::index');
$routes->post('login', 'Login::login');
$routes->get('user/dashboard', 'Login::userDashboard');
$routes->get('test', function() {
    return 'Test route works!';
});
$routes->get('doctor/dashboard', 'Login::doctorDashboard');
$routes->get('nurse/dashboard', 'Login::nurseDashboard');
$routes->get('receptionist/dashboard', 'Login::receptionistDashboard');
$routes->get('laboratory/dashboard', 'Login::laboratoryDashboard');
$routes->get('pharmacist/dashboard', 'Login::pharmacistDashboard');
$routes->get('accountant/dashboard', 'Login::accountantDashboard');
$routes->get('it/dashboard', 'Login::itDashboard');
$routes->get('superadmin/dashboard', 'Login::superAdminDashboard');
$routes->get('logout', 'Login::logout');