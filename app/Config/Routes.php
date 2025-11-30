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
$routes->get('nurse/dashboard', 'Dashboard::index', ['filter' => 'auth:nurse,admin']);
$routes->get('receptionist/dashboard', 'Dashboard::index', ['filter' => 'auth:receptionist,admin']);
$routes->get('patient/dashboard', 'Dashboard::index', ['filter' => 'auth:patient']);
$routes->get('accounting/dashboard', 'Dashboard::index', ['filter' => 'auth:finance,admin']);
$routes->get('itstaff/dashboard', 'Dashboard::index', ['filter' => 'auth:itstaff,admin']);
$routes->get('labstaff/dashboard', 'Dashboard::index', ['filter' => 'auth:labstaff,admin']);
$routes->get('pharmacy/dashboard', 'Dashboard::index', ['filter' => 'auth:pharmacy,admin']);

// Para sa backward compatibility: lumang URL ng admin dashboard (iba sa unified dashboard)
$routes->get('/admin/dashboard', 'Admin\DashboardController::index', ['filter' => 'auth:admin']);

// Mga admin management page (hindi mga dashboard)
$routes->get('admin/Administration/ManageUser', 'Admin::manageUsers', ['filter' => 'auth:admin']);

// New Admin Module Routes
$routes->group('admin', ['namespace' => 'App\\Controllers\\Admin', 'filter' => 'auth:admin'], function($routes) {
    // Dashboard
    $routes->get('dashboard', 'DashboardController::index');
    
    // Patients
    $routes->group('patients', function($routes) {
        $routes->get('/', 'PatientController::index');
        $routes->get('create', 'PatientController::create');
        $routes->post('store', 'PatientController::store');
        $routes->get('edit/(:num)', 'PatientController::edit/$1');
        $routes->post('update/(:num)', 'PatientController::update/$1');
        $routes->get('delete/(:num)', 'PatientController::delete/$1');
    });
    
    // Scheduling
    $routes->group('schedule', function($routes) {
        $routes->get('/', 'ScheduleController::index');
        $routes->get('create', 'ScheduleController::create');
        $routes->post('store', 'ScheduleController::store');
        $routes->get('edit/(:num)', 'ScheduleController::edit/$1');
        $routes->post('update/(:num)', 'ScheduleController::update/$1');
        $routes->get('delete/(:num)', 'ScheduleController::delete/$1');
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
        $routes->get('edit/(:num)', 'LabController::edit/$1');
        $routes->post('update/(:num)', 'LabController::update/$1');
        $routes->get('delete/(:num)', 'LabController::delete/$1');
    });
    
    // Pharmacy
    $routes->group('pharmacy', function($routes) {
        $routes->get('/', 'PharmacyController::index');
        $routes->get('create', 'PharmacyController::create');
        $routes->post('store', 'PharmacyController::store');
        $routes->get('edit/(:num)', 'PharmacyController::edit/$1');
        $routes->post('update/(:num)', 'PharmacyController::update/$1');
        $routes->get('delete/(:num)', 'PharmacyController::delete/$1');
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

$routes->group('receptionist/appointments', ['namespace' => 'App\\Controllers', 'filter' => 'auth:receptionist,admin'], function($routes) {
    // Gamitin ang Appointment::book para maipasa sa view ang patients at doctors mula sa database
    $routes->get('book', 'Appointment::book');
});

$routes->group('receptionist/patients', ['namespace' => 'App\\Controllers\\Receptionist', 'filter' => 'auth:receptionist,admin'], function($routes) {
    $routes->get('/', 'Patients::index');
    $routes->get('list', 'Patients::index');
    $routes->get('create', 'Patients::create');
    $routes->post('store', 'Patients::store');
    $routes->get('show/(:num)', 'Patients::show/$1');
    $routes->get('edit/(:num)', 'Patients::edit/$1');
    $routes->post('update/(:num)', 'Patients::update/$1');
    $routes->post('delete/(:num)', 'Patients::delete/$1');
});

$routes->group('receptionist/rooms', ['namespace' => 'App\\Controllers\\Receptionist', 'filter' => 'auth:receptionist,admin'], function($routes) {
    $routes->get('ward/(:segment)', 'Rooms::ward/$1');
    $routes->get('assign/(:num)', 'Rooms::assignForm/$1');
    $routes->post('assign/(:num)', 'Rooms::assignStore/$1');
    $routes->post('vacate/(:num)', 'Rooms::vacate/$1');
});

// Doctor routes
$routes->group('doctor', ['namespace' => 'App\\Controllers', 'filter' => 'auth:doctor,admin'], function($routes) {
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
    });

    // Consultation Schedule
    $routes->group('consultations', function($routes) {
        $routes->get('upcoming', 'Doctor\ConsultationController::upcoming');
        $routes->get('my-schedule', 'Doctor\ConsultationController::mySchedule');
        $routes->get('create', 'Doctor\ConsultationController::create');
        $routes->post('store', 'Doctor\ConsultationController::store');
        $routes->get('edit/(:num)', 'Doctor\ConsultationController::edit/$1');
        $routes->post('update/(:num)', 'Doctor\ConsultationController::update/$1');
        $routes->get('delete/(:num)', 'Doctor\ConsultationController::delete/$1');
    });
});
