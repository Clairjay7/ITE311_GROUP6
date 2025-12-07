<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Mga ruta para sa Home controller
// Pangunahing mga ruta
$routes->get('/', 'Auth::login');
$routes->get('home', 'Home::index');
$routes->get('services', 'Home::services');
$routes->get('doctors', 'Home::doctors');
$routes->get('contact', 'Home::contact');

// Mga ruta para sa authentication
$routes->get('login', 'Auth::login');
$routes->post('auth/process_login', 'Auth::process_login');
$routes->get('register', 'Auth::register');
$routes->post('auth/process_register', 'Auth::process_register');
$routes->get('auth/logout', 'Auth::logout');

// Tinatanggal ang index.php sa URL
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

// Mga ruta base sa role
// Tanging mga naka-login na user lang ang makakakita nito (pinag-isang dashboard)
$routes->get('/dashboard', 'Dashboard::index', ['filter' => 'auth']);
// Mga dashboard per role para mas madali, gamit pa rin ang unified dashboard
$routes->get('doctor/dashboard', 'Dashboard::index', ['filter' => 'auth:doctor,admin']);
$routes->get('nurse/dashboard', 'Nurse\DashboardController::index', ['filter' => 'auth:nurse,admin']);
$routes->get('receptionist/dashboard', 'Dashboard::index', ['filter' => 'auth:receptionist,admin']);
$routes->get('patient/dashboard', 'Dashboard::index', ['filter' => 'auth:patient']);
$routes->get('accounting/dashboard', 'Dashboard::index', ['filter' => 'auth:finance,admin']);
$routes->get('itstaff/dashboard', 'Dashboard::index', ['filter' => 'auth:itstaff,admin']);
// Lab Staff Routes
$routes->group('labstaff', ['filter' => 'auth:labstaff,lab_staff,admin,finance'], function($routes) {
    $routes->get('dashboard', 'LabStaff\LabStaffController::dashboard');
    $routes->get('test-requests', 'LabStaff\LabStaffController::testRequests');
    $routes->get('pending-specimens', 'LabStaff\LabStaffController::pendingSpecimens');
    $routes->get('completed-tests', 'LabStaff\LabStaffController::completedTests');
    $routes->get('print-result/(:num)', 'LabStaff\LabStaffController::printResult/$1', ['as' => 'labstaff_print_result']);
    $routes->post('test-requests/mark-collected/(:num)', 'LabStaff\LabStaffController::markCollected/$1');
    $routes->post('test-requests/mark-completed/(:num)', 'LabStaff\LabStaffController::markCompleted/$1');
    $routes->post('test-requests/process-payment/(:num)', 'LabStaff\LabStaffController::processPayment/$1');
    $routes->get('test-requests/receipt/(:num)', 'LabStaff\LabStaffController::printReceipt/$1');
    $routes->post('pending-specimens/mark-collected/(:num)', 'LabStaff\LabStaffController::markCollected/$1');
    $routes->post('pending-specimens/mark-completed/(:num)', 'LabStaff\LabStaffController::markCompleted/$1');
    $routes->post('pending-specimens/process-payment/(:num)', 'LabStaff\LabStaffController::processPayment/$1');
    $routes->get('pending-specimens/receipt/(:num)', 'LabStaff\LabStaffController::printReceipt/$1');
    $routes->get('logout', 'LabStaff\LabStaffController::logout');
    $routes->get('labstaff/logout', 'LabStaff\LabStaffController::logout');
});
$routes->get('pharmacy/dashboard', 'Pharmacy\PharmacyController::index', ['filter' => 'auth:pharmacy,admin']);

// Para sa backward compatibility: lumang URL ng admin dashboard (iba sa unified dashboard)
$routes->get('/admin/dashboard', 'Admin\DashboardController::index', ['filter' => 'auth:admin']);
$routes->get('/admin/dashboard/stats', 'Admin\DashboardStats::stats', ['filter' => 'auth:admin']);

// Mga admin management page (hindi mga dashboard)
$routes->get('admin/Administration/ManageUser', 'Admin::manageUsers', ['filter' => 'auth:admin']);

// New Admin Module Routes
$routes->group('admin', ['namespace' => 'App\\Controllers\\Admin', 'filter' => 'auth:admin'], function($routes) {
    // Dashboard
    $routes->get('dashboard', 'DashboardController::index');
    
    // Patients
    $routes->group('patients', function($routes) {
        $routes->get('/', 'PatientController::index');
        $routes->get('register', 'PatientController::register');
        $routes->get('outpatient', 'PatientController::outpatient');
        $routes->get('create', 'PatientController::create'); // Keep for backward compatibility
        $routes->post('store', 'PatientController::store');
        $routes->get('show/(:num)', 'PatientController::show/$1');
        $routes->get('edit/(:num)', 'PatientController::edit/$1');
        $routes->post('update/(:num)', 'PatientController::update/$1');
        $routes->get('delete/(:num)', 'PatientController::delete/$1');
        $routes->get('get-doctor-schedule-dates', 'PatientController::getDoctorScheduleDates');
        $routes->get('get-available-times', 'PatientController::getAvailableTimes');
    });
    
    // Scheduling
    $routes->group('schedule', function($routes) {
        // View-only routes (accessible by admin and receptionist)
        $routes->get('/', 'ScheduleController::index', ['filter' => 'auth:admin,receptionist']);
        $routes->get('view/(:num)', 'ScheduleController::view/$1', ['filter' => 'auth:admin,receptionist']);
        
        // Admin-only routes (create, edit, delete)
        $routes->get('create', 'ScheduleController::create');
        $routes->get('create-doctor', 'ScheduleController::createDoctor');
        $routes->get('create-nurse', 'ScheduleController::createNurse');
        $routes->post('store-doctor', 'ScheduleController::storeDoctor');
        $routes->post('store-nurse', 'ScheduleController::storeNurse');
        $routes->post('store', 'ScheduleController::store');
        $routes->get('edit/(:num)', 'ScheduleController::edit/$1');
        $routes->post('update/(:num)', 'ScheduleController::update/$1');
        $routes->get('delete/(:num)', 'ScheduleController::delete/$1');
        $routes->get('cleanup-old', 'ScheduleController::cleanupOldSchedules');
        $routes->get('clear-all-doctor', 'ScheduleController::clearAllDoctorSchedules');
    });
    
    // Nurse Schedules
    $routes->group('nurse-schedules', function($routes) {
        $routes->get('/', 'NurseScheduleController::index');
        $routes->get('create', 'NurseScheduleController::create');
        $routes->post('store', 'NurseScheduleController::store');
        $routes->get('edit/(:num)', 'NurseScheduleController::edit/$1');
        $routes->post('update/(:num)', 'NurseScheduleController::update/$1');
        $routes->get('delete/(:num)', 'NurseScheduleController::delete/$1');
        $routes->get('bulk-assign', 'NurseScheduleController::bulkAssign');
        $routes->post('bulk-assign-store', 'NurseScheduleController::bulkAssignStore');
    });
    
    // Billing
    $routes->group('billing', function($routes) {
        $routes->get('/', 'BillingController::index');
        $routes->get('create', 'BillingController::create');
        $routes->post('store', 'BillingController::store');
        $routes->get('edit/(:num)', 'BillingController::edit/$1');
        $routes->post('update/(:num)', 'BillingController::update/$1');
        $routes->get('delete/(:num)', 'BillingController::delete/$1');
    });
    
    // Lab Services
    $routes->group('lab', function($routes) {
        $routes->get('/', 'LabController::index');
        $routes->get('create', 'LabController::create');
        $routes->post('store', 'LabController::store');
        $routes->get('view/(:num)', 'LabController::view/$1');
        $routes->get('edit/(:num)', 'LabController::edit/$1');
        $routes->post('update/(:num)', 'LabController::update/$1');
        $routes->get('delete/(:num)', 'LabController::delete/$1');
        $routes->get('get-available-doctors', 'LabController::getAvailableDoctors');
    });
    
    // Pharmacy
    $routes->group('pharmacy', function($routes) {
        $routes->get('/', 'PharmacyController::index');
        $routes->get('create', 'PharmacyController::create');
        $routes->post('store', 'PharmacyController::store');
        $routes->get('edit/(:num)', 'PharmacyController::edit/$1');
        $routes->post('update/(:num)', 'PharmacyController::update/$1');
        $routes->get('delete/(:num)', 'PharmacyController::delete/$1');
        $routes->get('get-medicines-by-category', 'PharmacyController::getMedicinesByCategory');
        $routes->get('get-generic-names-by-category', 'PharmacyController::getGenericNamesByCategory');
        $routes->get('get-dosage-strength-by-category', 'PharmacyController::getDosageStrengthByCategory');
        $routes->get('get-inventory-info-by-category', 'PharmacyController::getInventoryInfoByCategory');
        $routes->get('get-average-pricing-by-category', 'PharmacyController::getAveragePricingByCategory');
        $routes->post('add-stock', 'PharmacyController::addStock');
        $routes->post('update-pricing', 'PharmacyController::updatePricing');
    });
    
    // Stock Monitoring
    $routes->group('stock', function($routes) {
        $routes->get('/', 'StockController::index');
        $routes->get('create', 'StockController::create');
        $routes->post('store', 'StockController::store');
        $routes->get('edit/(:num)', 'StockController::edit/$1');
        $routes->post('update/(:num)', 'StockController::update/$1');
        $routes->get('delete/(:num)', 'StockController::delete/$1');
    });
    
    // System Controls
    $routes->group('system', function($routes) {
        $routes->get('/', 'SystemController::index');
        $routes->get('create', 'SystemController::create');
        $routes->post('store', 'SystemController::store');
        $routes->get('edit/(:num)', 'SystemController::edit/$1');
        $routes->post('update/(:num)', 'SystemController::update/$1');
        $routes->get('delete/(:num)', 'SystemController::delete/$1');
    });
    
    // User Management
    $routes->group('users', function($routes) {
        $routes->get('/', 'UserController::index');
        $routes->get('create', 'UserController::create');
        $routes->post('store', 'UserController::store');
        $routes->get('edit/(:num)', 'UserController::edit/$1');
        $routes->post('update/(:num)', 'UserController::update/$1');
        $routes->get('delete/(:num)', 'UserController::delete/$1');
        $routes->get('restore/(:num)', 'UserController::restore/$1');
        $routes->get('get-doctor-schedule', 'UserController::getDoctorSchedule');
    });
});

// IT Staff routes
$routes->group('it', ['namespace' => 'App\\Controllers\\ITStaff', 'filter' => 'auth:itstaff,admin'], function($routes) {
    $routes->get('dashboard', 'DashboardController::index');
    $routes->get('logs', 'SystemLogController::index');
    $routes->get('logs/view/(:num)', 'SystemLogController::view/$1');
    $routes->get('logs/delete/(:num)', 'SystemLogController::delete/$1');
    $routes->post('logs/clear', 'SystemLogController::clear');
    
    $routes->group('users', function($routes) {
        $routes->get('/', 'UserManagementController::index');
        $routes->get('create', 'UserManagementController::create');
        $routes->post('store', 'UserManagementController::store');
        $routes->get('edit/(:num)', 'UserManagementController::edit/$1');
        $routes->post('update/(:num)', 'UserManagementController::update/$1');
        $routes->get('delete/(:num)', 'UserManagementController::delete/$1');
    });
    
    $routes->group('backup', function($routes) {
        $routes->get('/', 'BackupController::index');
        $routes->post('create', 'BackupController::create');
        $routes->get('download/(:num)', 'BackupController::download/$1');
        $routes->get('delete/(:num)', 'BackupController::delete/$1');
    });
    
    $routes->group('restore', function($routes) {
        $routes->get('/', 'RestoreController::index');
        $routes->post('restore/(:num)', 'RestoreController::restore/$1');
    });
});

// Mga ruta para sa schedule ng doctor
$routes->get('/doctor/schedule', 'Doctor\Doctor::schedule', ['filter' => 'auth:admin,doctor']);
$routes->post('/doctor/addSchedule', 'Doctor\Doctor::addSchedule', ['filter' => 'auth:admin,doctor']);
$routes->post('/doctor/updateSchedule/(:num)', 'Doctor\Doctor::updateSchedule/$1', ['filter' => 'auth:admin,doctor']);
$routes->post('/doctor/deleteSchedule/(:num)', 'Doctor\Doctor::deleteSchedule/$1', ['filter' => 'auth:admin,doctor']);
$routes->post('/doctor/getConflicts', 'Doctor\Doctor::getConflicts', ['filter' => 'auth:admin,doctor']);
$routes->get('/doctor/getScheduleData', 'Doctor\Doctor::getScheduleData', ['filter' => 'auth:admin,doctor']);
$routes->get('/doctor/getDoctors', 'Doctor\Doctor::getDoctors', ['filter' => 'auth:admin,doctor']);
// Mga shortcut papunta sa mga page ng doctor
$routes->get('doctor/appointments', 'Appointment::index', ['filter' => 'auth:doctor,admin']);

// Admin o Nurse lang ang may access
$routes->get('/nurse/reports', 'Nurse::reports', ['filter' => 'auth:admin,nurse']);

// Mga ruta para sa appointment
$routes->get('appointments/book', 'Appointment::book');
$routes->get('appointments/list', 'Appointment::index');
$routes->get('appointments/schedule', 'Appointment::schedule');
$routes->post('appointments/create', 'Appointment::create');
$routes->get('appointments/show/(:num)', 'Appointment::show/$1');
$routes->post('appointments/update/(:num)', 'Appointment::update/$1');
$routes->post('appointments/cancel/(:num)', 'Appointment::cancel/$1');
$routes->post('appointments/complete/(:num)', 'Appointment::complete/$1');
$routes->post('appointments/no-show/(:num)', 'Appointment::noShow/$1');
$routes->post('appointments/delete/(:num)', 'Appointment::delete/$1');

// Mga ruta para sa pag-query o pagkuha ng appointments
$routes->get('appointments/doctor/(:num)', 'Appointment::getByDoctor/$1');
$routes->get('appointments/patient/(:num)', 'Appointment::getByPatient/$1');
$routes->get('appointments/today', 'Appointment::getTodays');
$routes->get('appointments/upcoming', 'Appointment::getUpcoming');
$routes->get('appointments/search', 'Appointment::search');
$routes->get('appointments/stats', 'Appointment::getStats');


// Mga ruta para sa laboratory
$routes->get('laboratory/request', 'Laboratory::request', ['filter' => 'auth:labstaff,admin']);
$routes->post('laboratory/request/submit', 'Laboratory::submitRequest', ['filter' => 'auth:labstaff,admin']);
// Laboratory: mga ruta para sa test results
$routes->get('laboratory/testresult', 'Laboratory::testresult', ['filter' => 'auth:labstaff,admin']);
$routes->get('laboratory/testresult/view/(:any)', 'Laboratory::viewTestResult/$1', ['filter' => 'auth:labstaff,admin']);
$routes->match(['get', 'post'], 'laboratory/testresult/add/(:any)', 'Laboratory::addTestResult/$1', ['filter' => 'auth:labstaff,admin']);
$routes->get('laboratory/testresult/data', 'Laboratory::getTestResultsData');

// Mga ruta para sa administration
    $routes->group('admin', ['namespace' => 'App\\Controllers', 'filter' => 'auth:admin'], function($routes) {
        // Ang unified dashboard ay hinahandle ng Admin::index (naka-route na sa /admin/dashboard)
        $routes->get('Administration/RoleManagement', 'Admin::roleManagement');
        
        $routes->group('patients', function($routes) {
            $routes->get('', 'Admin\Patients::index');
            $routes->get('register', 'Admin\Patients::register');
            $routes->get('inpatient', 'Admin\Patients::inpatient');
            $routes->post('register', 'Admin\Patients::processRegister');  
            $routes->get('view/(:num)', 'Admin\Patients::view/$1');
            $routes->get('edit/(:num)', 'Admin\Patients::edit/$1');
            $routes->post('update/(:num)', 'Admin\Patients::update/$1');
            $routes->get('delete/(:num)', 'Admin\Patients::delete/$1');
        });
    });

// Endpoint para sa real-time na stats ng dashboard
$routes->get('receptionist/dashboard/stats', 'Receptionist\DashboardStats::stats', ['filter' => 'auth:receptionist,admin']);

// Pharmacy Dashboard Stats
$routes->get('pharmacy/dashboard/stats', 'Pharmacy\DashboardStats::stats', ['filter' => 'auth:pharmacy,admin']);

// Pharmacy Routes
$routes->group('pharmacy', ['namespace' => 'App\\Controllers\\Pharmacy'], function($routes) {
    $routes->get('/', 'PharmacyController::index');
    $routes->get('prescription-queue', 'PharmacyController::prescriptionQueue');
    $routes->get('medicine-release', 'PharmacyController::medicineRelease');
    $routes->get('stock-monitoring', 'PharmacyController::stockMonitoring');
    $routes->post('dispense/(:num)', 'PharmacyController::dispensePrescription/$1');
    $routes->post('update-pharmacy-status/(:num)', 'PharmacyController::updatePharmacyStatus/$1');
    $routes->post('update-stock/(:num)', 'PharmacyController::updateStock/$1');
    $routes->get('add-medicine', 'PharmacyController::addMedicine');
    $routes->post('add-medicine', 'PharmacyController::addMedicine');
    $routes->get('edit-medicine/(:num)', 'PharmacyController::editMedicine/$1');
    $routes->post('edit-medicine/(:num)', 'PharmacyController::editMedicine/$1');
    $routes->post('delete-medicine/(:num)', 'PharmacyController::deleteMedicine/$1');
});

// Test route (remove after testing)
$routes->get('pharmacy/test-insert', 'Pharmacy\TestController::testInsert');
$routes->get('pharmacy/direct-test', 'Pharmacy\DirectInsert::index');

// Lab Staff Dashboard Stats
$routes->get('labstaff/dashboard/stats', 'LabStaff\DashboardStats::stats', ['filter' => 'auth:labstaff,admin']);

// Accountant/Finance Dashboard Stats
$routes->get('accountant/dashboard/stats', 'Accountant\DashboardStats::stats', ['filter' => 'auth:finance,admin']);

// Accountant/Finance Routes (Admin can also access)
$routes->group('accounting', ['namespace' => 'App\\Controllers\\Accountant', 'filter' => 'auth:finance,admin'], function($routes) {
    // Finance Overview
    $routes->get('finance', 'FinanceOverviewController::index');
    $routes->get('finance/create', 'FinanceOverviewController::create');
    $routes->post('finance/store', 'FinanceOverviewController::store');
    $routes->get('finance/edit/(:num)', 'FinanceOverviewController::edit/$1');
    $routes->post('finance/update/(:num)', 'FinanceOverviewController::update/$1');
    $routes->get('finance/delete/(:num)', 'FinanceOverviewController::delete/$1');
    
    // Payment Reports
    $routes->get('payments', 'PaymentReportController::index');
    $routes->get('payments/create', 'PaymentReportController::create');
    $routes->post('payments/store', 'PaymentReportController::store');
    $routes->get('payments/edit/(:num)', 'PaymentReportController::edit/$1');
    $routes->post('payments/update/(:num)', 'PaymentReportController::update/$1');
    $routes->get('payments/delete/(:num)', 'PaymentReportController::delete/$1');
    
    // Expense Tracking
    $routes->get('expenses', 'ExpenseController::index');
    $routes->get('expenses/create', 'ExpenseController::create');
    $routes->post('expenses/store', 'ExpenseController::store');
    $routes->get('expenses/edit/(:num)', 'ExpenseController::edit/$1');
    $routes->post('expenses/update/(:num)', 'ExpenseController::update/$1');
    $routes->get('expenses/delete/(:num)', 'ExpenseController::delete/$1');
    
    // Medication Billing
    $routes->get('medication-billing', 'MedicationBillingController::index');
    $routes->get('medication-billing/view/(:num)', 'MedicationBillingController::view/$1');
    $routes->get('medication-billing/invoice/(:num)', 'MedicationBillingController::invoice/$1');
    $routes->post('medication-billing/process-payment/(:num)', 'MedicationBillingController::processPayment/$1');
    $routes->post('medication-billing/cancel/(:num)', 'MedicationBillingController::cancel/$1');
    
    // Charges Management (Auto-generated charges from consultations)
    $routes->get('charges', 'ChargesController::index');
    $routes->get('charges/view/(:num)', 'ChargesController::view/$1');
    $routes->get('charges/invoice/(:num)', 'ChargesController::invoice/$1');
    $routes->post('charges/approve/(:num)', 'ChargesController::approve/$1');
    $routes->post('charges/process-payment/(:num)', 'ChargesController::processPayment/$1');
    $routes->post('charges/cancel/(:num)', 'ChargesController::cancel/$1');
    
    // Discharge Management (Finalize billing before discharge)
    $routes->get('discharge', 'DischargeController::index');
    $routes->get('discharge/finalize/(:num)', 'DischargeController::finalize/$1');
    $routes->post('discharge/process/(:num)', 'DischargeController::processDischarge/$1');
});

// Doctor read-only access to medication billing
$routes->group('accounting/medication-billing', ['namespace' => 'App\\Controllers\\Accountant', 'filter' => 'auth:doctor'], function($routes) {
    $routes->get('/', 'MedicationBillingController::index');
    $routes->get('view/(:num)', 'MedicationBillingController::view/$1');
    $routes->get('invoice/(:num)', 'MedicationBillingController::invoice/$1');
});

$routes->group('receptionist/appointments', ['namespace' => 'App\\Controllers', 'filter' => 'auth:receptionist,admin'], function($routes) {
    // Gamitin ang Appointment::book para maipasa sa view ang patients at doctors mula sa database
    $routes->get('book', 'Appointment::book');
});

$routes->group('receptionist/patients', ['namespace' => 'App\\Controllers\\Receptionist', 'filter' => 'auth:receptionist,admin'], function($routes) {
    $routes->get('/', 'Patients::index');
    $routes->get('list', 'Patients::index');
    $routes->get('register', 'Patients::register');
    $routes->get('outpatient', 'Patients::outpatient');
    $routes->get('create', 'Patients::create'); // Keep for backward compatibility
    $routes->post('store', 'Patients::store');
    $routes->get('show/(:num)', 'Patients::show/$1');
    $routes->get('edit/(:num)', 'Patients::edit/$1');
    $routes->post('update/(:num)', 'Patients::update/$1');
    $routes->post('delete/(:num)', 'Patients::delete/$1');
    $routes->get('get-doctor-schedule-dates', 'Patients::getDoctorScheduleDates');
    $routes->get('get-available-times', 'Patients::getAvailableTimes');
});

// Receptionist - Assign Doctor Routes
$routes->group('receptionist/assign-doctor', ['namespace' => 'App\\Controllers\\Receptionist', 'filter' => 'auth:receptionist,admin'], function($routes) {
    $routes->get('waiting-list', 'AssignDoctorController::waitingList');
    $routes->get('available-doctors', 'AssignDoctorController::getAvailableDoctors');
    $routes->post('assign', 'AssignDoctorController::assign');
});

// Receptionist - Doctor Schedules (redirects to admin schedule view)
$routes->get('receptionist/doctor-schedules', 'Receptionist\DoctorScheduleController::index', ['filter' => 'auth:receptionist,admin']);

// Receptionist - View Schedules (read-only access to admin schedules)
$routes->group('receptionist/schedule', ['namespace' => 'App\\Controllers\\Admin', 'filter' => 'auth:receptionist,admin'], function($routes) {
    $routes->get('/', 'ScheduleController::index');
    $routes->get('view/(:num)', 'ScheduleController::view/$1');
});

$routes->group('receptionist/rooms', ['namespace' => 'App\\Controllers\\Receptionist', 'filter' => 'auth:receptionist,admin'], function($routes) {
    $routes->get('ward/(:segment)', 'Rooms::ward/$1');
    $routes->get('type/(:segment)', 'Rooms::type/$1');
    $routes->get('assign/(:num)', 'Rooms::assignForm/$1');
    $routes->post('assign/(:num)', 'Rooms::assignStore/$1');
    $routes->post('vacate/(:num)', 'Rooms::vacate/$1');
    $routes->get('get-beds/(:num)', 'Rooms::getBeds/$1'); // AJAX endpoint for beds
});

// Receptionist - Admission (can assign rooms/beds)
$routes->group('receptionist/admission', ['namespace' => 'App\\Controllers', 'filter' => 'auth:receptionist'], function($routes) {
    $routes->get('pending', 'Nurse\AdmissionController::pending');
});

// Doctor routes
$routes->group('doctor', ['namespace' => 'App\\Controllers', 'filter' => 'auth:doctor,admin'], function($routes) {
    // ER Bed Management Routes (for doctors after triage)
    $routes->group('er-beds', ['namespace' => 'App\\Controllers\\Nurse'], function($routes) {
        $routes->get('/', 'ERBedController::index');
        $routes->post('assign', 'ERBedController::assign');
        $routes->post('release', 'ERBedController::release');
        $routes->get('get-available-beds', 'ERBedController::getAvailableBeds');
    });
    $routes->get('dashboard', 'Doctor\DashboardController::index');
    $routes->get('dashboard/stats', 'Doctor\DashboardStats::stats');

    // Patient List
    $routes->group('patients', function($routes) {
        $routes->get('/', 'Doctor\PatientController::index');
        $routes->get('view/(:num)', 'Doctor\PatientController::view/$1');
        $routes->get('create', 'Doctor\PatientController::create');
        $routes->post('store', 'Doctor\PatientController::store');
        $routes->get('edit/(:num)', 'Doctor\PatientController::edit/$1');
        $routes->post('update/(:num)', 'Doctor\PatientController::update/$1');
        $routes->get('delete/(:num)', 'Doctor\PatientController::delete/$1');
        $routes->post('approve-admission', 'Doctor\PatientController::approveAdmission'); // Approve admission request
    });

    // Consultation Schedule
    $routes->group('consultations', function($routes) {
        $routes->get('upcoming', 'Doctor\ConsultationController::upcoming');
        $routes->get('my-schedule', 'Doctor\ConsultationController::mySchedule');
        $routes->get('create', 'Doctor\ConsultationController::create');
        $routes->post('store', 'Doctor\ConsultationController::store');
        $routes->get('start/(:num)/(:any)', 'Doctor\ConsultationController::startConsultation/$1/$2');
        $routes->post('save-consultation', 'Doctor\ConsultationController::saveConsultation');
        $routes->get('edit/(:num)', 'Doctor\ConsultationController::edit/$1');
        $routes->post('update/(:num)', 'Doctor\ConsultationController::update/$1');
        $routes->get('delete/(:num)', 'Doctor\ConsultationController::delete/$1');
        $routes->get('pediatrics', 'Doctor\ConsultationController::pediatricsList');
        $routes->get('pediatrics/consult/(:num)', 'Doctor\ConsultationController::pediatricsConsult/$1');
        $routes->post('pediatrics/save', 'Doctor\ConsultationController::savePediatricsConsultation');
    });
    
    // Lab Requests from Nurses
    $routes->group('lab-requests', function($routes) {
        $routes->get('/', 'Doctor\LabRequestController::index');
        $routes->post('confirm/(:num)', 'Doctor\LabRequestController::confirm/$1');
        $routes->post('reject/(:num)', 'Doctor\LabRequestController::reject/$1');
    });

    // Doctor Orders
    $routes->group('orders', function($routes) {
        $routes->get('/', 'Doctor\OrderController::index');
        $routes->get('create', 'Doctor\OrderController::create');
        $routes->post('store', 'Doctor\OrderController::store');
        $routes->get('view/(:num)', 'Doctor\OrderController::view/$1');
        $routes->get('print-prescription/(:num)', 'Doctor\OrderController::printPrescription/$1');
        $routes->get('edit/(:num)', 'Doctor\OrderController::edit/$1');
        $routes->post('update/(:num)', 'Doctor\OrderController::update/$1');
        $routes->post('cancel/(:num)', 'Doctor\OrderController::cancel/$1');
    });
    
    // Admission Orders (After patient is admitted)
    $routes->group('admission-orders', function($routes) {
        $routes->get('/', 'Doctor\AdmissionOrdersController::index');
        $routes->get('view/(:num)', 'Doctor\AdmissionOrdersController::view/$1');
        $routes->get('create/(:num)', 'Doctor\AdmissionOrdersController::create/$1');
        $routes->post('store', 'Doctor\AdmissionOrdersController::store');
    });

    // Doctor Discharge (Primary role - creates discharge orders)
    $routes->group('discharge', function($routes) {
        $routes->get('/', 'Doctor\DischargeController::index');
        $routes->get('create/(:num)', 'Doctor\DischargeController::create/$1');
        $routes->post('store', 'Doctor\DischargeController::store');
        $routes->get('view/(:num)', 'Doctor\DischargeController::view/$1');
    });
    
    // Doctor Notifications
    $routes->group('notifications', function($routes) {
        $routes->post('mark-read/(:num)', 'Doctor\NotificationController::markRead/$1');
        $routes->post('mark-all-read', 'Doctor\NotificationController::markAllRead');
    });
});

// Admission Routes (Nurse, Receptionist ONLY - Doctor can only mark "For Admission" but cannot process)
// NOT for Lab, Pharmacy, Accountant, IT, Doctor
$routes->group('admission', ['namespace' => 'App\\Controllers', 'filter' => 'auth:nurse,receptionist'], function($routes) {
    $routes->get('create/(:num)', 'AdmissionController::create/$1');
    $routes->get('create', 'AdmissionController::create');
    $routes->post('store', 'AdmissionController::store');
    $routes->get('view/(:num)', 'AdmissionController::view/$1');
    $routes->get('beds/(:num)', 'AdmissionController::getBeds/$1');
});

// Nurse routes
$routes->group('nurse', ['namespace' => 'App\\Controllers\\Nurse', 'filter' => 'auth:nurse,admin'], function($routes) {
    // Laboratory routes
    $routes->group('laboratory', function($routes) {
        $routes->get('request', 'LaboratoryController::request');
        $routes->post('request/store', 'LaboratoryController::storeRequest');
        $routes->post('request/update-status/(:num)', 'LaboratoryController::updateRequestStatus/$1');
        $routes->post('request/mark-specimen-collected/(:num)', 'LaboratoryController::markSpecimenCollected/$1');
        $routes->get('testresult', 'LaboratoryController::testresult');
    });
    // ER Bed Management Routes
    $routes->group('er-beds', function($routes) {
        $routes->get('/', 'ERBedController::index');
        $routes->post('assign', 'ERBedController::assign');
        $routes->post('release', 'ERBedController::release');
        $routes->get('get-available-beds', 'ERBedController::getAvailableBeds');
    });
    
    // Doctor Schedule View
    $routes->get('doctor-schedules', 'DoctorScheduleController::index');
    
    // Nurse Discharge (Secondary role - prepare patient, print instructions)
    $routes->get('discharge', 'DischargeController::index');
    $routes->get('discharge/view/(:num)', 'DischargeController::view/$1');
    $routes->get('discharge/print/(:num)', 'DischargeController::printInstructions/$1');
    // Dashboard
    $routes->get('dashboard', 'DashboardController::index');
    $routes->get('dashboard/stats', 'DashboardStats::stats');
    
    // Notifications
    $routes->group('notifications', function($routes) {
        $routes->post('mark-read/(:num)', 'NotificationController::markAsRead/$1');
        $routes->post('mark-all-read', 'NotificationController::markAllAsRead');
    });
    
    // Patient Handling
    $routes->group('patients', function($routes) {
        $routes->get('view', 'PatientController::view');
        $routes->get('details/(:num)', 'PatientController::details/$1');
        $routes->get('add-vitals/(:num)', 'PatientController::addVitals/$1');
        $routes->post('store-vitals/(:num)', 'PatientController::storeVitals/$1');
        $routes->get('add-note/(:num)', 'PatientController::addNote/$1');
        $routes->post('store-note/(:num)', 'PatientController::storeNote/$1');
        $routes->post('update-order-status/(:num)', 'PatientController::updateOrderStatus/$1');
    });
    
    // Appointment Queue
    $routes->group('appointments', function($routes) {
        $routes->get('list', 'AppointmentController::list');
        $routes->post('update-status/(:num)', 'AppointmentController::updateStatus/$1');
        $routes->get('history', 'AppointmentController::history');
        $routes->get('history/(:num)', 'AppointmentController::history/$1');
    });
    
    // Medication Administration
    $routes->group('medications', function($routes) {
        $routes->get('/', 'MedicationController::index');
        $routes->get('view/(:num)', 'MedicationController::view/$1');
        $routes->post('administer/(:num)', 'MedicationController::administer/$1');
    });
    
    // Triage
    $routes->group('triage', function($routes) {
        $routes->get('/', 'TriageController::index');
        $routes->get('triage/(:num)/(:any)', 'TriageController::triage/$1/$2'); // patient_id, source
        $routes->get('get-doctors', 'TriageController::getAvailableDoctors');
        $routes->post('save', 'TriageController::save');
        $routes->post('send-to-doctor', 'TriageController::sendToDoctor');
    });
    
    // Lab Assistance - upload result route
    $routes->post('laboratory/upload-result/(:num)', 'LaboratoryController::uploadResult/$1');
    
    // Admission (Nurse can process admissions)
    $routes->group('admission', function($routes) {
        $routes->get('pending', 'AdmissionController::pending');
    });
});
