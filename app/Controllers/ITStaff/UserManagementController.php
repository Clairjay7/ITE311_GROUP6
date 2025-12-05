<?php

namespace App\Controllers\ITStaff;

use App\Controllers\BaseController;
use App\Models\UserModel;

class UserManagementController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->userModel = new UserModel();
    }

    public function index()
    {
        // Check if user is logged in and is IT staff
        if (!session()->get('logged_in') || session()->get('role') !== 'itstaff') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as IT staff to access this page.');
        }

        $db = \Config\Database::connect();
        
        // Get all users with their roles (including deleted users)
        $users = $db->table('users')
            ->select('users.*, roles.name as role_name, roles.description as role_description')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->orderBy('users.deleted_at', 'ASC') // Show non-deleted first
            ->orderBy('users.created_at', 'DESC')
            ->get()->getResultArray();

        $data = [
            'title' => 'User Management',
            'users' => $users,
        ];

        return view('itstaff/users/index', $data);
    }

    public function create()
    {
        // Check if user is logged in and is IT staff
        if (!session()->get('logged_in') || session()->get('role') !== 'itstaff') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as IT staff to access this page.');
        }

        $db = \Config\Database::connect();
        
        // Get all available roles (excluding patient role)
        $roles = $db->table('roles')
            ->whereNotIn('name', ['patient'])
            ->orderBy('name', 'ASC')
            ->get()->getResultArray();

        $data = [
            'title' => 'Add New User',
            'roles' => $roles,
        ];

        return view('itstaff/users/create', $data);
    }

    public function store()
    {
        // Check if user is logged in and is IT staff
        if (!session()->get('logged_in') || session()->get('role') !== 'itstaff') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as IT staff to access this page.');
        }

        $validation = $this->validate([
            'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'role_id' => 'required|integer|greater_than[0]',
            'status' => 'required|in_list[active,inactive]',
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role_id' => $this->request->getPost('role_id'),
            'status' => $this->request->getPost('status'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($this->userModel->insert($data)) {
            // Log the user creation
            $this->logUserAction('User created', $this->userModel->getInsertID());
            
            return redirect()->to('/it/users')->with('success', 'User created successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create user.');
        }
    }

    public function edit($id)
    {
        // Check if user is logged in and is IT staff
        if (!session()->get('logged_in') || session()->get('role') !== 'itstaff') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as IT staff to access this page.');
        }

        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to('/it/users')->with('error', 'User not found.');
        }

        $db = \Config\Database::connect();
        
        // Get all available roles (excluding patient role)
        $roles = $db->table('roles')
            ->whereNotIn('name', ['patient'])
            ->orderBy('name', 'ASC')
            ->get()->getResultArray();

        // Get user's role name
        $userRole = $db->table('roles')
            ->where('id', $user['role_id'])
            ->get()->getRowArray();

        $data = [
            'title' => 'Edit User',
            'user' => $user,
            'roles' => $roles,
            'userRole' => $userRole,
        ];

        return view('itstaff/users/edit', $data);
    }

    public function update($id)
    {
        // Check if user is logged in and is IT staff
        if (!session()->get('logged_in') || session()->get('role') !== 'itstaff') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as IT staff to access this page.');
        }

        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to('/it/users')->with('error', 'User not found.');
        }

        $validationRules = [
            'username' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email',
            'role_id' => 'required|integer|greater_than[0]',
            'status' => 'required|in_list[active,inactive]',
        ];

        // Check if username is unique (excluding current user)
        $existingUser = $this->userModel
            ->where('username', $this->request->getPost('username'))
            ->where('id !=', $id)
            ->first();
        
        if ($existingUser) {
            $validationRules['username'] .= '|is_unique[users.username]';
        }

        // Check if email is unique (excluding current user)
        $existingEmail = $this->userModel
            ->where('email', $this->request->getPost('email'))
            ->where('id !=', $id)
            ->first();
        
        if ($existingEmail) {
            $validationRules['email'] .= '|is_unique[users.email]';
        }

        // Add password validation only if password is provided
        if ($this->request->getPost('password')) {
            $validationRules['password'] = 'min_length[6]';
        }

        $validation = $this->validate($validationRules);

        if (!$validation) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'role_id' => $this->request->getPost('role_id'),
            'status' => $this->request->getPost('status'),
        ];

        // Update password only if provided
        if ($this->request->getPost('password')) {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        if ($this->userModel->update($id, $data)) {
            // Log the user update
            $this->logUserAction('User updated', $id);
            
            return redirect()->to('/it/users')->with('success', 'User updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update user.');
        }
    }

    public function delete($id)
    {
        // Check if user is logged in and is IT staff
        if (!session()->get('logged_in') || session()->get('role') !== 'itstaff') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as IT staff to access this page.');
        }

        // Prevent deleting yourself
        if ($id == session()->get('user_id')) {
            return redirect()->to('/it/users')->with('error', 'You cannot delete your own account.');
        }

        $user = $this->userModel->withDeleted()->find($id);
        
        if (!$user) {
            return redirect()->to('/it/users')->with('error', 'User not found.');
        }

        // Check if already deleted
        if (!empty($user['deleted_at'])) {
            return redirect()->to('/it/users')->with('error', 'User is already deleted.');
        }

        // Mark as deleted (soft delete) instead of hard delete
        $data = [
            'deleted_at' => date('Y-m-d H:i:s'),
            'status' => 'inactive', // Also set status to inactive
        ];

        if ($this->userModel->update($id, $data)) {
            // Log the user deletion
            $this->logUserAction('User deleted', $id);
            
            return redirect()->to('/it/users')->with('success', 'User marked as deleted successfully.');
        } else {
            return redirect()->to('/it/users')->with('error', 'Failed to delete user.');
        }
    }

    /**
     * Log user action to system logs
     */
    private function logUserAction($action, $userId)
    {
        try {
            $logModel = new \App\Models\SystemLogModel();
            $userAgent = $this->request->getUserAgent();
            $userAgentString = $userAgent ? (method_exists($userAgent, 'getAgentString') ? $userAgent->getAgentString() : (string)$userAgent) : 'Unknown';
            
            // Truncate user_agent if too long
            if (strlen($userAgentString) > 255) {
                $userAgentString = substr($userAgentString, 0, 252) . '...';
            }
            
            // Truncate action if too long
            $actionString = strtolower(str_replace(' ', '_', $action));
            if (strlen($actionString) > 100) {
                $actionString = substr($actionString, 0, 97) . '...';
            }
            
            $logModel->insert([
                'level' => 'info',
                'message' => substr($action . ' by IT Staff: ' . (session()->get('name') ?? 'Unknown') . ' (User ID: ' . $userId . ')', 0, 65535), // TEXT field limit
                'user_id' => session()->get('user_id'),
                'ip_address' => $this->request->getIPAddress() ?: '0.0.0.0',
                'user_agent' => $userAgentString,
                'module' => 'user_management',
                'action' => $actionString,
            ]);
        } catch (\Exception $e) {
            // Silently fail logging to prevent infinite loop
            log_message('error', 'Failed to log user action: ' . $e->getMessage());
        }
    }
}

