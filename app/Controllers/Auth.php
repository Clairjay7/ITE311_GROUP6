<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;

class Auth extends Controller
{
    public function login()
    {
        return view('auth/login');
    }

    public function process_login()
    {
        $session = session();
        $model = new UserModel();

        // Debug: Log the posted data
        // log_message('debug', 'Login attempt with data: ' . print_r($this->request->getPost(), true));

        // Get the username and password
        $username = trim($this->request->getPost('username'));
        $password = $this->request->getPost('password');

        // Basic validation
        $validation = \Config\Services::validation();
        $validation->setRules([
            'username' => 'required',
            'password' => 'required|min_length[3]',
        ], [
            'username' => [
                'required' => 'Username is required'
            ],
            'password' => [
                'required' => 'Password is required',
                'min_length' => 'Password must be at least 3 characters long'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Try to find user by username
        $user = $model->where('username', $username)->first();

        if (!$user) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Invalid username or password.')
                           ->with('errors', ['username' => 'Invalid username or password']);
        }

        // Check if account is active
        if ($user['status'] !== 'active') {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Your account is inactive. Please contact the administrator.');
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Invalid username or password.')
                           ->with('errors', ['password' => 'Invalid password']);
        }

        // Get user role
        $db = \Config\Database::connect();
        $role = $db->table('roles')
                  ->where('id', $user['role_id'])
                  ->get()
                  ->getRowArray();

        if (!$role) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Your account has an invalid role. Please contact the administrator.');
        }

        // Set user session
        $userData = [
            'user_id'    => $user['id'],
            'username'   => $user['username'],
            'email'      => $user['email'],
            'role'       => $role['name'],
            'role_id'    => $user['role_id'],
            'isLoggedIn' => true,
            'logged_in'  => time()
        ];

        $session->set($userData);

        // Debug: Log successful login
        // log_message('debug', 'User logged in: ' . $user['username']);

        // Redirect based on role
        $redirectUrl = $this->getDashboardUrl($role['name']);
        return redirect()->to($redirectUrl);
    }

    /**
     * Get dashboard URL based on user role
     */
    protected function getDashboardUrl($role)
    {
        switch (strtolower($role)) {
            case 'admin':
                return '/admin/dashboard';
            case 'doctor':
                return '/doctor/dashboard';
            case 'nurse':
                return '/nurse/dashboard';
            case 'receptionist':
                return '/receptionist/dashboard';
            case 'patient':
                return '/patient/dashboard';
            default:
                return '/dashboard';
        }
    }

    public function register()
    {
        return view('auth/register');
    }

    public function process_register()
    {
        $model = new UserModel();
        $db = \Config\Database::connect();
        $postedRoleId = $this->request->getPost('role_id');
        $postedRoleName = $this->request->getPost('role');
        $resolvedRoleId = null;
        if (!empty($postedRoleId)) {
            $resolvedRoleId = (int) $postedRoleId;
        } elseif (!empty($postedRoleName)) {
            $roleRow = $db->table('roles')->where('name', $postedRoleName)->get()->getRowArray();
            $resolvedRoleId = $roleRow['id'] ?? null;
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role_id'  => $resolvedRoleId,
            'status'   => 'active',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $model->insert($data);

        return redirect()->to('/login')->with('success', 'Registration successful! Please login.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'You have been logged out successfully.');
    }

    
}
