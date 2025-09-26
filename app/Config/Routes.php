<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/homepage', 'Home::index');

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

// SuperAdmin Management Routes
$routes->group('super-admin', function($routes) {
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

// Laboratory Management Routes
$routes->group('laboratory', function($routes) {
    $routes->get('tests', 'Laboratory::tests');
    $routes->get('pending', 'Laboratory::pending');
    $routes->get('results', 'Laboratory::results');
    $routes->get('reports', 'Laboratory::reports');
    $routes->get('dashboard', 'Auth::laboratoryDashboard');
});

// Legacy routes for backward compatibility (redirect to new Auth controller)
$routes->get('/super-admin/dashboard', 'Auth::superAdminDashboard');
$routes->get('/doctor/dashboard', 'Auth::doctorDashboard');
$routes->get('/nurse/dashboard', 'Auth::nurseDashboard');
$routes->get('/receptionist/dashboard', 'Auth::receptionistDashboard');
$routes->get('/laboratory/dashboard', 'Laboratory::dashboard');
$routes->get('/pharmacist/dashboard', 'Pharmacist::dashboard');
$routes->get('/accountant/dashboard', 'Accountant::dashboard');
$routes->get('/it/dashboard', 'ITStaff::dashboard');

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
    // Dashboard
    $routes->get('dashboard', 'SuperAdmin::dashboard');
    
    // User Management
    $routes->get('users', 'SuperAdmin::users');
    $routes->get('users/add', 'SuperAdmin::addUser');
    $routes->post('users/add', 'SuperAdmin::addUser');
    $routes->get('users/edit/(:num)', 'SuperAdmin::editUser/$1');
    $routes->post('users/edit/(:num)', 'SuperAdmin::editUser/$1');
    $routes->delete('users/(:num)', 'SuperAdmin::deleteUser/$1');
    $routes->get('users/view/(:num)', 'SuperAdmin::viewUser/$1');
    
    // Roles & Permissions
    $routes->get('roles', 'SuperAdmin::roles');
    
    // Appointments
    $routes->get('appointments', 'SuperAdmin::appointments');
    
    // Calendars
    $routes->get('calendars', 'SuperAdmin::calendars');
    
    // Finance
    $routes->get('finance/reports', 'SuperAdmin::financeReports');
    
    // Laboratory
    $routes->get('laboratory', 'SuperAdmin::laboratory');
    
    // Pharmacy
    $routes->get('pharmacy', 'SuperAdmin::pharmacy');
    
    // Room Management
    $routes->get('rooms', 'SuperAdmin::rooms');
    $routes->get('rooms/add', 'SuperAdmin::addRoom');
    $routes->post('rooms/add', 'SuperAdmin::addRoom');
    $routes->get('rooms/edit/(:num)', 'SuperAdmin::editRoom/$1');
    $routes->post('rooms/edit/(:num)', 'SuperAdmin::editRoom/$1');
    $routes->delete('rooms/(:num)', 'SuperAdmin::deleteRoom/$1');
    
    //Occupancy
    $routes->get('occupancy', 'SuperAdmin::occupancy');
    
    // Patient Management
    $routes->get('patients', 'SuperAdmin::patients');
    $routes->get('admissions', 'SuperAdmin::admissions');
    
    // Doctor & Staff Management
    $routes->get('doctors', 'SuperAdmin::doctors');
    $routes->get('staff', 'SuperAdmin::staff');
    
    // Billing & Finance
    $routes->get('billing', 'SuperAdmin::billing');
    
    // Reports
    $routes->get('reports', 'SuperAdmin::reports');
    
    // Analytics
    $routes->get('analytics', 'SuperAdmin::analytics');
    
    // Settings
    $routes->get('settings', 'SuperAdmin::settings');
    
    // Security
    $routes->get('security', 'SuperAdmin::security');
    
    // Audit Logs
    $routes->get('audit-logs', 'SuperAdmin::auditLogs');
    
    // API Endpoints
    $routes->get('api/users', 'SuperAdmin::apiUsers');
    $routes->get('api/rooms', 'SuperAdmin::apiRooms');
    $routes->get('api/stats', 'SuperAdmin::apiStats');
});

// Doctor functional routes for dashboard links
$routes->group('doctor', ['filter' => 'auth:doctor'], static function($routes) {
    $routes->get('dashboard', 'Doctor::dashboard');
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

// Laboratory functional routes for dashboard links
$routes->group('laboratory', ['filter' => 'auth:laboratory_staff'], static function($routes) {
    $routes->get('dashboard', 'Laboratory::dashboard');
    $routes->get('tests', 'Laboratory::tests');
    $routes->get('pending', 'Laboratory::pending');
    $routes->get('in-progress', 'Laboratory::inProgress');
    $routes->get('completed', 'Laboratory::completed');
    $routes->get('results', 'Laboratory::results');
    $routes->get('equipment', 'Laboratory::equipment');
    $routes->get('inventory', 'Laboratory::inventory');
    $routes->get('reports', 'Laboratory::reports');
    $routes->get('quality', 'Laboratory::quality');
    $routes->get('settings', 'Laboratory::settings');
    
    // API endpoints
    $routes->post('api/update-status', 'Laboratory::updateTestStatus');
    $routes->get('api/stats', 'Laboratory::apiStats');
});

// Pharmacist functional routes for dashboard links
$routes->group('pharmacist', ['filter' => 'auth:pharmacist'], static function($routes) {
    $routes->get('dashboard', 'Pharmacist::dashboard');
    $routes->get('prescriptions', 'Pharmacist::prescriptions');
    $routes->get('pending', 'Pharmacist::pending');
    $routes->get('dispensed', 'Pharmacist::dispensed');
    $routes->get('inventory', 'Pharmacist::inventory');
    $routes->get('low-stock', 'Pharmacist::lowStock');
    $routes->get('expiring', 'Pharmacist::expiring');
    $routes->get('orders', 'Pharmacist::orders');
    $routes->get('reports', 'Pharmacist::reports');
    $routes->get('settings', 'Pharmacist::settings');
    
    // API endpoints
    $routes->post('api/dispense', 'Pharmacist::dispensePrescription');
    $routes->get('api/stats', 'Pharmacist::apiStats');
});

// Accountant functional routes for dashboard links
$routes->group('accountant', ['filter' => 'auth:accountant'], static function($routes) {
    $routes->get('dashboard', 'Accountant::dashboard');
    $routes->get('billing', 'Accountant::billing');
    $routes->get('payments', 'Accountant::payments');
    $routes->get('invoices', 'Accountant::invoices');
    $routes->get('reports', 'Accountant::reports');
    $routes->get('expenses', 'Accountant::expenses');
    $routes->get('revenue', 'Accountant::revenue');
    $routes->get('taxes', 'Accountant::taxes');
    $routes->get('settings', 'Accountant::settings');
    
    // API endpoints
    $routes->get('api/stats', 'Accountant::apiStats');
});

// IT Staff functional routes for dashboard links
$routes->group('it', ['filter' => 'auth:it_staff'], static function($routes) {
    $routes->get('dashboard', 'ITStaff::dashboard');
    $routes->get('users', 'ITStaff::users');
    $routes->get('systems', 'ITStaff::systems');
    $routes->get('security', 'ITStaff::security');
    $routes->get('backups', 'ITStaff::backups');
    $routes->get('monitoring', 'ITStaff::monitoring');
    $routes->get('maintenance', 'ITStaff::maintenance');
    $routes->get('logs', 'ITStaff::logs');
    $routes->get('settings', 'ITStaff::settings');
    
    // API endpoints
    $routes->get('api/stats', 'ITStaff::apiStats');
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