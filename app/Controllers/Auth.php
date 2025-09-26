<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;

class Auth extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Login page
     */
    public function index()
    {
        // If already logged in, redirect to appropriate dashboard
        if (session()->get('isLoggedIn')) {
            return $this->redirectToDashboard(session()->get('role'));
        }
        return view('auth/login');
    }

    /**
     * Process login
     */
    public function process()
    {
        // Enable error reporting for debugging
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        $session = session();
        $request = service('request');
        
        // Log raw POST data
        log_message('debug', 'Raw POST data: ' . file_get_contents('php://input'));
        
        // Get form data
        $username = $request->getPost('username');
        $password = $request->getPost('password');
        
        // Debug: Log the login attempt
        log_message('debug', "Login attempt - Username: {$username}");
        log_message('debug', 'POST data: ' . print_r($this->request->getPost(), true));
        
        if (empty($username) || empty($password)) {
            log_message('error', 'Empty username or password');
            return redirect()->back()->withInput()->with('error', 'Please enter both username and password');
        }
        
        try {
            // Find user by username only
            $user = $this->userModel->where('username', $username)
                                  ->first();
            
            if (!$user) {
                log_message('error', "User not found: {$username}");
                return redirect()->back()->withInput()->with('error', 'Invalid username or password');
            }
            
            // Debug: Show the actual SQL query being executed
            $db = \Config\Database::connect();
            $query = $db->getLastQuery();
            log_message('debug', 'SQL Query: ' . $query);
            
            // Verify password
            if (!password_verify($password, $user['password_hash'])) {
                log_message('error', 'Password verification failed for user: ' . $username);
                log_message('debug', 'Input password: ' . $password);
                log_message('debug', 'Stored hash: ' . $user['password_hash']);
                
                // For debugging: Check if password needs rehashing
                if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
                    log_message('debug', 'Password needs rehashing');
                }
                
                return redirect()->back()->withInput()->with('error', 'Invalid username or password');
            }
            
            // If we get here, login is successful
            $userData = [
                'user_id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'role' => $user['role'],
                'isLoggedIn' => true
            ];
            
            $session->set($userData);
            
            // Update last login
            $this->userModel->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);
            
            // Redirect to dashboard
            return $this->redirectToDashboard($user['role']);
            
        } catch (\Exception $e) {
            log_message('error', 'Login error: ' . $e->getMessage());
            log_message('error', $e->getTraceAsString());
            return redirect()->back()->withInput()->with('error', 'An error occurred during login. Please try again.');
        }
    }
    
    /**
     * Logout
     */
    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/login');
    }

    /**
     * Redirect to appropriate dashboard based on role
     */
    protected function redirectToDashboard($role)
    {
        switch($role) {
            case 'super_admin':
                return redirect()->to('/auth/super-admin-dashboard');
            case 'doctor':
                return redirect()->to('/auth/doctor-dashboard');
            case 'nurse':
                return redirect()->to('/auth/nurse-dashboard');
            case 'receptionist':
                return redirect()->to('/auth/receptionist-dashboard');
            case 'laboratory_staff':
                return redirect()->to('/auth/laboratory-dashboard');
            case 'pharmacist':
                return redirect()->to('/auth/pharmacist-dashboard');
            case 'accountant':
                return redirect()->to('/auth/accountant-dashboard');
            case 'it_staff':
                return redirect()->to('/auth/it-dashboard');
            default:
                return redirect()->to('/');
        }
    }

    /**
     * Super Admin Dashboard
     */
    public function superAdminDashboard()
    {
        // Check if user is logged in and has correct role
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'super_admin') {
            return redirect()->to('/login');
        }

        $db = \Config\Database::connect();
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');

        $totalDoctors = (int) $db->table('doctors')->countAllResults();
        $totalPatients = (int) $db->table('patients')->countAllResults();
        $todaysAppointments = (int) $db->table('appointments')
            ->where('appointment_date >=', $todayStart)
            ->where('appointment_date <=', $todayEnd)
            ->countAllResults();
        $pendingBills = (int) $db->table('billing')->where('status', 'pending')->countAllResults();

        $data = [
            'title' => 'Super Admin Dashboard',
            'username' => session()->get('username'),
            'role' => 'Super Admin',
            'totalDoctors' => $totalDoctors,
            'totalPatients' => $totalPatients,
            'todaysAppointments' => $todaysAppointments,
            'pendingBills' => $pendingBills,
        ];
        return view('SuperAdmin/dashboard', $data);
    }

    /**
     * Doctor Dashboard
     */
    public function doctorDashboard()
    {
        // Check if user is logged in and has correct role
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'doctor') {
            return redirect()->to('/login');
        }

        $db = \Config\Database::connect();
        // Resolve the current doctor's internal id via users->doctors mapping
        $doctor = $db->table('doctors')->where('user_id', session()->get('user_id'))->get()->getRowArray();
        $doctorId = $doctor['id'] ?? 0;

        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');

        $activePatients = (int) $db->table('appointments')
            ->select('patient_id')
            ->where('doctor_id', $doctorId)
            ->groupBy('patient_id')->countAllResults();

        $todaysAppointments = (int) $db->table('appointments')
            ->where('doctor_id', $doctorId)
            ->where('appointment_date >=', $todayStart)
            ->where('appointment_date <=', $todayEnd)
            ->countAllResults();

        $pendingReports = (int) $db->table('lab_requests')
            ->where('doctor_id', $doctorId)
            ->where('status !=', 'completed')
            ->countAllResults();

        $emergencyCases = 0; // placeholder until triage table exists

        $data = [
            'title' => 'Doctor Dashboard',
            'username' => session()->get('username'),
            'role' => 'Doctor',
            'rolePath' => 'doctor',
            'user' => [
                'full_name' => session()->get('first_name') . ' ' . session()->get('last_name'),
                'role' => 'Doctor'
            ],
            'activePatients' => $activePatients,
            'todaysAppointments' => $todaysAppointments,
            'pendingReports' => $pendingReports,
            'emergencyCases' => $emergencyCases,
        ];
        return view('doctor/doctor_dashboard', $data);
    }

    /**
     * Nurse Dashboard
     */
    public function nurseDashboard()
    {
        // Check if user is logged in and has correct role
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'nurse') {
            return redirect()->to('/login');
        }

        $data = [
            'title' => 'Nurse Dashboard',
            'username' => session()->get('username'),
            'role' => 'Nurse',
            'rolePath' => 'nurse',
            'user' => [
                'full_name' => session()->get('first_name') . ' ' . session()->get('last_name'),
                'role' => 'Nurse'
            ]
        ];
        return view('nurse/dashboard', $data);
    }

    /**
     * Receptionist Dashboard
     */
    public function receptionistDashboard()
    {
        // Check if user is logged in and has correct role
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'receptionist') {
            return redirect()->to('/login');
        }

        $data = [
            'title' => 'Receptionist Dashboard',
            'username' => session()->get('username'),
            'role' => 'Receptionist'
        ];
        return view('Receptionist/dashboard', $data);
    }

    /**
     * Laboratory Staff Dashboard
     */
    public function laboratoryDashboard()
    {
        // Check if user is logged in and has correct role
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'laboratory_staff') {
            return redirect()->to('/login');
        }

        $data = [
            'title' => 'Laboratory Staff Dashboard',
            'username' => session()->get('username'),
            'role' => 'Laboratory Staff'
        ];
        return view('Laboratory/dashboard', $data);
    }

    /**
     * Pharmacist Dashboard
     */
    public function pharmacistDashboard()
    {
        // Check if user is logged in and has correct role
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'pharmacist') {
            return redirect()->to('/login');
        }

        $data = [
            'title' => 'Pharmacist Dashboard',
            'username' => session()->get('username'),
            'role' => 'Pharmacist'
        ];
        return view('Pharmacist/dashboard', $data);
    }

    /**
     * Accountant Dashboard
     */
    public function accountantDashboard()
    {
        // Check if user is logged in and has correct role
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'accountant') {
            return redirect()->to('/login');
        }

        // Sample data - replace with actual database queries
        $data = [
            'title' => 'Accountant Dashboard',
            'username' => session()->get('username'),
            'role' => 'Accountant',
            'todayRevenue' => 1500,
            'outstandingBalance' => 25000, // Add the missing outstanding balance
            'pendingBills' => array_fill(0, 5, ['id' => 1, 'patient' => 'John Doe', 'amount' => 5000]),
            'insuranceClaims' => array_fill(0, 3, ['id' => 1, 'patient' => 'Jane Smith', 'amount' => 10000, 'status' => 'Pending']),
            'rolePath' => 'accountant',
            'user' => [
                'full_name' => session()->get('first_name') . ' ' . session()->get('last_name'),
                'role' => 'Accountant'
            ]
        ];
        
        return view('Accountant/dashboard', $data);
    }

    /**
     * IT Staff Dashboard
     */
    public function itDashboard()
    {
        // Check if user is logged in and has correct role
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'it_staff') {
            return redirect()->to('/login');
        }

        $data = [
            'title' => 'IT Staff Dashboard',
            'username' => session()->get('username'),
            'role' => 'IT Staff'
        ];
        return view('ITStaff/dashboard', $data);
    }
}
