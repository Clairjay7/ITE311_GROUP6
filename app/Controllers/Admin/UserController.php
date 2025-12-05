<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class UserController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->userModel = new UserModel();
    }

    public function index()
    {
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

        return view('admin/users/index', $data);
    }

    public function create()
    {
        $db = \Config\Database::connect();
        
        // Get all available roles (excluding patient role)
        $roles = $db->table('roles')
            ->whereNotIn('name', ['patient'])
            ->orderBy('name', 'ASC')
            ->get()->getResultArray();

        // Get nurse role ID for JavaScript
        $nurseRole = $db->table('roles')
            ->where('LOWER(name)', 'nurse')
            ->get()->getRowArray();

        $data = [
            'title' => 'Add New User',
            'roles' => $roles,
            'nurseRoleId' => $nurseRole ? $nurseRole['id'] : null,
        ];

        return view('admin/users/create', $data);
    }

    public function store()
    {
        $db = \Config\Database::connect();
        
        // Get role name to check if it's nurse
        $roleId = $this->request->getPost('role_id');
        $role = $db->table('roles')->where('id', $roleId)->get()->getRowArray();
        $isNurse = $role && strtolower($role['name']) === 'nurse';
        
        $validationRules = [
            'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'role_id' => 'required|integer|greater_than[0]',
            'status' => 'required|in_list[active,inactive]',
        ];
        
        // Add shift preference validation if role is nurse
        if ($isNurse) {
            $validationRules['shift_preference'] = 'required|in_list[morning,night,bulk]';
        }

        if (!$this->validate($validationRules)) {
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
            $userId = $this->userModel->getInsertID();
            
            // If nurse, create initial schedules based on shift preference
            if ($isNurse) {
                $this->createNurseSchedules($userId, $this->request->getPost('shift_preference'));
            }
            
            return redirect()->to('/admin/users')->with('success', 'User created successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create user.');
        }
    }
    
    /**
     * Create initial nurse schedules based on shift preference
     */
    private function createNurseSchedules($nurseId, $shiftPreference)
    {
        $db = \Config\Database::connect();
        
        if (!$db->tableExists('nurse_schedules')) {
            return;
        }
        
        // Generate schedules for the next 30 days
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+30 days'));
        
        $schedules = [];
        $currentDate = $startDate;
        
        while ($currentDate <= $endDate) {
            // Skip weekends (Saturday = 6, Sunday = 0)
            $dayOfWeek = date('w', strtotime($currentDate));
            if ($dayOfWeek == 0 || $dayOfWeek == 6) {
                $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
                continue;
            }
            
            // Check which shifts to create based on preference
            $shiftsToCreate = [];
            if ($shiftPreference === 'morning' || $shiftPreference === 'bulk') {
                $shiftsToCreate[] = 'morning';
            }
            if ($shiftPreference === 'night' || $shiftPreference === 'bulk') {
                $shiftsToCreate[] = 'night';
            }
            
            foreach ($shiftsToCreate as $shiftType) {
                // Check if schedule already exists
                $existing = $db->table('nurse_schedules')
                    ->where('nurse_id', $nurseId)
                    ->where('shift_date', $currentDate)
                    ->where('shift_type', $shiftType)
                    ->get()
                    ->getRowArray();
                
                if (!$existing) {
                    $startTime = $shiftType === 'morning' ? '06:00:00' : '18:00:00';
                    $endTime = $shiftType === 'morning' ? '12:00:00' : '00:00:00';
                    
                    $schedules[] = [
                        'nurse_id' => $nurseId,
                        'shift_date' => $currentDate,
                        'shift_type' => $shiftType,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'status' => 'active',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                }
            }
            
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }
        
        if (!empty($schedules)) {
            // Insert in batches of 50
            $batchSize = 50;
            for ($i = 0; $i < count($schedules); $i += $batchSize) {
                $batch = array_slice($schedules, $i, $batchSize);
                $db->table('nurse_schedules')->insertBatch($batch);
            }
        }
    }

    public function edit($id)
    {
        // Prevent editing yourself
        if ($id == session()->get('user_id')) {
            return redirect()->to('/admin/users')->with('error', 'You cannot edit your own account.');
        }

        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found.');
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
        
        // Get current shift preference if user is a nurse
        $currentShiftPreference = null;
        if ($userRole && strtolower($userRole['name']) === 'nurse') {
            // Check existing schedules to determine preference
            if ($db->tableExists('nurse_schedules')) {
                $morningCount = $db->table('nurse_schedules')
                    ->where('nurse_id', $id)
                    ->where('shift_type', 'morning')
                    ->where('status', 'active')
                    ->countAllResults();
                
                $nightCount = $db->table('nurse_schedules')
                    ->where('nurse_id', $id)
                    ->where('shift_type', 'night')
                    ->where('status', 'active')
                    ->countAllResults();
                
                if ($morningCount > 0 && $nightCount > 0) {
                    $currentShiftPreference = 'bulk';
                } elseif ($morningCount > 0) {
                    $currentShiftPreference = 'morning';
                } elseif ($nightCount > 0) {
                    $currentShiftPreference = 'night';
                }
            }
        }

        $data = [
            'title' => 'Edit User',
            'user' => $user,
            'roles' => $roles,
            'userRole' => $userRole,
            'currentShiftPreference' => $currentShiftPreference,
            'nurseRoleId' => $userRole && strtolower($userRole['name']) === 'nurse' ? $userRole['id'] : null,
        ];

        return view('admin/users/edit', $data);
    }

    public function update($id)
    {
        // Prevent updating yourself
        if ($id == session()->get('user_id')) {
            return redirect()->to('/admin/users')->with('error', 'You cannot edit your own account.');
        }

        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found.');
        }

        $db = \Config\Database::connect();
        
        // Get role name to check if it's nurse
        $roleId = $this->request->getPost('role_id');
        $role = $db->table('roles')->where('id', $roleId)->get()->getRowArray();
        $isNurse = $role && strtolower($role['name']) === 'nurse';

        $validationRules = [
            'username' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email',
            'role_id' => 'required|integer|greater_than[0]',
            'status' => 'required|in_list[active,inactive]',
        ];
        
        // Add shift preference validation if role is nurse
        if ($isNurse) {
            $validationRules['shift_preference'] = 'required|in_list[morning,night,bulk]';
        }

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
            // If nurse, update schedules based on shift preference
            if ($isNurse && $this->request->getPost('shift_preference')) {
                $this->updateNurseSchedules($id, $this->request->getPost('shift_preference'));
            }
            
            return redirect()->to('/admin/users')->with('success', 'User updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update user.');
        }
    }
    
    /**
     * Update nurse schedules based on shift preference
     */
    private function updateNurseSchedules($nurseId, $shiftPreference)
    {
        $db = \Config\Database::connect();
        
        if (!$db->tableExists('nurse_schedules')) {
            return;
        }
        
        // Get existing schedules for the next 30 days
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+30 days'));
        
        // Determine which shifts should exist
        $shouldHaveMorning = ($shiftPreference === 'morning' || $shiftPreference === 'bulk');
        $shouldHaveNight = ($shiftPreference === 'night' || $shiftPreference === 'bulk');
        
        // Get existing schedules
        $existingSchedules = $db->table('nurse_schedules')
            ->where('nurse_id', $nurseId)
            ->where('shift_date >=', $startDate)
            ->where('shift_date <=', $endDate)
            ->get()
            ->getResultArray();
        
        $currentDate = $startDate;
        $schedulesToAdd = [];
        
        while ($currentDate <= $endDate) {
            // Skip weekends
            $dayOfWeek = date('w', strtotime($currentDate));
            if ($dayOfWeek == 0 || $dayOfWeek == 6) {
                $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
                continue;
            }
            
            // Check existing schedules for this date
            $hasMorning = false;
            $hasNight = false;
            foreach ($existingSchedules as $schedule) {
                if ($schedule['shift_date'] === $currentDate) {
                    if ($schedule['shift_type'] === 'morning') {
                        $hasMorning = true;
                    } elseif ($schedule['shift_type'] === 'night') {
                        $hasNight = true;
                    }
                }
            }
            
            // Add morning shift if needed
            if ($shouldHaveMorning && !$hasMorning) {
                $schedulesToAdd[] = [
                    'nurse_id' => $nurseId,
                    'shift_date' => $currentDate,
                    'shift_type' => 'morning',
                    'start_time' => '06:00:00',
                    'end_time' => '12:00:00',
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
            
            // Add night shift if needed
            if ($shouldHaveNight && !$hasNight) {
                $schedulesToAdd[] = [
                    'nurse_id' => $nurseId,
                    'shift_date' => $currentDate,
                    'shift_type' => 'night',
                    'start_time' => '18:00:00',
                    'end_time' => '00:00:00',
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
            
            // Cancel/remove shifts that shouldn't exist
            if (!$shouldHaveMorning && $hasMorning) {
                $db->table('nurse_schedules')
                    ->where('nurse_id', $nurseId)
                    ->where('shift_date', $currentDate)
                    ->where('shift_type', 'morning')
                    ->update(['status' => 'cancelled']);
            }
            
            if (!$shouldHaveNight && $hasNight) {
                $db->table('nurse_schedules')
                    ->where('nurse_id', $nurseId)
                    ->where('shift_date', $currentDate)
                    ->where('shift_type', 'night')
                    ->update(['status' => 'cancelled']);
            }
            
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }
        
        // Insert new schedules
        if (!empty($schedulesToAdd)) {
            $batchSize = 50;
            for ($i = 0; $i < count($schedulesToAdd); $i += $batchSize) {
                $batch = array_slice($schedulesToAdd, $i, $batchSize);
                $db->table('nurse_schedules')->insertBatch($batch);
            }
        }
    }

    public function delete($id)
    {
        // Prevent deleting yourself
        if ($id == session()->get('user_id')) {
            return redirect()->to('/admin/users')->with('error', 'You cannot delete your own account.');
        }

        $user = $this->userModel->withDeleted()->find($id);
        
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found.');
        }

        // Check if already deleted
        if (!empty($user['deleted_at'])) {
            return redirect()->to('/admin/users')->with('error', 'User is already deleted.');
        }

        // Mark as deleted (soft delete) instead of hard delete
        $data = [
            'deleted_at' => date('Y-m-d H:i:s'),
            'status' => 'inactive', // Also set status to inactive
        ];

        if ($this->userModel->update($id, $data)) {
            return redirect()->to('/admin/users')->with('success', 'User marked as deleted successfully.');
        } else {
            return redirect()->to('/admin/users')->with('error', 'Failed to delete user.');
        }
    }
}

