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

// Test routes for debugging
$routes->get('/test-db', 'TestAuth::testDatabase');
$routes->get('/create-test-user', 'TestAuth::createTestUser');
$routes->get('/analyze-db', 'DatabaseAnalyzer::analyzeDatabase');
$routes->get('/debug', 'Debug::index');
$routes->get('/phpinfo', 'Debug::phpinfo');
$routes->get('/test-super-admin', function() {
    echo "<h1>✅ SuperAdmin Controller Test</h1>";
    echo "<p>If you see this, the SuperAdmin controller is working!</p>";
    echo "<p><a href='" . base_url('super-admin/unified') . "'>Go to SuperAdmin Dashboard</a></p>";
});
$routes->get('/test', 'TestController::index');
$routes->get('/simple-test', 'SimpleTest::index');
$routes->get('/simple-test/dashboard', 'SimpleTest::dashboard');
$routes->get('/simple-test/database', 'SimpleTest::database');
$routes->get('/simple-test/session', 'SimpleTest::session');
$routes->get('/quick-test-users', 'QuickTest::users');
$routes->get('/quick-test-api', 'QuickTest::apiTest');
// Dashboard API routes
$routes->get('/api/dashboard/users', 'DashboardAPI::users');
$routes->get('/api/dashboard/patients', 'DashboardAPI::patients');
$routes->get('/api/dashboard/appointments', 'DashboardAPI::appointments');

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
    $routes->get('pharmacy-settings', 'SuperAdmin\PharmacySettingsController::index');
    $routes->post('pharmacy-settings/update', 'SuperAdmin\PharmacySettingsController::update');
    $routes->get('rooms', 'SuperAdmin::rooms');
    $routes->get('occupancy', 'SuperAdmin::occupancy');
    $routes->get('reports', 'SuperAdmin\ReportsController::index');
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

// Admin routes
$routes->group('admin', static function($routes) {
    $routes->get('dashboard', 'Admin::dashboard');
    $routes->get('prescriptions', 'Admin\PrescriptionController::index');
    $routes->post('prescriptions/update/(:num)', 'Admin\PrescriptionController::updateStatus/$1');
});

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
$routes->group('super-admin', ['filter' => 'auth:superadmin'], static function($routes) {
    // Dashboard
    $routes->get('dashboard', 'SuperAdmin::dashboard');
    $routes->get('unified', 'SuperAdmin::unifiedDashboard');
    $routes->get('/', 'SuperAdmin::unifiedDashboard'); // Default to unified dashboard

    $routes->get('inventory', 'SuperAdmin\InventoryController::index');
    $routes->post('inventory/update/(:num)', 'SuperAdmin\InventoryController::updateStock/$1');

    $routes->get('reports', 'SuperAdmin\ReportsController::index');
    $routes->post('reports/generate', 'SuperAdmin\ReportsController::generate');
    
    // User Management
    $routes->get('users', 'SuperAdmin::users');
    $routes->get('users/add', 'SuperAdmin::addUser');
    $routes->post('users/add', 'SuperAdmin::addUser');
    $routes->get('users/edit/(:num)', 'SuperAdmin::editUser/$1');
    $routes->post('users/edit/(:num)', 'SuperAdmin::editUser/$1');
    $routes->delete('users/(:num)', 'SuperAdmin::deleteUser/$1');
    $routes->get('users/view/(:num)', 'SuperAdmin::viewUser/$1');
    
    // Department Management
    $routes->get('departments', 'SuperAdmin::departments');
    $routes->get('departments/add', 'SuperAdmin::addDepartment');
    $routes->post('departments/add', 'SuperAdmin::addDepartment');
    $routes->get('departments/edit/(:num)', 'SuperAdmin::editDepartment/$1');
    $routes->post('departments/edit/(:num)', 'SuperAdmin::editDepartment/$1');
    $routes->delete('departments/(:num)', 'SuperAdmin::deleteDepartment/$1');
    
    // Roles & Permissions
    $routes->get('roles', 'SuperAdmin::roles');
    $routes->get('roles/view/(:num)', 'SuperAdmin::viewRole/$1');
    $routes->get('roles/edit/(:num)', 'SuperAdmin::editRole/$1');
    $routes->post('roles/edit/(:num)', 'SuperAdmin::editRole/$1');
    $routes->get('roles/add', 'SuperAdmin::addRole');
    $routes->post('roles/add', 'SuperAdmin::addRole');
    
    // Appointments Management
    $routes->get('appointments', 'SuperAdmin::appointments');
    $routes->get('appointments/migrate', 'SuperAdmin::runAppointmentMigration');
    $routes->get('appointments/fix', 'SuperAdmin::fixAppointmentTable');
    $routes->get('appointments/test', 'SuperAdmin::testAppointmentDB');
    $routes->get('appointments/add', 'SuperAdmin::addAppointment');
    $routes->post('appointments/add', 'SuperAdmin::addAppointment');
    $routes->get('appointments/view/(:num)', 'SuperAdmin::viewAppointment/$1');
    $routes->get('appointments/edit/(:num)', 'SuperAdmin::editAppointment/$1');
    $routes->post('appointments/edit/(:num)', 'SuperAdmin::editAppointment/$1');
    $routes->post('appointments/reschedule/(:num)', 'SuperAdmin::rescheduleAppointment/$1');
    $routes->post('appointments/cancel/(:num)', 'SuperAdmin::cancelAppointment/$1');
    
    // Calendars
    $routes->get('calendars', 'SuperAdmin::calendars');
    $routes->get('calendars/data', 'SuperAdmin::getCalendarData');
    
    // Finance
    $routes->get('finance/reports', 'SuperAdmin::financeReports');
    
    // Laboratory
    $routes->get('laboratory', 'SuperAdmin::laboratory');
    
    // Pharmacy
    $routes->get('pharmacy', 'SuperAdmin::pharmacy');
    $routes->get('tests', 'SuperAdmin\TestManagementController::index');
    $routes->post('tests/add', 'SuperAdmin\TestManagementController::store');
    $routes->post('tests/update/(:num)', 'SuperAdmin\TestManagementController::update/$1');
    $routes->get('equipment', 'SuperAdmin\EquipmentController::index');
    $routes->post('equipment/add', 'SuperAdmin\EquipmentController::store');
    $routes->post('equipment/update/(:num)', 'SuperAdmin\EquipmentController::update/$1');
    $routes->get('lab-reports', 'SuperAdmin\LabReportsController::index');
    $routes->post('lab-reports/generate', 'SuperAdmin\LabReportsController::generate');
    $routes->post('lab-reports/generate/(:segment)', 'SuperAdmin\LabReportsController::generate/$1');
    $routes->get('laboratory-settings', 'SuperAdmin\LaboratorySettingsController::index');
    $routes->post('laboratory-settings/update', 'SuperAdmin\LaboratorySettingsController::update');

    // Room Management
    $routes->get('rooms', 'SuperAdmin::rooms');
    $routes->get('rooms/add', 'SuperAdmin::addRoom');
    $routes->post('rooms/add', 'SuperAdmin::addRoom');
    $routes->get('rooms/edit/(:num)', 'SuperAdmin::editRoom/$1');
    $routes->delete('rooms/(:num)', 'SuperAdmin::deleteRoom/$1');
    
    //Occupancy
    $routes->get('occupancy', 'SuperAdmin::occupancy');
    
    // Patient Records Management
    $routes->get('patients', 'SuperAdmin::patients');
    $routes->get('patients/add', 'SuperAdmin::addPatient');
    $routes->post('patients/add', 'SuperAdmin::addPatient');
    $routes->get('patients/view/(:num)', 'SuperAdmin::viewPatient/$1');
    $routes->get('patients/edit/(:num)', 'SuperAdmin::editPatient/$1');
    $routes->post('patients/edit/(:num)', 'SuperAdmin::editPatient/$1');
    $routes->delete('patients/(:num)', 'SuperAdmin::deletePatient/$1');
    $routes->post('patients/delete/(:num)', 'SuperAdmin::deletePatient/$1');
    $routes->get('patients/debug', 'SuperAdmin::debugPatients');
    $routes->get('patients/fix-table', 'SuperAdmin::fixPatientsTable');
    $routes->get('patients/emergency-fix', 'SuperAdmin::emergencyFixPatients');
    $routes->get('patients/recreate-table', 'SuperAdmin::recreatePatientsTable');
    $routes->get('patients/clear-samples', 'SuperAdmin::clearSamplePatients');
    $routes->get('patients/force-fix', 'SuperAdmin::forceFixPatients');
    $routes->get('patients/run-migration', 'SuperAdmin::runPatientsMigration');
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
    $routes->get('api/users/(:num)', 'SuperAdmin::apiUsers/$1');
    $routes->get('api/patients', 'SuperAdmin::apiPatients');
    $routes->get('api/appointments', 'SuperAdmin::apiAppointments');
    $routes->get('api/rooms', 'SuperAdmin::apiRooms');
    $routes->get('api/stats', 'SuperAdmin::apiStats');
    $routes->get('api/test', 'SuperAdmin::testApi');
    $routes->get('api/simple-users', 'SuperAdmin::simpleUsers');
    $routes->get('debug-users', 'SuperAdmin::debugUsers');
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
$routes->group('laboratory', ['filter' => 'auth:laboratory'], static function($routes) {
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

// Medical Services routes (accessible by multiple roles)
$routes->group('medical-services', ['filter' => 'auth'], static function($routes) {
    $routes->get('/', 'MedicalServices::index');
    $routes->get('create', 'MedicalServices::create');
    $routes->post('store', 'MedicalServices::store');
    $routes->get('edit/(:num)', 'MedicalServices::edit/$1');
    $routes->post('update/(:num)', 'MedicalServices::update/$1');
    $routes->delete('delete/(:num)', 'MedicalServices::delete/$1');
    
    // API endpoints
    $routes->get('api/services', 'MedicalServices::apiServices');
    $routes->get('api/service/(:num)', 'MedicalServices::apiServiceDetails/$1');
    $routes->get('api/billing', 'MedicalServices::apiServicesForBilling');
    $routes->post('api/status/(:num)', 'MedicalServices::updateStatus/$1');
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