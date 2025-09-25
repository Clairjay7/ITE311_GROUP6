<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
<<<<<<< HEAD
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
=======

// Authentication routes
$routes->get('/login', 'Auth::index');
$routes->post('/login/process', 'Auth::process');
$routes->get('/logout', 'Auth::logout');
$routes->post('/logout', 'Auth::logout');

// Consolidated Dashboard routes using Auth controller
$routes->get('/auth/super-admin-dashboard', 'Auth::superAdminDashboard');
$routes->get('/auth/doctor-dashboard', 'Auth::doctorDashboard');
$routes->get('/auth/nurse-dashboard', 'Auth::nurseDashboard');
$routes->get('/auth/receptionist-dashboard', 'Auth::receptionistDashboard');
$routes->get('/auth/laboratory-dashboard', 'Auth::laboratoryDashboard');
$routes->get('/auth/pharmacist-dashboard', 'Auth::pharmacistDashboard');
$routes->get('/auth/accountant-dashboard', 'Auth::accountantDashboard');
$routes->get('/auth/it-dashboard', 'Auth::itDashboard');

// Legacy routes for backward compatibility (redirect to new Auth controller)
$routes->get('/super-admin/dashboard', 'Auth::superAdminDashboard');
$routes->get('/doctor/dashboard', 'Auth::doctorDashboard');
$routes->get('/nurse/dashboard', 'Auth::nurseDashboard');
$routes->get('/receptionist/dashboard', 'Auth::receptionistDashboard');
$routes->get('/laboratory/dashboard', 'Auth::laboratoryDashboard');
$routes->get('/pharmacist/dashboard', 'Auth::pharmacistDashboard');
$routes->get('/accountant/dashboard', 'Auth::accountantDashboard');
$routes->get('/it/dashboard', 'Auth::itDashboard');

// SuperAdmin functional routes for dashboard links
$routes->group('super-admin', ['filter' => 'auth:super_admin'], static function($routes) {
    $routes->get('users', 'SuperAdmin::users');
    $routes->get('roles', 'SuperAdmin::roles');
    $routes->get('appointments', 'SuperAdmin::appointments');
    $routes->get('calendars', 'SuperAdmin::calendars');
    $routes->get('patients', 'SuperAdmin::patients');
    $routes->get('admissions', 'SuperAdmin::admissions');
    $routes->get('doctors', 'SuperAdmin::doctors');
    $routes->get('staff', 'SuperAdmin::staff');
    $routes->get('billing', 'SuperAdmin::billing');
    $routes->get('finance/reports', 'SuperAdmin::financeReports');
    $routes->get('laboratory', 'SuperAdmin::laboratory');
    $routes->get('pharmacy', 'SuperAdmin::pharmacy');
    $routes->get('rooms', 'SuperAdmin::rooms');
    $routes->get('occupancy', 'SuperAdmin::occupancy');
    $routes->get('reports', 'SuperAdmin::reports');
    $routes->get('analytics', 'SuperAdmin::analytics');
    $routes->get('settings', 'SuperAdmin::settings');
    $routes->get('security', 'SuperAdmin::security');
});

// Doctor functional routes for dashboard links
$routes->group('doctor', ['filter' => 'auth:doctor'], static function($routes) {
    $routes->get('patients', 'Doctor::patients');
    $routes->get('appointments', 'Doctor::appointments');
    $routes->get('calendar', 'Doctor::calendar');
    $routes->get('notifications', 'Doctor::notifications');
    $routes->get('emr', 'Doctor::emr');
    $routes->get('prescriptions', 'Doctor::prescriptions');
    $routes->get('lab/requests', 'Doctor::labRequests');
    $routes->get('lab/results', 'Doctor::labResults');
    $routes->get('messaging', 'Doctor::messaging');
    $routes->get('reports', 'Doctor::reports');
    $routes->get('profile', 'Doctor::profile');
    $routes->get('settings', 'Doctor::settings');
});
>>>>>>> c95817c (Admins and doctor)
