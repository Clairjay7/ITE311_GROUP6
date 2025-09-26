<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

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

// Admin dashboard
$routes->get('/admin/dashboard', 'Admin::dashboard');

// Nurse routes
$routes->group('nurse', ['filter' => 'auth:nurse'], static function($routes) {
    $routes->get('dashboard', 'Nurse::dashboard');
    $routes->get('patients', 'Nurse::patients');
    $routes->get('medications', 'Nurse::medications');
    $routes->get('vitals', 'Nurse::vitals');
    $routes->get('tasks', 'Nurse::tasks');
    $routes->get('orders', 'Nurse::orders');
    $routes->get('notifications', 'Nurse::notifications');
    $routes->get('roster', 'Nurse::roster');
});

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
    $routes->get('emr', 'Doctor::emr');
    $routes->get('prescriptions', 'Doctor::prescriptions');
    $routes->get('lab/requests', 'Doctor::labRequests');
    $routes->get('lab/results', 'Doctor::labResults');
    $routes->get('messaging', 'Doctor::messaging');
    $routes->get('reports', 'Doctor::reports');
    $routes->get('profile', 'Doctor::profile');
    $routes->get('settings', 'Doctor::settings');
});

// Receptionist functional routes for dashboard links
$routes->group('receptionist', ['filter' => 'auth:receptionist'], static function($routes) {
    $routes->get('patients', 'Receptionist::patients');
    $routes->get('appointments', 'Receptionist::appointments');
    $routes->get('calendar', 'Receptionist::calendar');
    $routes->get('checkins', 'Receptionist::checkins');
    $routes->get('billing', 'Receptionist::billing');
    $routes->get('visitors', 'Receptionist::visitors');
    $routes->get('notifications', 'Receptionist::notifications');
    $routes->get('settings', 'Receptionist::settings');

    // API endpoints
    // Patients
    $routes->get('api/patients/search', 'Receptionist::patientSearch');
    $routes->post('api/patients', 'Receptionist::patientStore');
    $routes->post('api/patients/(:num)', 'Receptionist::patientUpdate/$1');
    $routes->delete('api/patients/(:num)', 'Receptionist::patientDelete/$1');

    // Appointments
    $routes->post('api/appointments', 'Receptionist::appointmentStore');
    $routes->post('api/appointments/(:num)/reschedule', 'Receptionist::appointmentReschedule/$1');
    $routes->post('api/appointments/(:num)/cancel', 'Receptionist::appointmentCancel/$1');

    // Doctor availability
    $routes->get('api/doctors/availability', 'Receptionist::doctorAvailability');

    // Check-ins
    $routes->post('api/checkins', 'Receptionist::checkinMark');
    $routes->post('api/checkins/(:num)/checkout', 'Receptionist::checkoutMark/$1');

    // Billing
    $routes->post('api/billing', 'Receptionist::billingStore');
    $routes->post('api/billing/(:num)/paid', 'Receptionist::billingMarkPaid/$1');

    // Visitors
    $routes->post('api/visitors', 'Receptionist::visitorStore');
    $routes->post('api/visitors/(:num)/checkout', 'Receptionist::visitorCheckout/$1');
});