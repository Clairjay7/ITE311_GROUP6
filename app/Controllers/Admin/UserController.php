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
        
        // Check if doctors table has user_id column
        $hasUserIdColumn = false;
        if ($db->tableExists('doctors')) {
            $fields = $db->getFieldData('doctors');
            foreach ($fields as $field) {
                if ($field->name === 'user_id') {
                    $hasUserIdColumn = true;
                    break;
                }
            }
        }
        
        // Get all users with their roles and doctor specialization (including deleted users)
        $query = $db->table('users')
            ->select('users.*, roles.name as role_name, roles.description as role_description')
            ->join('roles', 'roles.id = users.role_id', 'left');
        
        // Join with doctors table if user_id column exists
        if ($hasUserIdColumn) {
            $query->select('doctors.specialization as doctor_specialization, doctors.doctor_name')
                  ->join('doctors', 'doctors.user_id = users.id', 'left');
        }
        
        $users = $query->orderBy('users.deleted_at', 'ASC') // Show non-deleted first
            ->orderBy('users.created_at', 'DESC')
            ->get()->getResultArray();
        
        // If user_id column doesn't exist, try to match by doctor name from username
        if (!$hasUserIdColumn && $db->tableExists('doctors')) {
            foreach ($users as &$user) {
                if (strtolower($user['role_name'] ?? '') === 'doctor') {
                    // Try to find doctor by matching username pattern
                    $username = strtolower($user['username']);
                    // Remove 'dr.' prefix if present
                    $namePart = preg_replace('/^dr\./', '', $username);
                    $namePart = str_replace('.', ' ', $namePart);
                    
                    // Try to find matching doctor
                    $doctor = $db->table('doctors')
                        ->where('LOWER(REPLACE(REPLACE(doctor_name, "Dr. ", ""), " ", ""))', str_replace(' ', '', $namePart))
                        ->orLike('doctor_name', $namePart)
                        ->get()
                        ->getRowArray();
                    
                    if ($doctor) {
                        $user['doctor_specialization'] = $doctor['specialization'] ?? null;
                        $user['doctor_name'] = $doctor['doctor_name'] ?? null;
                    }
                }
            }
            unset($user);
        }

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
        
        // Get doctor role ID for JavaScript
        $doctorRole = $db->table('roles')
            ->where('LOWER(name)', 'doctor')
            ->get()->getRowArray();
        
        // Get available specializations from doctors table
        $specializations = [];
        if ($db->tableExists('doctors')) {
            $specializations = $db->table('doctors')
                ->select('specialization')
                ->distinct()
                ->where('specialization IS NOT NULL')
                ->where('specialization !=', '')
                ->orderBy('specialization', 'ASC')
                ->get()
                ->getResultArray();
            $specializations = array_column($specializations, 'specialization');
        }
        
        // If no specializations found, use default ones from seeder
        if (empty($specializations)) {
            $specializations = [
                'Internal Medicine',
                'Pediatrics',
                'Family Medicine',
                'Obstetrics and Gynecology',
                'General Surgery'
            ];
        }

        $data = [
            'title' => 'Add New User',
            'roles' => $roles,
            'nurseRoleId' => $nurseRole ? $nurseRole['id'] : null,
            'doctorRoleId' => $doctorRole ? $doctorRole['id'] : null,
            'specializations' => $specializations,
        ];

        return view('admin/users/create', $data);
    }

    public function store()
    {
        $db = \Config\Database::connect();
        
        // Get role name to check role-specific requirements
        $roleId = $this->request->getPost('role_id');
        $role = $db->table('roles')->where('id', $roleId)->get()->getRowArray();
        $roleName = $role ? strtolower($role['name']) : '';
        $isNurse = $roleName === 'nurse';
        $isDoctor = $roleName === 'doctor';
        $isLabStaff = $roleName === 'lab_staff';
        $isPharmacy = $roleName === 'pharmacy';
        $needsEmployeeId = in_array($roleName, ['admin', 'finance', 'itstaff', 'receptionist']);
        $needsPRCLicense = in_array($roleName, ['doctor', 'lab_staff', 'pharmacy']);
        $needsNursingLicense = $isNurse;
        
        $validationRules = [
            'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'role_id' => 'required|integer|greater_than[0]',
            'status' => 'required|in_list[active,inactive]',
            'first_name' => 'permit_empty|max_length[100]',
            'middle_name' => 'permit_empty|max_length[100]',
            'last_name' => 'permit_empty|max_length[100]',
            'contact' => 'permit_empty|max_length[20]',
            'address' => 'permit_empty|max_length[255]',
        ];
        
        // Add role-specific validations
        if ($needsEmployeeId) {
            $validationRules['employee_id'] = 'required|max_length[50]';
        }
        
        if ($needsPRCLicense) {
            $validationRules['prc_license'] = 'required|max_length[50]';
        }
        
        if ($needsNursingLicense) {
            $validationRules['nursing_license'] = 'required|max_length[50]';
        }
        
        // Add specialization validation if role is doctor
        if ($isDoctor) {
            $validationRules['specialization'] = 'required|max_length[100]';
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
            'first_name' => $this->request->getPost('first_name') ?: null,
            'middle_name' => $this->request->getPost('middle_name') ?: null,
            'last_name' => $this->request->getPost('last_name') ?: null,
            'contact' => $this->request->getPost('contact') ?: null,
            'address' => $this->request->getPost('address') ?: null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        
        // Add role-specific fields
        if ($needsEmployeeId) {
            $data['employee_id'] = $this->request->getPost('employee_id');
        }
        
        if ($needsPRCLicense) {
            $data['prc_license'] = $this->request->getPost('prc_license');
        }
        
        if ($needsNursingLicense) {
            $data['nursing_license'] = $this->request->getPost('nursing_license');
        }
        
        if ($isDoctor) {
            $data['specialization'] = $this->request->getPost('specialization');
        }

        if ($this->userModel->insert($data)) {
            $userId = $this->userModel->getInsertID();
            
            // If doctor role, create doctor record and schedule
            if ($isDoctor && $db->tableExists('doctors')) {
                $specialization = $this->request->getPost('specialization');
                
                // Use first_name and last_name if available, otherwise extract from username
                $firstName = $this->request->getPost('first_name');
                $lastName = $this->request->getPost('last_name');
                
                if ($firstName && $lastName) {
                    $doctorName = 'Dr. ' . trim($firstName . ' ' . ($this->request->getPost('middle_name') ? $this->request->getPost('middle_name') . ' ' : '') . $lastName);
                } else {
                    // Fallback: Try to extract name from username
                    $username = strtolower($this->request->getPost('username'));
                    $namePart = preg_replace('/^dr\./', '', $username);
                    $namePart = str_replace('.', ' ', $namePart);
                    $namePart = ucwords($namePart);
                    $doctorName = 'Dr. ' . $namePart;
                }
                
                $doctorData = [
                    'doctor_name' => $doctorName,
                    'specialization' => $specialization,
                    'user_id' => $userId,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                
                $db->table('doctors')->insert($doctorData);
            }
            
            // If nurse, create schedule from form data
            if ($isNurse) {
                $workingDays = $this->request->getPost('working_days');
                if ($workingDays && is_array($workingDays) && !empty($workingDays)) {
                    $this->createNurseScheduleFromForm($userId, $workingDays);
                } else {
                    // Fallback: create initial schedules based on shift preference (old method)
                    $shiftPreference = $this->request->getPost('shift_preference');
                    if ($shiftPreference) {
                        $this->createNurseSchedules($userId, $shiftPreference);
                    }
                }
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
        
        // Get nurse role ID for JavaScript
        $nurseRole = $db->table('roles')
            ->where('LOWER(name)', 'nurse')
            ->get()->getRowArray();
        
        // Get doctor role ID for JavaScript
        $doctorRole = $db->table('roles')
            ->where('LOWER(name)', 'doctor')
            ->get()->getRowArray();

        $data = [
            'title' => 'Edit User',
            'user' => $user,
            'roles' => $roles,
            'userRole' => $userRole,
            'nurseRoleId' => $nurseRole ? $nurseRole['id'] : null,
            'doctorRoleId' => $doctorRole ? $doctorRole['id'] : null,
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
        
        // Get role name to check role-specific requirements
        $roleId = $this->request->getPost('role_id');
        $role = $db->table('roles')->where('id', $roleId)->get()->getRowArray();
        $roleName = $role ? strtolower($role['name']) : '';
        $isNurse = $roleName === 'nurse';
        $isDoctor = $roleName === 'doctor';
        $isLabStaff = $roleName === 'lab_staff';
        $isPharmacy = $roleName === 'pharmacy';
        $needsEmployeeId = in_array($roleName, ['admin', 'finance', 'itstaff', 'receptionist']);
        $needsPRCLicense = in_array($roleName, ['doctor', 'lab_staff', 'pharmacy']);
        $needsNursingLicense = $isNurse;

        $validationRules = [
            'username' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email',
            'role_id' => 'required|integer|greater_than[0]',
            'status' => 'required|in_list[active,inactive]',
            'first_name' => 'permit_empty|max_length[100]',
            'middle_name' => 'permit_empty|max_length[100]',
            'last_name' => 'permit_empty|max_length[100]',
            'contact' => 'permit_empty|max_length[20]',
            'address' => 'permit_empty|max_length[255]',
        ];
        
        // Add role-specific validations
        if ($needsEmployeeId) {
            $validationRules['employee_id'] = 'required|max_length[50]';
        }
        
        if ($needsPRCLicense) {
            $validationRules['prc_license'] = 'required|max_length[50]';
        }
        
        if ($needsNursingLicense) {
            $validationRules['nursing_license'] = 'required|max_length[50]';
        }
        
        // Add specialization validation if role is doctor
        if ($isDoctor) {
            $validationRules['specialization'] = 'required|max_length[100]';
        }
        
        // Add shift preference validation if role is nurse (for backward compatibility)
        if ($isNurse) {
            $validationRules['shift_preference'] = 'permit_empty|in_list[morning,night,bulk]';
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
            'first_name' => $this->request->getPost('first_name') ?: null,
            'middle_name' => $this->request->getPost('middle_name') ?: null,
            'last_name' => $this->request->getPost('last_name') ?: null,
            'contact' => $this->request->getPost('contact') ?: null,
            'address' => $this->request->getPost('address') ?: null,
        ];
        
        // Add role-specific fields
        if ($needsEmployeeId) {
            $data['employee_id'] = $this->request->getPost('employee_id');
        } else {
            $data['employee_id'] = null;
        }
        
        if ($needsPRCLicense) {
            $data['prc_license'] = $this->request->getPost('prc_license');
        } else {
            $data['prc_license'] = null;
        }
        
        if ($needsNursingLicense) {
            $data['nursing_license'] = $this->request->getPost('nursing_license');
        } else {
            $data['nursing_license'] = null;
        }
        
        if ($isDoctor) {
            $data['specialization'] = $this->request->getPost('specialization');
        } else {
            $data['specialization'] = null;
        }

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

    public function restore($id)
    {
        // Check if user is logged in and is admin
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as admin to access this page.');
        }

        $user = $this->userModel->withDeleted()->find($id);
        
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found.');
        }

        // Check if user is deleted
        if (empty($user['deleted_at'])) {
            return redirect()->to('/admin/users')->with('error', 'User is not deleted.');
        }

        // Restore user (remove deleted_at and set status to active)
        $data = [
            'deleted_at' => null,
            'status' => 'active',
        ];

        if ($this->userModel->update($id, $data)) {
            return redirect()->to('/admin/users')->with('success', 'User restored successfully.');
        } else {
            return redirect()->to('/admin/users')->with('error', 'Failed to restore user.');
        }
    }

    /**
     * Create nurse schedule from form data
     */
    private function createNurseScheduleFromForm($nurseId, $workingDays)
    {
        $db = \Config\Database::connect();
        
        if (!$db->tableExists('nurse_schedules')) {
            return;
        }
        
        $shiftType = $this->request->getPost('nurse_shift_type') ?: $this->request->getPost('shift_type');
        $timeIn = $this->request->getPost('nurse_time_in') ?: $this->request->getPost('time_in');
        $timeOut = $this->request->getPost('nurse_time_out') ?: $this->request->getPost('time_out');
        $dutyType = $this->request->getPost('duty_type') ?: 'regular';
        $standby = $this->request->getPost('standby') ?: 'no';
        $stationAssignment = $this->request->getPost('station_assignment');
        $status = $this->request->getPost('schedule_status') ?: 'active';
        
        // Generate schedules for 1 year starting from today's date
        $startDate = new \DateTime(); // Today's date
        $endDate = new \DateTime(); // Today's date
        $endDate->modify('+1 year'); // 1 year from today
        
        $schedulesToInsert = [];
        $currentDate = clone $startDate;
        
        while ($currentDate <= $endDate) {
            $dayName = $currentDate->format('l'); // Full day name
            
            // Check if this day is in working days
            if (in_array($dayName, $workingDays)) {
                $schedulesToInsert[] = [
                    'nurse_id' => $nurseId,
                    'shift_date' => $currentDate->format('Y-m-d'),
                    'shift_type' => $shiftType ?: 'morning',
                    'start_time' => $timeIn ?: '06:00:00',
                    'end_time' => $timeOut ?: '14:00:00',
                    'duty_type' => $dutyType,
                    'standby' => $standby,
                    'station_assignment' => $stationAssignment,
                    'status' => $status,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
            
            $currentDate->modify('+1 day');
        }
        
        // Batch insert schedules
        if (!empty($schedulesToInsert)) {
            $chunks = array_chunk($schedulesToInsert, 100);
            foreach ($chunks as $chunk) {
                $db->table('nurse_schedules')->insertBatch($chunk);
            }
        }
    }
    
    /**
     * Get doctor schedule (AJAX endpoint)
     */
    public function getDoctorSchedule()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request'])->setStatusCode(400);
        }
        
        $doctorId = $this->request->getGet('doctor_id');
        
        if (!$doctorId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Doctor ID is required']);
        }
        
        $db = \Config\Database::connect();
        $schedules = [];
        
        if ($db->tableExists('doctor_schedules')) {
            $schedules = $db->table('doctor_schedules')
                ->select('shift_date, start_time, end_time, status')
                ->where('doctor_id', $doctorId)
                ->where('shift_date >=', date('Y-m-d'))
                ->where('shift_date <=', date('Y-m-d', strtotime('+30 days')))
                ->where('status !=', 'cancelled')
                ->orderBy('shift_date', 'ASC')
                ->orderBy('start_time', 'ASC')
                ->limit(20)
                ->get()
                ->getResultArray();
        }
        
        return $this->response->setJSON([
            'success' => true,
            'schedules' => $schedules,
            'message' => count($schedules) > 0 ? '' : 'No schedule found'
        ]);
    }
}

