<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;

class Login extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        // If already logged in, redirect to appropriate dashboard
        if (session()->get('isLoggedIn')) {
            return $this->redirectToDashboard(session()->get('role'));
        }
        return view('auth/login');
    }

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
    
    protected function redirectToDashboard($role)
    {
        switch($role) {
            case 'super_admin':
                return redirect()->to('/super-admin/dashboard');
            case 'doctor':
                return redirect()->to('/doctor/dashboard');
            case 'nurse':
                return redirect()->to('/nurse/dashboard');
            case 'receptionist':
                return redirect()->to('/receptionist/dashboard');
            case 'laboratory_staff':
                return redirect()->to('/laboratory/dashboard');
            case 'pharmacist':
                return redirect()->to('/pharmacist/dashboard');
            case 'accountant':
                return redirect()->to('/accountant/dashboard');
            case 'it_staff':
                return redirect()->to('/it/dashboard');
            default:
                return redirect()->to('/');
        }
    }
    
    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/login');
    }
}
