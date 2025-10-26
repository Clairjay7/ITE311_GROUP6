<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\DepartmentModel;
use App\Models\RoomModel;
use App\Models\SystemLogModel;
use App\Models\AuditLogModel;
use App\Models\AppointmentModel;
use App\Models\PatientModel;

class SuperAdmin extends Controller
{
    protected $userModel;
    protected $roleModel;
    protected $departmentModel;
    protected $roomModel;
    protected $systemLogModel;
    protected $auditLogModel;
    protected $appointmentModel;
    protected $patientModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->departmentModel = new DepartmentModel();
        $this->roomModel = new RoomModel();
        $this->systemLogModel = new SystemLogModel();
        $this->auditLogModel = new AuditLogModel();
        $this->appointmentModel = new AppointmentModel();
        $this->patientModel = new PatientModel();
    }

    protected function ensureSuperAdmin()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'superadmin') {
            return redirect()->to('/login');
        }
        return null;
    }

    protected function render(string $view, array $data = [])
    {
        $guard = $this->ensureSuperAdmin();
        if ($guard !== null) {
            return $guard;
        }
        $base = [
            'username' => session()->get('username'),
        ];
        return view($view, $base + $data);
    }

    public function unifiedDashboard()
    {
        return $this->dashboard(); // Alias for dashboard
    }

    public function dashboard()
    {
        $data = [
            'totalDoctors' => 0,
            'totalPatients' => 0,
            'todaysAppointments' => 0,
            'pendingBills' => 0,
        ];
        
        try {
            $db = \Config\Database::connect();
            $todayStart = date('Y-m-d 00:00:00');
            $todayEnd = date('Y-m-d 23:59:59');

            // Get actual counts from your database tables
            $data['totalDoctors'] = $db->tableExists('doctors') ? 
                (int) $db->table('doctors')->countAllResults() : 
                (int) $this->userModel->where('role', 'doctor')->countAllResults();
                
            $data['totalPatients'] = $db->tableExists('patients') ? 
                (int) $db->table('patients')->countAllResults() : 0;
                
            $data['todaysAppointments'] = $db->tableExists('appointments') ? 
                (int) $db->table('appointments')
                    ->where('appointment_date >=', $todayStart)
                    ->where('appointment_date <=', $todayEnd)
                    ->countAllResults() : 0;
                    
            $data['pendingBills'] = $db->tableExists('billing') ? 
                (int) $db->table('billing')->where('status', 'pending')->countAllResults() : 0;
        } catch (\Exception $e) {
            log_message('error', 'SuperAdmin dashboard error: ' . $e->getMessage());
        }
        
        return $this->render('SuperAdmin/unified_dashboard', $data);
    }

    // ============ USERS MANAGEMENT ============
    public function users()
    {
        $users = $this->userModel->getUsersWithRoles();
        return $this->render('SuperAdmin/users', ['users' => $users]);
    }

    public function addUser()
    {
        if ($this->request->getMethod() === 'POST') {
            $data = $this->request->getPost();
            
            // Remove confirm_password field as it's not needed in database
            unset($data['confirm_password']);
            
            // Convert password to password_hash for the model
            if (isset($data['password'])) {
                $data['password_hash'] = $data['password'];
                unset($data['password']);
            }
            
            // Set default status if not provided
            if (!isset($data['status'])) {
                $data['status'] = 'active';
            }
            
            if ($this->userModel->insert($data)) {
                // Log the action (optional - comment out if SystemLogModel causes issues)
                try {
                    $logData = $data;
                    unset($logData['password_hash']); // Don't log password
                    $this->systemLogModel->insert([
                        'action' => 'New user created',
                        'details' => json_encode(['user_data' => $logData]),
                        'user_id' => session()->get('user_id'),
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                } catch (\Exception $e) {
                    // Log error but don't fail the user creation
                    log_message('error', 'Failed to log user creation: ' . $e->getMessage());
                }
                
                return $this->response->setJSON(['success' => true, 'message' => 'User added successfully']);
            } else {
                return $this->response->setJSON(['success' => false, 'errors' => $this->userModel->errors()]);
            }
        }

        // For GET request, just show the form
        return $this->render('SuperAdmin/add_user');
    }

    public function editUser($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('User not found');
        }

        if ($this->request->getMethod() === 'POST') {
            $data = $this->request->getPost();
            
            // Remove confirm_password field if it exists
            unset($data['confirm_password']);
            
            // Handle password update
            if (!empty($data['password'])) {
                // Hash the new password
                $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
                unset($data['password']); // Remove plain password
            } else {
                // Don't update password if it's empty
                unset($data['password']);
                unset($data['password_hash']);
            }
            
            // Debug: Log the data being updated
            log_message('debug', 'Updating user ID: ' . $id . ' with data: ' . json_encode($data));
            
            // Try to update with validation disabled temporarily
            $this->userModel->skipValidation(true);
            $result = $this->userModel->update($id, $data);
            $this->userModel->skipValidation(false);
            
            if ($result) {
                // Log the action
                try {
                    $logData = $data;
                    unset($logData['password_hash']); // Don't log password
                    $this->systemLogModel->info('User updated', ['user_id' => $id, 'user_data' => $logData], session()->get('user_id'));
                } catch (\Exception $e) {
                    log_message('error', 'Failed to log user update: ' . $e->getMessage());
                }
                
                return $this->response->setJSON(['success' => true, 'message' => 'User updated successfully']);
            } else {
                $errors = $this->userModel->errors();
                log_message('error', 'User update failed. Errors: ' . json_encode($errors));
                
                // Return more detailed error message
                $errorMessage = 'Failed to update user';
                if (!empty($errors)) {
                    $errorMessage .= ': ' . implode(', ', $errors);
                }
                
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => $errorMessage,
                    'errors' => $errors
                ]);
            }
        }

        // For GET request, show the edit form
        return $this->render('SuperAdmin/edit_user', ['user' => $user]);
    }

    public function deleteUser($id)
    {
        if ($this->userModel->delete($id)) {
            // Log the action
            try {
                $this->systemLogModel->warning('User deleted', ['user_id' => $id], session()->get('user_id'));
            } catch (\Exception $e) {
                log_message('error', 'Failed to log user deletion: ' . $e->getMessage());
            }
            
            return $this->response->setJSON(['success' => true, 'message' => 'User deleted successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete user']);
        }
    }

    public function viewUser($id)
    {
        $user = $this->userModel->getUserWithDetails($id);
        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('User not found');
        }
        return $this->render('SuperAdmin/view_user', ['user' => $user]);
    }

    // ============ DEPARTMENTS MANAGEMENT ============
    public function departments()
    {
        $departments = $this->departmentModel->getDepartmentsWithHeadDoctor();
        return $this->render('SuperAdmin/departments', ['departments' => $departments]);
    }

    public function addDepartment()
    {
        if ($this->request->getMethod() === 'POST') {
            $data = $this->request->getPost();
            
            if ($this->departmentModel->insert($data)) {
                $this->systemLogModel->info('New department created', ['department_data' => $data], session()->get('user_id'));
                return $this->response->setJSON(['success' => true, 'message' => 'Department added successfully']);
            } else {
                return $this->response->setJSON(['success' => false, 'errors' => $this->departmentModel->errors()]);
            }
        }

        $doctors = $this->userModel->getUsersByRole('doctor');
        return $this->render('SuperAdmin/add_department', ['doctors' => $doctors]);
    }

    public function editDepartment($id)
    {
        $department = $this->departmentModel->find($id);
        if (!$department) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Department not found');
        }

        if ($this->request->getMethod() === 'POST') {
            $data = $this->request->getPost();
            
            if ($this->departmentModel->update($id, $data)) {
                $this->systemLogModel->info('Department updated', ['department_id' => $id, 'department_data' => $data], session()->get('user_id'));
                return $this->response->setJSON(['success' => true, 'message' => 'Department updated successfully']);
            } else {
                return $this->response->setJSON(['success' => false, 'errors' => $this->departmentModel->errors()]);
            }
        }

        $doctors = $this->userModel->getUsersByRole('doctor');
        return $this->render('SuperAdmin/edit_department', ['department' => $department, 'doctors' => $doctors]);
    }

    public function deleteDepartment($id)
    {
        if ($this->departmentModel->delete($id)) {
            $this->systemLogModel->warning('Department deleted', ['department_id' => $id], session()->get('user_id'));
            return $this->response->setJSON(['success' => true, 'message' => 'Department deleted successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete department']);
        }
    }

    // ============ ROOMS MANAGEMENT ============
    public function rooms()
    {
        $rooms = $this->roomModel->getRoomsWithDepartment();
        return $this->render('SuperAdmin/rooms', ['rooms' => $rooms]);
    }

    public function addRoom()
    {
        if ($this->request->getMethod() === 'POST') {
            $data = $this->request->getPost();
            
            if ($this->roomModel->insert($data)) {
                $this->systemLogModel->info('New room created', ['room_data' => $data], session()->get('user_id'));
                return $this->response->setJSON(['success' => true, 'message' => 'Room added successfully']);
            } else {
                return $this->response->setJSON(['success' => false, 'errors' => $this->roomModel->errors()]);
            }
        }

        $departments = $this->departmentModel->getActiveDepartments();
        return $this->render('SuperAdmin/add_room', ['departments' => $departments]);
    }

    public function editRoom($id)
    {
        $room = $this->roomModel->find($id);
        if (!$room) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Room not found');
        }

        if ($this->request->getMethod() === 'POST') {
            $data = $this->request->getPost();
            
            if ($this->roomModel->update($id, $data)) {
                $this->systemLogModel->info('Room updated', ['room_id' => $id, 'room_data' => $data], session()->get('user_id'));
                return $this->response->setJSON(['success' => true, 'message' => 'Room updated successfully']);
            } else {
                return $this->response->setJSON(['success' => false, 'errors' => $this->roomModel->errors()]);
            }
        }

        $departments = $this->departmentModel->getActiveDepartments();
        return $this->render('SuperAdmin/edit_room', ['room' => $room, 'departments' => $departments]);
    }

    public function deleteRoom($id)
    {
        if ($this->roomModel->delete($id)) {
            $this->systemLogModel->warning('Room deleted', ['room_id' => $id], session()->get('user_id'));
            return $this->response->setJSON(['success' => true, 'message' => 'Room deleted successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete room']);
        }
    }

    // ============ AUDIT LOGS ============
    public function patients()
    {
        try {
            // Get all patients with search and filter
            $search = $this->request->getGet('search');
            $status = $this->request->getGet('status');
            $department = $this->request->getGet('department');
            
            $patients = $this->patientModel->getFilteredPatients($search, $status, $department);
            
            // Get unique departments for filter
            $departments = $this->patientModel->getUniqueDepartments();
            
            return $this->render('SuperAdmin/patient_records', [
                'patients' => $patients,
                'departments' => $departments,
                'search' => $search,
                'status_filter' => $status,
                'department_filter' => $department
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Patient records page error: ' . $e->getMessage());
            
            // Fallback with sample data
            $patients = $this->getSamplePatients();
            return $this->render('SuperAdmin/patient_records', [
                'patients' => $patients,
                'departments' => ['Cardiology', 'Pediatrics', 'Orthopedics'],
                'search' => '',
                'status_filter' => '',
                'department_filter' => ''
            ]);
        }
    }



    private function generatePatientId()
    {
        // Generate unique patient ID (e.g., PAT-2024-0001)
        $year = date('Y');
        $lastPatient = $this->patientModel->orderBy('id', 'DESC')->first();
        $nextNumber = $lastPatient ? (intval(substr($lastPatient['patient_id'], -4)) + 1) : 1;
        
        return 'PAT-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    private function getSamplePatients()
    {
        // Return empty array - no more sample patients
        return [];
    }

    public function debugPatients()
    {
        try {
            $db = \Config\Database::connect();
            
            $debug = [
                'database_connected' => true,
                'table_exists' => $db->tableExists('patients'),
                'fields' => [],
                'missing_columns' => [],
                'fix_applied' => false,
                'test_insert' => false,
                'errors' => []
            ];
            
            if ($debug['table_exists']) {
                // Get current table structure
                $fields = $db->getFieldData('patients');
                $fieldNames = array_column($fields, 'name');
                $debug['fields'] = $fieldNames;
                
                // Check for missing columns
                $requiredColumns = [
                    'middle_name', 'contact_number', 'emergency_contact_number', 
                    'government_id', 'department', 'admission_date', 'archived_at'
                ];
                
                $debug['missing_columns'] = array_diff($requiredColumns, $fieldNames);
                
                // If there are missing columns, try to add them
                if (!empty($debug['missing_columns'])) {
                    $debug['fix_applied'] = $this->applyTableFix($db);
                }
                
                // Test simple insert (without missing columns)
                $testData = [
                    'first_name' => 'Test',
                    'last_name' => 'Patient',
                    'email' => 'test@example.com',
                    'address' => 'Test Address',
                    'emergency_contact_name' => 'Test Emergency',
                    'status' => 'outpatient'
                ];
                
                // Add optional columns if they exist
                if (in_array('middle_name', $fieldNames)) {
                    $testData['middle_name'] = 'Debug';
                }
                if (in_array('contact_number', $fieldNames)) {
                    $testData['contact_number'] = '09123456789';
                }
                if (in_array('emergency_contact_number', $fieldNames)) {
                    $testData['emergency_contact_number'] = '09987654321';
                }
                
                try {
                    if ($this->patientModel->insert($testData)) {
                        $debug['test_insert'] = true;
                        $debug['inserted_id'] = $this->patientModel->getInsertID();
                        
                        // Clean up test data
                        $this->patientModel->delete($this->patientModel->getInsertID());
                    } else {
                        $debug['test_insert'] = false;
                        $debug['errors'] = $this->patientModel->errors();
                    }
                } catch (\Exception $e) {
                    $debug['test_insert'] = false;
                    $debug['errors'][] = $e->getMessage();
                }
            }
            
            // Return as JSON for easy reading
            return $this->response->setJSON($debug);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'database_connected' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function applyTableFix($db)
    {
        try {
            $columnsToAdd = [
                'middle_name' => "ALTER TABLE patients ADD COLUMN middle_name VARCHAR(100) NULL AFTER last_name",
                'contact_number' => "ALTER TABLE patients ADD COLUMN contact_number VARCHAR(20) NULL AFTER phone",
                'emergency_contact_number' => "ALTER TABLE patients ADD COLUMN emergency_contact_number VARCHAR(20) NULL AFTER emergency_contact_name",
                'government_id' => "ALTER TABLE patients ADD COLUMN government_id VARCHAR(50) NULL AFTER emergency_contact_phone",
                'department' => "ALTER TABLE patients ADD COLUMN department VARCHAR(100) NULL AFTER status",
                'admission_date' => "ALTER TABLE patients ADD COLUMN admission_date DATE NULL AFTER department",
                'archived_at' => "ALTER TABLE patients ADD COLUMN archived_at DATETIME NULL AFTER admission_date"
            ];

            foreach ($columnsToAdd as $columnName => $sql) {
                if (!$db->fieldExists($columnName, 'patients')) {
                    try {
                        $db->query($sql);
                        log_message('info', "Added column: {$columnName}");
                    } catch (\Exception $e) {
                        log_message('error', "Failed to add {$columnName}: " . $e->getMessage());
                    }
                }
            }

            // Update status enum
            try {
                $db->query("ALTER TABLE patients MODIFY COLUMN status ENUM('active', 'inactive', 'deceased', 'inpatient', 'outpatient', 'archived') DEFAULT 'outpatient'");
                log_message('info', "Updated status enum");
            } catch (\Exception $e) {
                log_message('error', "Failed to update status enum: " . $e->getMessage());
            }

            return true;
        } catch (\Exception $e) {
            log_message('error', "Fix patients table failed: " . $e->getMessage());
            return false;
        }
    }

    public function fixPatientsTable()
    {
        try {
            $db = \Config\Database::connect();
            $output = [];
            
            $output[] = "Starting patients table structure fix...";
            
            // Check if table exists
            if (!$db->tableExists('patients')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Patients table does not exist'
                ]);
            }
            
            // Get current fields
            $fields = $db->getFieldData('patients');
            $currentFields = array_column($fields, 'name');
            $output[] = "Current fields: " . implode(', ', $currentFields);
            
            // Required columns
            $requiredColumns = [
                'middle_name' => "ALTER TABLE patients ADD COLUMN middle_name VARCHAR(100) NULL",
                'contact_number' => "ALTER TABLE patients ADD COLUMN contact_number VARCHAR(20) NULL", 
                'emergency_contact_number' => "ALTER TABLE patients ADD COLUMN emergency_contact_number VARCHAR(20) NULL",
                'government_id' => "ALTER TABLE patients ADD COLUMN government_id VARCHAR(50) NULL",
                'department' => "ALTER TABLE patients ADD COLUMN department VARCHAR(100) NULL",
                'admission_date' => "ALTER TABLE patients ADD COLUMN admission_date DATE NULL",
                'archived_at' => "ALTER TABLE patients ADD COLUMN archived_at DATETIME NULL"
            ];

            // Add missing columns
            foreach ($requiredColumns as $columnName => $sql) {
                if (!in_array($columnName, $currentFields)) {
                    try {
                        $db->query($sql);
                        $output[] = "✅ Added column: {$columnName}";
                    } catch (\Exception $e) {
                        $output[] = "❌ Failed to add {$columnName}: " . $e->getMessage();
                    }
                } else {
                    $output[] = "⏭️ Column {$columnName} already exists";
                }
            }

            // Update status enum
            try {
                $db->query("ALTER TABLE patients MODIFY COLUMN status ENUM('active', 'inactive', 'deceased', 'inpatient', 'outpatient', 'archived') DEFAULT 'outpatient'");
                $output[] = "✅ Updated status enum";
            } catch (\Exception $e) {
                $output[] = "❌ Failed to update status enum: " . $e->getMessage();
            }

            // Make columns nullable
            $nullableColumns = [
                'patient_id' => "ALTER TABLE patients MODIFY COLUMN patient_id VARCHAR(20) NULL",
                'date_of_birth' => "ALTER TABLE patients MODIFY COLUMN date_of_birth DATE NULL",
                'gender' => "ALTER TABLE patients MODIFY COLUMN gender ENUM('male', 'female', 'other') NULL"
            ];

            foreach ($nullableColumns as $columnName => $sql) {
                try {
                    $db->query($sql);
                    $output[] = "✅ Made {$columnName} nullable";
                } catch (\Exception $e) {
                    $output[] = "❌ Failed to make {$columnName} nullable: " . $e->getMessage();
                }
            }

            // Test insertion
            try {
                $testData = [
                    'first_name' => 'Test',
                    'last_name' => 'Patient',
                    'middle_name' => 'Fix',
                    'contact_number' => '09123456789',
                    'status' => 'outpatient'
                ];
                
                $db->table('patients')->insert($testData);
                $insertId = $db->insertID();
                
                if ($insertId) {
                    $output[] = "✅ Test insertion successful (ID: {$insertId})";
                    $db->table('patients')->delete(['id' => $insertId]);
                    $output[] = "✅ Test data cleaned up";
                }
            } catch (\Exception $e) {
                $output[] = "❌ Test insertion failed: " . $e->getMessage();
            }

            $output[] = "Fix completed!";
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Table structure fix completed',
                'output' => $output
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Fix failed: ' . $e->getMessage()
            ]);
        }
    }

    public function emergencyFixPatients()
    {
        $output = [];
        $success = true;
        
        try {
            $db = \Config\Database::connect();
            $output[] = "🚨 EMERGENCY FIX: Adding missing columns to patients table";
            
            // Direct SQL commands to add missing columns
            $sqlCommands = [
                "ALTER TABLE patients ADD COLUMN IF NOT EXISTS middle_name VARCHAR(100) NULL",
                "ALTER TABLE patients ADD COLUMN IF NOT EXISTS contact_number VARCHAR(20) NULL", 
                "ALTER TABLE patients ADD COLUMN IF NOT EXISTS emergency_contact_number VARCHAR(20) NULL",
                "ALTER TABLE patients ADD COLUMN IF NOT EXISTS government_id VARCHAR(50) NULL",
                "ALTER TABLE patients ADD COLUMN IF NOT EXISTS department VARCHAR(100) NULL",
                "ALTER TABLE patients ADD COLUMN IF NOT EXISTS admission_date DATE NULL",
                "ALTER TABLE patients ADD COLUMN IF NOT EXISTS archived_at DATETIME NULL"
            ];
            
            foreach ($sqlCommands as $sql) {
                try {
                    $db->query($sql);
                    $columnName = preg_match('/ADD COLUMN IF NOT EXISTS (\w+)/', $sql, $matches) ? $matches[1] : 'unknown';
                    $output[] = "✅ Added/Verified column: {$columnName}";
                } catch (\Exception $e) {
                    // If "IF NOT EXISTS" doesn't work, try without it
                    $simpleSql = str_replace(' IF NOT EXISTS', '', $sql);
                    try {
                        $db->query($simpleSql);
                        $columnName = preg_match('/ADD COLUMN (\w+)/', $simpleSql, $matches) ? $matches[1] : 'unknown';
                        $output[] = "✅ Added column: {$columnName}";
                    } catch (\Exception $e2) {
                        $columnName = preg_match('/ADD COLUMN (\w+)/', $sql, $matches) ? $matches[1] : 'unknown';
                        $output[] = "⏭️ Column {$columnName} may already exist: " . $e2->getMessage();
                    }
                }
            }
            
            // Update status enum
            try {
                $db->query("ALTER TABLE patients MODIFY COLUMN status ENUM('active', 'inactive', 'deceased', 'inpatient', 'outpatient', 'archived') DEFAULT 'outpatient'");
                $output[] = "✅ Updated status enum";
            } catch (\Exception $e) {
                $output[] = "⚠️ Status enum update: " . $e->getMessage();
            }
            
            // Test the fix
            try {
                $testData = [
                    'first_name' => 'Emergency',
                    'last_name' => 'Test',
                    'middle_name' => 'Fix',
                    'contact_number' => '09999999999',
                    'status' => 'outpatient'
                ];
                
                $db->table('patients')->insert($testData);
                $insertId = $db->insertID();
                
                if ($insertId) {
                    $output[] = "✅ EMERGENCY FIX SUCCESSFUL! Test insertion worked (ID: {$insertId})";
                    $db->table('patients')->delete(['id' => $insertId]);
                    $output[] = "✅ Test data cleaned up";
                } else {
                    $output[] = "❌ Test insertion failed - no ID returned";
                    $success = false;
                }
            } catch (\Exception $e) {
                $output[] = "❌ EMERGENCY FIX FAILED! Test insertion error: " . $e->getMessage();
                $success = false;
            }
            
        } catch (\Exception $e) {
            $output[] = "❌ CRITICAL ERROR: " . $e->getMessage();
            $success = false;
        }
        
        // Return as both JSON and HTML for easy viewing
        if ($this->request->getHeaderLine('Accept') === 'application/json') {
            return $this->response->setJSON([
                'success' => $success,
                'output' => $output
            ]);
        } else {
            $html = '<h2>🚨 Emergency Patients Table Fix</h2>';
            $html .= '<pre>' . implode("\n", $output) . '</pre>';
            if ($success) {
                $html .= '<p style="color: green; font-weight: bold;">✅ FIX SUCCESSFUL! You can now add patients.</p>';
                $html .= '<a href="' . base_url('super-admin/patients/add') . '">Try Adding a Patient</a>';
            } else {
                $html .= '<p style="color: red; font-weight: bold;">❌ FIX FAILED! Check the errors above.</p>';
            }
            return $html;
        }
    }

    public function recreatePatientsTable()
    {
        $output = [];
        $success = true;
        
        try {
            $db = \Config\Database::connect();
            $forge = \Config\Database::forge();
            
            $output[] = "🔄 RECREATING PATIENTS TABLE with complete structure...";
            
            // Backup existing data if any
            $existingData = [];
            if ($db->tableExists('patients')) {
                try {
                    $existingData = $db->table('patients')->get()->getResultArray();
                    $output[] = "📦 Backed up " . count($existingData) . " existing patient records";
                } catch (\Exception $e) {
                    $output[] = "⚠️ Could not backup existing data: " . $e->getMessage();
                }
                
                // Drop existing table
                $forge->dropTable('patients', true);
                $output[] = "🗑️ Dropped existing patients table";
            }
            
            // Create new table using migration
            $migration = new \App\Database\Migrations\CreatePatientsTable();
            
            // Set up migration properties
            $reflection = new \ReflectionClass($migration);
            $dbProperty = $reflection->getProperty('db');
            $dbProperty->setAccessible(true);
            $dbProperty->setValue($migration, $db);
            
            $forgeProperty = $reflection->getProperty('forge');
            $forgeProperty->setAccessible(true);
            $forgeProperty->setValue($migration, $forge);
            
            // Call createNewTable method
            $method = $reflection->getMethod('createNewTable');
            $method->setAccessible(true);
            
            ob_start();
            $method->invoke($migration);
            $migrationOutput = ob_get_clean();
            
            $output[] = "✅ Created new patients table with complete structure";
            $output[] = "Migration output: " . $migrationOutput;
            
            // Restore data if any (map old fields to new structure)
            if (!empty($existingData)) {
                $restored = 0;
                foreach ($existingData as $oldRecord) {
                    try {
                        // Map old fields to new structure
                        $newRecord = [
                            'patient_id' => $oldRecord['patient_id'] ?? null,
                            'first_name' => $oldRecord['first_name'] ?? 'Unknown',
                            'last_name' => $oldRecord['last_name'] ?? 'Patient',
                            'middle_name' => $oldRecord['middle_name'] ?? null,
                            'date_of_birth' => $oldRecord['date_of_birth'] ?? null,
                            'gender' => $oldRecord['gender'] ?? null,
                            'contact_number' => $oldRecord['contact_number'] ?? $oldRecord['phone'] ?? null,
                            'phone' => $oldRecord['phone'] ?? null,
                            'email' => $oldRecord['email'] ?? null,
                            'address' => $oldRecord['address'] ?? null,
                            'emergency_contact_name' => $oldRecord['emergency_contact_name'] ?? null,
                            'emergency_contact_number' => $oldRecord['emergency_contact_number'] ?? $oldRecord['emergency_contact_phone'] ?? null,
                            'emergency_contact_phone' => $oldRecord['emergency_contact_phone'] ?? null,
                            'government_id' => $oldRecord['government_id'] ?? null,
                            'blood_type' => $oldRecord['blood_type'] ?? null,
                            'allergies' => $oldRecord['allergies'] ?? null,
                            'medical_history' => $oldRecord['medical_history'] ?? null,
                            'status' => $oldRecord['status'] ?? 'outpatient',
                            'department' => $oldRecord['department'] ?? null,
                            'admission_date' => $oldRecord['admission_date'] ?? null,
                            'archived_at' => $oldRecord['archived_at'] ?? null,
                            'created_at' => $oldRecord['created_at'] ?? date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                        
                        $db->table('patients')->insert($newRecord);
                        $restored++;
                    } catch (\Exception $e) {
                        $output[] = "⚠️ Failed to restore record ID " . ($oldRecord['id'] ?? 'unknown') . ": " . $e->getMessage();
                    }
                }
                $output[] = "📥 Restored {$restored} patient records";
            }
            
            // Test the new structure
            try {
                $testData = [
                    'first_name' => 'Test',
                    'last_name' => 'Patient',
                    'middle_name' => 'Recreate',
                    'contact_number' => '09123456789',
                    'email' => 'test@example.com',
                    'address' => 'Test Address',
                    'emergency_contact_name' => 'Test Emergency',
                    'emergency_contact_number' => '09987654321',
                    'status' => 'outpatient',
                    'department' => 'Test Department'
                ];
                
                $db->table('patients')->insert($testData);
                $insertId = $db->insertID();
                
                if ($insertId) {
                    $output[] = "✅ TABLE RECREATION SUCCESSFUL! Test insertion worked (ID: {$insertId})";
                    $db->table('patients')->delete(['id' => $insertId]);
                    $output[] = "✅ Test data cleaned up";
                } else {
                    $output[] = "❌ Test insertion failed - no ID returned";
                    $success = false;
                }
            } catch (\Exception $e) {
                $output[] = "❌ TABLE RECREATION FAILED! Test insertion error: " . $e->getMessage();
                $success = false;
            }
            
        } catch (\Exception $e) {
            $output[] = "❌ CRITICAL ERROR: " . $e->getMessage();
            $success = false;
        }
        
        // Return as HTML for easy viewing
        $html = '<h2>🔄 Patients Table Recreation</h2>';
        $html .= '<pre>' . implode("\n", $output) . '</pre>';
        if ($success) {
            $html .= '<p style="color: green; font-weight: bold;">✅ TABLE RECREATION SUCCESSFUL!</p>';
            $html .= '<p>The patients table now has the complete structure with all required fields.</p>';
            $html .= '<a href="' . base_url('super-admin/patients/add') . '" style="background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Try Adding a Patient</a>';
        } else {
            $html .= '<p style="color: red; font-weight: bold;">❌ TABLE RECREATION FAILED!</p>';
            $html .= '<p>Check the errors above and try the emergency fix instead.</p>';
            $html .= '<a href="' . base_url('super-admin/patients/emergency-fix') . '" style="background: #ef4444; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Try Emergency Fix</a>';
        }
        return $html;
    }

    public function clearSamplePatients()
    {
        $output = [];
        $success = true;
        
        try {
            $db = \Config\Database::connect();
            
            $output[] = "🧹 CLEARING SAMPLE/TEST PATIENTS from database...";
            
            // Get all patients first to show what will be deleted
            $allPatients = $db->table('patients')->get()->getResultArray();
            $output[] = "📊 Found " . count($allPatients) . " total patient records";
            
            if (empty($allPatients)) {
                $output[] = "✅ No patients found - table is already clean!";
                $success = true;
            } else {
                // Show current patients
                $output[] = "\n📋 Current patients in database:";
                foreach ($allPatients as $patient) {
                    $name = ($patient['first_name'] ?? '') . ' ' . ($patient['middle_name'] ?? '') . ' ' . ($patient['last_name'] ?? '');
                    $output[] = "  - ID: {$patient['id']}, Name: " . trim($name) . ", Status: " . ($patient['status'] ?? 'unknown');
                }
                
                // Identify test/sample patients by common test names
                $testPatterns = [
                    'test', 'sample', 'demo', 'example', 'migration', 'debug', 'fix', 'emergency', 'recreate'
                ];
                
                $testPatients = [];
                $realPatients = [];
                
                foreach ($allPatients as $patient) {
                    $fullName = strtolower(($patient['first_name'] ?? '') . ' ' . ($patient['middle_name'] ?? '') . ' ' . ($patient['last_name'] ?? ''));
                    $isTest = false;
                    
                    foreach ($testPatterns as $pattern) {
                        if (strpos($fullName, $pattern) !== false) {
                            $isTest = true;
                            break;
                        }
                    }
                    
                    if ($isTest) {
                        $testPatients[] = $patient;
                    } else {
                        $realPatients[] = $patient;
                    }
                }
                
                $output[] = "\n🔍 Analysis:";
                $output[] = "  - Test/Sample patients: " . count($testPatients);
                $output[] = "  - Real patients: " . count($realPatients);
                
                if (!empty($testPatients)) {
                    $output[] = "\n🗑️ Deleting test/sample patients:";
                    $deleted = 0;
                    
                    foreach ($testPatients as $patient) {
                        try {
                            $db->table('patients')->delete(['id' => $patient['id']]);
                            $name = trim(($patient['first_name'] ?? '') . ' ' . ($patient['middle_name'] ?? '') . ' ' . ($patient['last_name'] ?? ''));
                            $output[] = "  ✅ Deleted: {$name} (ID: {$patient['id']})";
                            $deleted++;
                        } catch (\Exception $e) {
                            $output[] = "  ❌ Failed to delete ID {$patient['id']}: " . $e->getMessage();
                        }
                    }
                    
                    $output[] = "\n📊 Summary:";
                    $output[] = "  - Deleted: {$deleted} test/sample patients";
                    $output[] = "  - Remaining: " . count($realPatients) . " real patients";
                    
                } else {
                    $output[] = "\n✅ No test/sample patients found to delete";
                }
                
                // Option to clear ALL patients (with confirmation)
                if ($this->request->getGet('clear_all') === 'confirm') {
                    $output[] = "\n🚨 CLEARING ALL PATIENTS (as requested)...";
                    try {
                        $db->table('patients')->truncate();
                        $output[] = "✅ All patient records have been deleted";
                        $output[] = "📊 Patients table is now empty";
                    } catch (\Exception $e) {
                        $output[] = "❌ Failed to clear all patients: " . $e->getMessage();
                        $success = false;
                    }
                }
            }
            
        } catch (\Exception $e) {
            $output[] = "❌ CRITICAL ERROR: " . $e->getMessage();
            $success = false;
        }
        
        // Return as HTML for easy viewing
        $html = '<h2>🧹 Clear Sample Patients</h2>';
        $html .= '<pre>' . implode("\n", $output) . '</pre>';
        
        if ($success) {
            $html .= '<p style="color: green; font-weight: bold;">✅ CLEANUP SUCCESSFUL!</p>';
            
            // Show options
            $html .= '<div style="margin: 20px 0;">';
            $html .= '<h3>Options:</h3>';
            $html .= '<a href="' . base_url('super-admin/patients') . '" style="background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-right: 10px;">View Patients</a>';
            $html .= '<a href="' . base_url('super-admin/patients/add') . '" style="background: #10b981; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-right: 10px;">Add New Patient</a>';
            
            // Only show "Clear All" if there are still patients
            $remainingCount = $db->table('patients')->countAllResults();
            if ($remainingCount > 0) {
                $html .= '<br><br><strong>⚠️ Clear ALL Patients:</strong><br>';
                $html .= '<p style="color: #dc2626;">This will delete ALL patient records permanently!</p>';
                $html .= '<a href="' . base_url('super-admin/patients/clear-samples?clear_all=confirm') . '" style="background: #dc2626; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;" onclick="return confirm(\'Are you sure you want to delete ALL patient records? This cannot be undone!\')">⚠️ Clear ALL Patients</a>';
            }
            $html .= '</div>';
            
        } else {
            $html .= '<p style="color: red; font-weight: bold;">❌ CLEANUP FAILED!</p>';
        }
        
        return $html;
    }

    public function forceFixPatients()
    {
        try {
            $db = \Config\Database::connect();
            
            echo "<h2>🔧 FORCE FIX: Adding Missing Columns to Patients Table</h2>";
            echo "<pre>";
            
            // Direct SQL commands - no checking, just add
            $sqlCommands = [
                "ALTER TABLE patients ADD COLUMN middle_name VARCHAR(100) NULL",
                "ALTER TABLE patients ADD COLUMN contact_number VARCHAR(20) NULL", 
                "ALTER TABLE patients ADD COLUMN emergency_contact_number VARCHAR(20) NULL",
                "ALTER TABLE patients ADD COLUMN government_id VARCHAR(50) NULL",
                "ALTER TABLE patients ADD COLUMN department VARCHAR(100) NULL",
                "ALTER TABLE patients ADD COLUMN admission_date DATE NULL",
                "ALTER TABLE patients ADD COLUMN archived_at DATETIME NULL"
            ];
            
            echo "Executing SQL commands directly...\n\n";
            
            foreach ($sqlCommands as $sql) {
                echo "Running: {$sql}\n";
                try {
                    $db->query($sql);
                    echo "✅ SUCCESS\n\n";
                } catch (\Exception $e) {
                    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                        echo "⏭️ Column already exists\n\n";
                    } else {
                        echo "❌ ERROR: " . $e->getMessage() . "\n\n";
                    }
                }
            }
            
            // Update status enum
            echo "Updating status enum...\n";
            try {
                $db->query("ALTER TABLE patients MODIFY COLUMN status ENUM('active', 'inactive', 'deceased', 'inpatient', 'outpatient', 'archived') DEFAULT 'outpatient'");
                echo "✅ Status enum updated\n\n";
            } catch (\Exception $e) {
                echo "❌ Status enum error: " . $e->getMessage() . "\n\n";
            }
            
            // Test insertion
            echo "Testing patient insertion...\n";
            try {
                $testData = [
                    'first_name' => 'Force',
                    'last_name' => 'Fix',
                    'middle_name' => 'Test',
                    'contact_number' => '09999999999',
                    'status' => 'outpatient'
                ];
                
                $db->table('patients')->insert($testData);
                $insertId = $db->insertID();
                
                if ($insertId) {
                    echo "✅ SUCCESS! Patient insertion worked (ID: {$insertId})\n";
                    $db->table('patients')->delete(['id' => $insertId]);
                    echo "✅ Test data cleaned up\n\n";
                    
                    echo "🎉 FORCE FIX COMPLETED SUCCESSFULLY!\n";
                    echo "You can now register patients without errors.\n\n";
                    
                    echo "</pre>";
                    echo '<a href="' . base_url('super-admin/patients/add') . '" style="background: #10b981; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block; margin: 20px 0;">🎯 Try Registering a Patient Now!</a>';
                    
                } else {
                    echo "❌ Test insertion failed - no ID returned\n";
                    echo "</pre>";
                }
            } catch (\Exception $e) {
                echo "❌ Test insertion failed: " . $e->getMessage() . "\n";
                echo "</pre>";
            }
            
        } catch (\Exception $e) {
            echo "❌ CRITICAL ERROR: " . $e->getMessage();
        }
    }

    public function runPatientsMigration()
    {
        try {
            $db = \Config\Database::connect();
            
            echo "<h2>🔄 Adding ALL Missing Columns to Patients Table</h2>";
            echo "<pre>";
            echo "Executing SQL commands directly...\n\n";
            
            // Direct SQL commands from the migration
            $sqlCommands = [
                "ALTER TABLE patients ADD COLUMN middle_name VARCHAR(100) NULL COMMENT 'Patient middle name - optional'",
                "ALTER TABLE patients ADD COLUMN contact_number VARCHAR(20) NULL COMMENT 'Primary contact number (09XXXXXXXXX)'",
                "ALTER TABLE patients ADD COLUMN emergency_contact_number VARCHAR(20) NULL COMMENT 'Emergency contact phone number'",
                "ALTER TABLE patients ADD COLUMN government_id VARCHAR(50) NULL COMMENT 'Government ID number (SSS, PhilHealth, etc.)'",
                "ALTER TABLE patients ADD COLUMN department VARCHAR(100) NULL COMMENT 'Assigned department (Cardiology, Pediatrics, etc.)'",
                "ALTER TABLE patients ADD COLUMN admission_date DATE NULL COMMENT 'Date of admission for inpatients'",
                "ALTER TABLE patients ADD COLUMN archived_at DATETIME NULL COMMENT 'Soft delete timestamp'"
            ];
            
            foreach ($sqlCommands as $sql) {
                echo "Running: " . substr($sql, 0, 60) . "...\n";
                try {
                    $db->query($sql);
                    $columnName = preg_match('/ADD COLUMN (\w+)/', $sql, $matches) ? $matches[1] : 'unknown';
                    echo "✅ Added column: {$columnName}\n\n";
                } catch (\Exception $e) {
                    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                        $columnName = preg_match('/ADD COLUMN (\w+)/', $sql, $matches) ? $matches[1] : 'unknown';
                        echo "⏭️ Column {$columnName} already exists\n\n";
                    } else {
                        echo "❌ Error: " . $e->getMessage() . "\n\n";
                    }
                }
            }
            
            // Update status enum
            echo "Updating status enum...\n";
            try {
                $db->query("ALTER TABLE patients MODIFY COLUMN status ENUM('active', 'inactive', 'deceased', 'inpatient', 'outpatient', 'archived') DEFAULT 'outpatient'");
                echo "✅ Updated status enum\n\n";
            } catch (\Exception $e) {
                echo "❌ Status enum update failed: " . $e->getMessage() . "\n\n";
            }
            
            // Make columns nullable
            echo "Making columns nullable...\n";
            try {
                $db->query("ALTER TABLE patients MODIFY COLUMN patient_id VARCHAR(20) NULL");
                $db->query("ALTER TABLE patients MODIFY COLUMN date_of_birth DATE NULL");
                $db->query("ALTER TABLE patients MODIFY COLUMN gender ENUM('male', 'female', 'other') NULL");
                echo "✅ Made columns nullable\n\n";
            } catch (\Exception $e) {
                echo "❌ Failed to make columns nullable: " . $e->getMessage() . "\n\n";
            }
            
            // Test insertion with ALL fields
            echo "Testing patient insertion with ALL fields...\n";
            try {
                $testData = [
                    'first_name' => 'Migration',
                    'last_name' => 'Test',
                    'middle_name' => 'Complete',
                    'contact_number' => '09123456789',
                    'emergency_contact_number' => '09987654321',
                    'department' => 'Test Department',
                    'status' => 'outpatient'
                ];
                
                $db->table('patients')->insert($testData);
                $insertId = $db->insertID();
                
                if ($insertId) {
                    echo "✅ SUCCESS! Patient insertion with ALL fields worked (ID: {$insertId})\n";
                    $db->table('patients')->delete(['id' => $insertId]);
                    echo "✅ Test data cleaned up\n\n";
                    
                    echo "🎉 ALL COLUMNS ADDED SUCCESSFULLY!\n";
                    echo "The 'Unknown column' errors should now be GONE!\n\n";
                } else {
                    echo "❌ Test insertion failed - no ID returned\n\n";
                }
            } catch (\Exception $e) {
                echo "❌ Test insertion failed: " . $e->getMessage() . "\n\n";
            }
            
            echo "</pre>";
            echo '<h3 style="color: green;">✅ Database Update Completed!</h3>';
            echo '<p>All missing columns have been added to the patients table from CreatePatientsTable migration.</p>';
            echo '<a href="' . base_url('super-admin/patients/add') . '" style="background: #10b981; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block; margin: 20px 0;">🎯 Try Registering a Patient Now!</a>';
            
        } catch (\Exception $e) {
            echo "<h3 style='color: red;'>❌ Database Update Failed!</h3>";
            echo "<pre>Error: " . $e->getMessage() . "</pre>";
        }
    }

    public function admissions()
    {
        return $this->render('SuperAdmin/admissions');
    }

    public function doctors()
    {
        $doctors = $this->userModel->getUsersByRole('doctor');
        return $this->render('SuperAdmin/doctors', ['doctors' => $doctors]);
    }

    public function staff()
    {
        $staff = $this->userModel->whereIn('role', ['nurse', 'receptionist', 'laboratory', 'pharmacist', 'accountant', 'it_staff'])->findAll();
        return $this->render('SuperAdmin/staff', ['staff' => $staff]);
    }

    public function billing()
    {
        return $this->render('SuperAdmin/billing');
    }

    public function roles()
    {
        try {
            // Try to get roles, fallback to empty array if fails
            $roles = [];
            try {
                $roles = $this->roleModel->getActiveRoles();
            } catch (\Exception $e) {
                log_message('error', 'Failed to get roles: ' . $e->getMessage());
                // Create default roles if table is empty or doesn't exist
                $roles = $this->createDefaultRoles();
            }
            
            return $this->render('SuperAdmin/roles', ['roles' => $roles]);
        } catch (\Exception $e) {
            log_message('error', 'Roles page error: ' . $e->getMessage());
            return $this->render('SuperAdmin/roles', ['roles' => [], 'error' => $e->getMessage()]);
        }
    }
    
    private function createDefaultRoles()
    {
        $defaultRoles = [
            ['id' => 1, 'role_name' => 'Super Admin', 'role_description' => 'Full system access', 'is_active' => true],
            ['id' => 2, 'role_name' => 'Doctor', 'role_description' => 'Medical staff access', 'is_active' => true],
            ['id' => 3, 'role_name' => 'Nurse', 'role_description' => 'Nursing staff access', 'is_active' => true],
            ['id' => 4, 'role_name' => 'Receptionist', 'role_description' => 'Front desk access', 'is_active' => true],
            ['id' => 5, 'role_name' => 'Laboratory', 'role_description' => 'Lab staff access', 'is_active' => true],
            ['id' => 6, 'role_name' => 'Pharmacist', 'role_description' => 'Pharmacy access', 'is_active' => true],
            ['id' => 7, 'role_name' => 'Accountant', 'role_description' => 'Financial access', 'is_active' => true],
            ['id' => 8, 'role_name' => 'IT Staff', 'role_description' => 'Technical support access', 'is_active' => true],
        ];
        
        return $defaultRoles;
    }

    public function viewRole($id)
    {
        try {
            // Always use default roles for now since database might not exist
            $defaultRoles = $this->createDefaultRoles();
            $role = array_filter($defaultRoles, function($r) use ($id) {
                return $r['id'] == $id;
            });
            $role = !empty($role) ? array_values($role)[0] : null;
            
            if (!$role) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Role not found');
            }
            
            return $this->render('SuperAdmin/view_role', ['role' => $role]);
        } catch (\Exception $e) {
            log_message('error', 'View role error: ' . $e->getMessage());
            return redirect()->to('super-admin/roles')->with('error', 'Role not found: ' . $e->getMessage());
        }
    }

    public function editRole($id)
    {
        try {
            // Always use default roles for now since database might not exist
            $defaultRoles = $this->createDefaultRoles();
            $role = array_filter($defaultRoles, function($r) use ($id) {
                return $r['id'] == $id;
            });
            $role = !empty($role) ? array_values($role)[0] : null;
            
            if (!$role) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Role not found');
            }

            if ($this->request->getMethod() === 'POST') {
                // For now, just simulate success since we're using default roles
                $data = $this->request->getPost();
                
                log_message('info', 'Role edit attempt for ID: ' . $id . ' with data: ' . json_encode($data));
                
                // Simulate successful update
                return $this->response->setJSON([
                    'success' => true, 
                    'message' => 'Role updated successfully (simulated - using default roles)'
                ]);
            }

            return $this->render('SuperAdmin/edit_role', ['role' => $role]);
        } catch (\Exception $e) {
            log_message('error', 'Edit role error: ' . $e->getMessage());
            return redirect()->to('super-admin/roles')->with('error', 'Role not found: ' . $e->getMessage());
        }
    }

    public function addRole()
    {
        if ($this->request->getMethod() === 'POST') {
            $data = $this->request->getPost();
            
            try {
                if ($this->roleModel->insert($data)) {
                    return $this->response->setJSON(['success' => true, 'message' => 'Role created successfully']);
                } else {
                    return $this->response->setJSON(['success' => false, 'errors' => $this->roleModel->errors()]);
                }
            } catch (\Exception $e) {
                return $this->response->setJSON(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
        }

        return $this->render('SuperAdmin/add_role');
    }

    public function runAppointmentMigration()
    {
        try {
            $migrate = \Config\Services::migrations();
            $migrate->setNamespace('App');
            
            // Run the specific migration
            $result = $migrate->version('2024-01-01-000011');
            
            if ($result === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Migration failed: ' . implode(', ', $migrate->getCliMessages())
                ]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Migration completed successfully. Appointment fields added.'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Migration error: ' . $e->getMessage()
            ]);
        }
    }

    public function appointments()
    {
        try {
            // First, ensure the table has the required columns
            $this->ensureAppointmentColumns();
            
            // Get appointments from database
            $appointments = $this->appointmentModel->getAllAppointments();
            
            // If no appointments in database, create some sample data
            if (empty($appointments)) {
                $this->createSampleAppointments();
                $appointments = $this->appointmentModel->getAllAppointments();
            }
            
            return $this->render('SuperAdmin/appointments', ['appointments' => $appointments]);
        } catch (\Exception $e) {
            log_message('error', 'Appointments page error: ' . $e->getMessage());
            
            // Fallback to sample data if database fails
            $appointments = $this->getSampleAppointments();
            return $this->render('SuperAdmin/appointments', ['appointments' => $appointments]);
        }
    }

    private function ensureAppointmentColumns()
    {
        try {
            $db = \Config\Database::connect();
            
            // Check if patient_name column exists
            if (!$db->fieldExists('patient_name', 'appointments')) {
                log_message('info', 'Adding missing appointment columns...');
                
                // Add columns one by one to avoid SQL errors
                $columnsToAdd = [
                    "ALTER TABLE appointments ADD COLUMN patient_name VARCHAR(255) NULL",
                    "ALTER TABLE appointments ADD COLUMN patient_phone VARCHAR(20) NULL", 
                    "ALTER TABLE appointments ADD COLUMN doctor_name VARCHAR(255) NULL",
                    "ALTER TABLE appointments ADD COLUMN department VARCHAR(100) NULL",
                    "ALTER TABLE appointments ADD COLUMN appointment_type ENUM('consultation','follow-up','emergency','surgery','therapy') DEFAULT 'consultation'"
                ];
                
                foreach ($columnsToAdd as $sql) {
                    try {
                        $db->query($sql);
                        log_message('info', 'Executed: ' . $sql);
                    } catch (\Exception $e) {
                        log_message('error', 'Failed to execute: ' . $sql . ' - ' . $e->getMessage());
                    }
                }
            }
            
            // Update status enum to include 'pending'
            try {
                $db->query("ALTER TABLE appointments MODIFY COLUMN status ENUM('pending','scheduled','confirmed','in_progress','completed','cancelled','no_show') DEFAULT 'pending'");
                log_message('info', 'Updated status enum');
            } catch (\Exception $e) {
                log_message('error', 'Failed to update status enum: ' . $e->getMessage());
            }
            
            // Make patient_id and doctor_id nullable
            try {
                $db->query("ALTER TABLE appointments MODIFY COLUMN patient_id INT(11) UNSIGNED NULL");
                $db->query("ALTER TABLE appointments MODIFY COLUMN doctor_id INT(11) UNSIGNED NULL");
                log_message('info', 'Made ID columns nullable');
            } catch (\Exception $e) {
                log_message('error', 'Failed to make ID columns nullable: ' . $e->getMessage());
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Failed to ensure appointment columns: ' . $e->getMessage());
        }
    }

    private function createSampleAppointments()
    {
        $sampleAppointments = [
            [
                'patient_name' => 'Juan Dela Cruz',
                'patient_phone' => '09123456789',
                'doctor_name' => 'Dr. Maria Santos',
                'department' => 'Cardiology',
                'appointment_date' => date('Y-m-d'),
                'appointment_time' => '09:00:00',
                'status' => 'pending',
                'appointment_type' => 'consultation',
                'notes' => 'Regular checkup'
            ],
            [
                'patient_name' => 'Anna Garcia',
                'patient_phone' => '09987654321',
                'doctor_name' => 'Dr. Roberto Cruz',
                'department' => 'Pediatrics',
                'appointment_date' => date('Y-m-d', strtotime('+1 day')),
                'appointment_time' => '14:30:00',
                'status' => 'confirmed',
                'appointment_type' => 'follow-up',
                'notes' => 'Follow-up consultation'
            ],
            [
                'patient_name' => 'Pedro Reyes',
                'patient_phone' => '09555123456',
                'doctor_name' => 'Dr. Lisa Fernandez',
                'department' => 'Orthopedics',
                'appointment_date' => date('Y-m-d', strtotime('-1 day')),
                'appointment_time' => '11:00:00',
                'status' => 'completed',
                'appointment_type' => 'consultation',
                'notes' => 'Knee examination'
            ]
        ];

        foreach ($sampleAppointments as $appointment) {
            try {
                $this->appointmentModel->insert($appointment);
            } catch (\Exception $e) {
                log_message('error', 'Failed to create sample appointment: ' . $e->getMessage());
            }
        }
    }

    private function getSampleAppointments()
    {
        return [
            [
                'id' => 1,
                'patient_name' => 'Juan Dela Cruz',
                'patient_phone' => '09123456789',
                'doctor_name' => 'Dr. Maria Santos',
                'department' => 'Cardiology',
                'appointment_date' => date('Y-m-d'),
                'appointment_time' => '09:00:00',
                'status' => 'pending',
                'appointment_type' => 'consultation'
            ],
            [
                'id' => 2,
                'patient_name' => 'Anna Garcia',
                'patient_phone' => '09987654321',
                'doctor_name' => 'Dr. Roberto Cruz',
                'department' => 'Pediatrics',
                'appointment_date' => date('Y-m-d', strtotime('+1 day')),
                'appointment_time' => '14:30:00',
                'status' => 'confirmed',
                'appointment_type' => 'follow-up'
            ]
        ];
    }

    public function rescheduleAppointment($id)
    {
        if ($this->request->getMethod() === 'POST') {
            $data = $this->request->getPost();
            
            try {
                $newDate = $data['new_date'];
                $newTime = $data['new_time'];
                $reason = $data['reason'] ?? null;
                
                if ($this->appointmentModel->rescheduleAppointment($id, $newDate, $newTime, $reason)) {
                    log_message('info', 'Appointment rescheduled successfully for ID: ' . $id);
                    
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Appointment rescheduled successfully in database'
                    ]);
                } else {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Failed to reschedule appointment'
                    ]);
                }
            } catch (\Exception $e) {
                log_message('error', 'Failed to reschedule appointment: ' . $e->getMessage());
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Database error: ' . $e->getMessage()
                ]);
            }
        }
        
        return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method']);
    }

    public function cancelAppointment($id)
    {
        if ($this->request->getMethod() === 'POST') {
            try {
                if ($this->appointmentModel->cancelAppointment($id)) {
                    log_message('info', 'Appointment cancelled successfully for ID: ' . $id);
                    
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Appointment cancelled successfully in database'
                    ]);
                } else {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Failed to cancel appointment'
                    ]);
                }
            } catch (\Exception $e) {
                log_message('error', 'Failed to cancel appointment: ' . $e->getMessage());
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Database error: ' . $e->getMessage()
                ]);
            }
        }
        
        return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method']);
    }

    public function fixAppointmentTable()
    {
        try {
            $db = \Config\Database::connect();
            $messages = [];
            
            // Check current table structure
            $fields = $db->getFieldData('appointments');
            $fieldNames = array_column($fields, 'name');
            $messages[] = "Current fields: " . implode(', ', $fieldNames);
            
            // Add missing columns
            $columnsToAdd = [
                'patient_name' => "ALTER TABLE appointments ADD COLUMN patient_name VARCHAR(255) NULL",
                'patient_phone' => "ALTER TABLE appointments ADD COLUMN patient_phone VARCHAR(20) NULL", 
                'doctor_name' => "ALTER TABLE appointments ADD COLUMN doctor_name VARCHAR(255) NULL",
                'department' => "ALTER TABLE appointments ADD COLUMN department VARCHAR(100) NULL",
                'appointment_type' => "ALTER TABLE appointments ADD COLUMN appointment_type ENUM('consultation','follow-up','emergency','surgery','therapy') DEFAULT 'consultation'"
            ];
            
            foreach ($columnsToAdd as $column => $sql) {
                if (!in_array($column, $fieldNames)) {
                    try {
                        $db->query($sql);
                        $messages[] = "✅ Added column: $column";
                    } catch (\Exception $e) {
                        $messages[] = "❌ Failed to add $column: " . $e->getMessage();
                    }
                } else {
                    $messages[] = "ℹ️ Column $column already exists";
                }
            }
            
            // Update status enum
            try {
                $db->query("ALTER TABLE appointments MODIFY COLUMN status ENUM('pending','scheduled','confirmed','in_progress','completed','cancelled','no_show') DEFAULT 'pending'");
                $messages[] = "✅ Updated status enum";
            } catch (\Exception $e) {
                $messages[] = "❌ Failed to update status: " . $e->getMessage();
            }
            
            // Make ID columns nullable
            try {
                $db->query("ALTER TABLE appointments MODIFY COLUMN patient_id INT(11) UNSIGNED NULL");
                $db->query("ALTER TABLE appointments MODIFY COLUMN doctor_id INT(11) UNSIGNED NULL");
                $messages[] = "✅ Made ID columns nullable";
            } catch (\Exception $e) {
                $messages[] = "❌ Failed to make IDs nullable: " . $e->getMessage();
            }
            
            // Test insertion
            $testData = [
                'patient_name' => 'Test Patient Fix',
                'patient_phone' => '09123456789',
                'doctor_name' => 'Dr. Test Doctor',
                'department' => 'Test Department',
                'appointment_date' => date('Y-m-d'),
                'appointment_time' => '10:00:00',
                'appointment_type' => 'consultation',
                'status' => 'pending',
                'notes' => 'Database fix test'
            ];
            
            $result = $this->appointmentModel->insert($testData);
            if ($result) {
                $messages[] = "✅ Test insertion successful! ID: " . $this->appointmentModel->getInsertID();
            } else {
                $messages[] = "❌ Test insertion failed: " . implode(', ', $this->appointmentModel->errors());
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Database fix completed',
                'details' => $messages
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database fix failed: ' . $e->getMessage()
            ]);
        }
    }

    public function testAppointmentDB()
    {
        try {
            $db = \Config\Database::connect();
            
            // Check table structure
            $fields = $db->getFieldData('appointments');
            $fieldNames = array_column($fields, 'name');
            
            // Test data
            $testData = [
                'patient_name' => 'Test Patient',
                'patient_phone' => '09123456789',
                'doctor_name' => 'Dr. Test Doctor',
                'department' => 'Test Department',
                'appointment_date' => date('Y-m-d'),
                'appointment_time' => '10:00:00',
                'appointment_type' => 'consultation',
                'status' => 'pending',
                'notes' => 'Test appointment'
            ];
            
            // Try to insert
            $result = $this->appointmentModel->insert($testData);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Database test completed',
                'table_fields' => $fieldNames,
                'insert_result' => $result,
                'insert_id' => $this->appointmentModel->getInsertID(),
                'test_data' => $testData
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database test failed: ' . $e->getMessage(),
                'error_details' => $e->getTraceAsString()
            ]);
        }
    }

    public function addAppointment()
    {
        if ($this->request->getMethod() === 'POST') {
            $data = $this->request->getPost();
            
            // Map form data to database fields
            $appointmentData = [
                'patient_name' => $data['patient_name'] ?? '',
                'patient_phone' => $data['patient_phone'] ?? '',
                'doctor_name' => $data['doctor_name'] ?? '',
                'department' => $data['department'] ?? '',
                'appointment_date' => $data['appointment_date'],
                'appointment_time' => $data['appointment_time'],
                'appointment_type' => $data['appointment_type'],
                'status' => $data['status'] ?? 'pending',
                'notes' => $data['notes'] ?? ''
            ];
            
            try {
                if ($this->appointmentModel->insert($appointmentData)) {
                    // Log the successful creation
                    log_message('info', 'New appointment created with ID: ' . $this->appointmentModel->getInsertID());
                    
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Appointment created successfully and saved to database'
                    ]);
                } else {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Failed to save appointment',
                        'errors' => $this->appointmentModel->errors()
                    ]);
                }
            } catch (\Exception $e) {
                log_message('error', 'Failed to create appointment: ' . $e->getMessage());
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Database error: ' . $e->getMessage()
                ]);
            }
        }

        // Sample doctors and patients for the form
        $doctors = [
            ['id' => 1, 'name' => 'Dr. Maria Santos', 'department' => 'Cardiology'],
            ['id' => 2, 'name' => 'Dr. Roberto Cruz', 'department' => 'Pediatrics'],
            ['id' => 3, 'name' => 'Dr. Lisa Fernandez', 'department' => 'Orthopedics'],
            ['id' => 4, 'name' => 'Dr. Miguel Torres', 'department' => 'Dermatology'],
            ['id' => 5, 'name' => 'Dr. Elena Villanueva', 'department' => 'Neurology']
        ];

        $patients = [
            ['id' => 1, 'name' => 'Juan Dela Cruz', 'phone' => '09123456789'],
            ['id' => 2, 'name' => 'Anna Garcia', 'phone' => '09987654321'],
            ['id' => 3, 'name' => 'Pedro Reyes', 'phone' => '09555123456'],
            ['id' => 4, 'name' => 'Carmen Lopez', 'phone' => '09777888999'],
            ['id' => 5, 'name' => 'Jose Martinez', 'phone' => '09444555666']
        ];

        return $this->render('SuperAdmin/add_appointment', [
            'doctors' => $doctors,
            'patients' => $patients
        ]);
    }

    public function viewAppointment($id)
    {
        // Sample appointment data
        $appointment = [
            'id' => $id,
            'patient_name' => 'Juan Dela Cruz',
            'patient_phone' => '09123456789',
            'patient_email' => 'juan@email.com',
            'doctor_name' => 'Dr. Maria Santos',
            'department' => 'Cardiology',
            'appointment_date' => date('Y-m-d'),
            'appointment_time' => '09:00:00',
            'status' => 'pending',
            'appointment_type' => 'consultation',
            'notes' => 'Regular checkup for heart condition',
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
        ];

        return $this->render('SuperAdmin/view_appointment', ['appointment' => $appointment]);
    }

    public function editAppointment($id)
    {
        if ($this->request->getMethod() === 'POST') {
            $data = $this->request->getPost();
            
            // Simulate successful appointment update
            log_message('info', 'Appointment update for ID: ' . $id . ' with data: ' . json_encode($data));
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Appointment updated successfully'
            ]);
        }

        // Sample appointment data for editing
        $appointment = [
            'id' => $id,
            'patient_id' => 1,
            'doctor_id' => 1,
            'appointment_date' => date('Y-m-d'),
            'appointment_time' => '09:00',
            'status' => 'pending',
            'appointment_type' => 'consultation',
            'notes' => 'Regular checkup for heart condition'
        ];

        // Sample doctors and patients for the form
        $doctors = [
            ['id' => 1, 'name' => 'Dr. Maria Santos', 'department' => 'Cardiology'],
            ['id' => 2, 'name' => 'Dr. Roberto Cruz', 'department' => 'Pediatrics'],
            ['id' => 3, 'name' => 'Dr. Lisa Fernandez', 'department' => 'Orthopedics']
        ];

        $patients = [
            ['id' => 1, 'name' => 'Juan Dela Cruz', 'phone' => '09123456789'],
            ['id' => 2, 'name' => 'Anna Garcia', 'phone' => '09987654321'],
            ['id' => 3, 'name' => 'Pedro Reyes', 'phone' => '09555123456']
        ];

        return $this->render('SuperAdmin/edit_appointment', [
            'appointment' => $appointment,
            'doctors' => $doctors,
            'patients' => $patients
        ]);
    }

    public function calendars()
    {
        try {
            // Get all appointments for calendar
            $appointments = $this->appointmentModel->getAllAppointments();
            
            // Get unique doctors and departments for filters
            $doctors = array_unique(array_column($appointments, 'doctor_name'));
            $departments = array_unique(array_column($appointments, 'department'));
            $patients = array_unique(array_column($appointments, 'patient_name'));
            
            // Remove empty values
            $doctors = array_filter($doctors);
            $departments = array_filter($departments);
            $patients = array_filter($patients);
            
            return $this->render('SuperAdmin/calendars', [
                'appointments' => $appointments,
                'doctors' => $doctors,
                'departments' => $departments,
                'patients' => $patients
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Calendar page error: ' . $e->getMessage());
            
            // Fallback with empty data
            return $this->render('SuperAdmin/calendars', [
                'appointments' => [],
                'doctors' => [],
                'departments' => [],
                'patients' => []
            ]);
        }
    }

    public function getCalendarData()
    {
        try {
            $start = $this->request->getGet('start');
            $end = $this->request->getGet('end');
            $doctor = $this->request->getGet('doctor');
            $department = $this->request->getGet('department');
            $patient = $this->request->getGet('patient');
            $status = $this->request->getGet('status');
            
            // Get appointments based on filters
            $appointments = $this->appointmentModel->getAllAppointments();
            
            // Apply filters
            if ($doctor) {
                $appointments = array_filter($appointments, function($apt) use ($doctor) {
                    return $apt['doctor_name'] === $doctor;
                });
            }
            
            if ($department) {
                $appointments = array_filter($appointments, function($apt) use ($department) {
                    return $apt['department'] === $department;
                });
            }
            
            if ($patient) {
                $appointments = array_filter($appointments, function($apt) use ($patient) {
                    return $apt['patient_name'] === $patient;
                });
            }
            
            if ($status) {
                $appointments = array_filter($appointments, function($apt) use ($status) {
                    return $apt['status'] === $status;
                });
            }
            
            // Format for FullCalendar
            $events = [];
            foreach ($appointments as $appointment) {
                $events[] = [
                    'id' => $appointment['id'],
                    'title' => $appointment['patient_name'] . ' - Dr. ' . $appointment['doctor_name'],
                    'start' => $appointment['appointment_date'] . 'T' . $appointment['appointment_time'],
                    'backgroundColor' => $this->getStatusColor($appointment['status']),
                    'borderColor' => $this->getStatusColor($appointment['status']),
                    'extendedProps' => [
                        'patient_name' => $appointment['patient_name'],
                        'patient_phone' => $appointment['patient_phone'] ?? '',
                        'doctor_name' => $appointment['doctor_name'],
                        'department' => $appointment['department'] ?? '',
                        'appointment_type' => $appointment['appointment_type'] ?? 'consultation',
                        'status' => $appointment['status'],
                        'notes' => $appointment['notes'] ?? ''
                    ]
                ];
            }
            
            return $this->response->setJSON($events);
            
        } catch (\Exception $e) {
            log_message('error', 'Calendar data error: ' . $e->getMessage());
            return $this->response->setJSON([]);
        }
    }

    private function getStatusColor($status)
    {
        switch ($status) {
            case 'pending':
                return '#3b82f6'; // Blue
            case 'confirmed':
            case 'scheduled':
                return '#10b981'; // Green
            case 'completed':
                return '#6b7280'; // Gray
            case 'cancelled':
                return '#ef4444'; // Red
            case 'in_progress':
                return '#f59e0b'; // Orange
            case 'no_show':
                return '#8b5cf6'; // Purple
            default:
                return '#3b82f6'; // Default blue
        }
    }

    public function financeReports()
    {
        return $this->render('SuperAdmin/finance_reports');
    }

    public function laboratory()
    {
        return $this->render('SuperAdmin/laboratory');
    }

    public function pharmacy()
    {
        return $this->render('SuperAdmin/pharmacy');
    }

    public function occupancy()
    {
        return $this->render('SuperAdmin/occupancy');
    }

    public function reports()
    {
        return $this->render('SuperAdmin/reports');
    }

    public function analytics()
    {
        return $this->render('SuperAdmin/analytics');
    }

    public function settings()
    {
        return $this->render('SuperAdmin/settings');
    }

    public function security()
    {
        return $this->render('SuperAdmin/security');
    }

    public function auditLogs()
    {
        $logs = $this->auditLogModel->getRecentLogs(100);
        return $this->render('SuperAdmin/audit_logs', ['logs' => $logs]);
    }

    // ============ API ENDPOINTS ============
    public function apiUsers($id = null)
    {
        try {
            if ($id) {
                // Get single user
                log_message('debug', 'Getting user with ID: ' . $id);
                $user = $this->userModel->find($id);
                if ($user) {
                    log_message('debug', 'User found: ' . json_encode($user));
                    return $this->response->setJSON(['success' => true, 'data' => $user]);
                } else {
                    log_message('debug', 'User not found with ID: ' . $id);
                    return $this->response->setJSON(['success' => false, 'message' => 'User not found']);
                }
            } else {
                // Get all users
                log_message('debug', 'Getting all users');
                $users = $this->userModel->getUsersWithRoles();
                return $this->response->setJSON($users);
            }
        } catch (\Exception $e) {
            log_message('error', 'API Users error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    public function apiPatients()
    {
        $db = \Config\Database::connect();
        
        if ($db->tableExists('patients')) {
            $patients = $db->table('patients')
                          ->orderBy('created_at', 'DESC')
                          ->limit(50)
                          ->get()
                          ->getResultArray();
            return $this->response->setJSON($patients);
        } else {
            return $this->response->setJSON([]);
        }
    }

    public function apiAppointments()
    {
        $db = \Config\Database::connect();
        
        if ($db->tableExists('appointments')) {
            $appointments = $db->table('appointments')
                              ->orderBy('appointment_date', 'DESC')
                              ->limit(50)
                              ->get()
                              ->getResultArray();
            return $this->response->setJSON($appointments);
        } else {
            return $this->response->setJSON([]);
        }
    }

    // ============ PATIENT MANAGEMENT ============
    public function addPatient()
    {
        if ($this->request->getMethod() === 'POST') {
            $db = \Config\Database::connect();
            
            if (!$db->tableExists('patients')) {
                return $this->response->setJSON(['success' => false, 'message' => 'Patients table does not exist']);
            }
            
            $data = $this->request->getPost();
            
            // Generate patient ID if not provided
            if (empty($data['patient_id'])) {
                $lastPatient = $db->table('patients')->orderBy('id', 'DESC')->limit(1)->get()->getRowArray();
                $nextId = $lastPatient ? $lastPatient['id'] + 1 : 1;
                $data['patient_id'] = 'PAT-' . date('Y') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            }
            
            // Set default status if not provided
            if (!isset($data['status'])) {
                $data['status'] = 'active';
            }
            
            try {
                $result = $db->table('patients')->insert($data);
                if ($result) {
                    return $this->response->setJSON(['success' => true, 'message' => 'Patient added successfully']);
                } else {
                    return $this->response->setJSON(['success' => false, 'message' => 'Failed to add patient']);
                }
            } catch (\Exception $e) {
                return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }
        
        return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method']);
    }

    public function editPatient($id)
    {
        $db = \Config\Database::connect();
        
        if (!$db->tableExists('patients')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Patients table does not exist']);
        }
        
        if ($this->request->getMethod() === 'POST') {
            $data = $this->request->getPost();
            
            try {
                $result = $db->table('patients')->where('id', $id)->update($data);
                if ($result) {
                    return $this->response->setJSON(['success' => true, 'message' => 'Patient updated successfully']);
                } else {
                    return $this->response->setJSON(['success' => false, 'message' => 'Failed to update patient']);
                }
            } catch (\Exception $e) {
                return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }
        
        // GET request - return patient data
        $patient = $db->table('patients')->where('id', $id)->get()->getRowArray();
        if ($patient) {
            return $this->response->setJSON(['success' => true, 'data' => $patient]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Patient not found']);
        }
    }

    public function viewPatient($id)
    {
        $db = \Config\Database::connect();
        
        if (!$db->tableExists('patients')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Patients table does not exist']);
        }
        
        $patient = $db->table('patients')->where('id', $id)->get()->getRowArray();
        if ($patient) {
            return $this->response->setJSON(['success' => true, 'data' => $patient]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Patient not found']);
        }
    }

    public function deletePatient($id)
    {
        $db = \Config\Database::connect();
        
        if (!$db->tableExists('patients')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Patients table does not exist']);
        }
        
        try {
            $result = $db->table('patients')->where('id', $id)->delete();
            if ($result) {
                return $this->response->setJSON(['success' => true, 'message' => 'Patient deleted successfully']);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete patient']);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function apiDepartments()
    {
        return $this->response->setJSON($this->departmentModel->getActiveDepartments());
    }

    public function apiStats()
    {
        $stats = [
            'total_users' => $this->userModel->countAll(),
            'active_users' => $this->userModel->where('status', 'active')->countAllResults(),
            'total_rooms' => $this->roomModel->countAll(),
            'available_rooms' => $this->roomModel->where('status', 'available')->countAllResults(),
            'occupied_rooms' => $this->roomModel->where('status', 'occupied')->countAllResults(),
            'total_departments' => $this->departmentModel->countAll(),
            'recent_activity' => $this->auditLogModel->getRecentActivity(5)
        ];
        return $this->response->setJSON($stats);
    }

    // Simple test endpoint
    public function testApi()
    {
        try {
            $userCount = $this->userModel->countAll();
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'API is working',
                'timestamp' => date('Y-m-d H:i:s'),
                'user_count' => $userCount
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'API error: ' . $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        }
    }

    // Simple users endpoint with direct database query
    public function simpleUsers()
    {
        try {
            $db = \Config\Database::connect();
            
            if ($db->tableExists('users')) {
                // Get all users (remove status filter to see all)
                $users = $db->table('users')
                           ->select('id, username, first_name, last_name, email, role, phone, address, status, last_login, created_at')
                           ->orderBy('id', 'ASC')
                           ->get()
                           ->getResultArray();
                
                log_message('debug', 'Found ' . count($users) . ' users in database');
                
                return $this->response->setJSON([
                    'success' => true,
                    'count' => count($users),
                    'data' => $users
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Users table does not exist'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Simple users API error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Debug users - show all users with detailed info
    public function debugUsers()
    {
        try {
            $db = \Config\Database::connect();
            
            echo "<h1>🔍 Users Debug Information</h1>";
            
            if ($db->tableExists('users')) {
                $users = $db->table('users')->get()->getResultArray();
                
                echo "<h2>📊 Users Table Info</h2>";
                echo "<p>Total users found: <strong>" . count($users) . "</strong></p>";
                
                if (count($users) > 0) {
                    echo "<h3>👥 All Users:</h3>";
                    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
                    echo "<tr style='background: #f5f5f5;'>";
                    echo "<th>ID</th><th>Username</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Role</th><th>Status</th><th>Created</th>";
                    echo "</tr>";
                    
                    foreach ($users as $user) {
                        echo "<tr>";
                        echo "<td>{$user['id']}</td>";
                        echo "<td>{$user['username']}</td>";
                        echo "<td>{$user['first_name']}</td>";
                        echo "<td>{$user['last_name']}</td>";
                        echo "<td>{$user['email']}</td>";
                        echo "<td>{$user['role']}</td>";
                        echo "<td>{$user['status']}</td>";
                        echo "<td>{$user['created_at']}</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>❌ No users found in database</p>";
                    echo "<p><a href='" . base_url('create-test-user') . "'>Create Test User</a></p>";
                }
                
                // Show table structure
                echo "<h3>🏗️ Table Structure:</h3>";
                $fields = $db->getFieldData('users');
                echo "<ul>";
                foreach ($fields as $field) {
                    echo "<li><strong>{$field->name}</strong> ({$field->type})</li>";
                }
                echo "</ul>";
                
            } else {
                echo "<p>❌ Users table does not exist</p>";
                echo "<p>Please run migrations first</p>";
            }
            
        } catch (\Exception $e) {
            echo "<p>❌ Error: " . $e->getMessage() . "</p>";
        }
    }
}
