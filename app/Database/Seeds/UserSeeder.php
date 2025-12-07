<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // First, ensure all roles exist
        $this->ensureRolesExist();

        // Get all roles as name => id map
        $roles = $this->db->table('roles')->get()->getResultArray();
        $roleMap = [];
        foreach ($roles as $role) {
            $roleMap[$role['name']] = (int)$role['id'];
        }

        $users = [
            // Admin User
            [
                'username'   => 'sys.admin',
                'email'      => 'sysadmin@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleMap['admin'],
                'status'     => 'active',
                'first_name' => 'System',
                'last_name'  => 'Administrator',
                'employee_id' => 'EMP-000001',
                'contact'    => '0912-345-6789',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Doctor Users (10 doctors with different specializations)
            [
                'username'   => 'dr.delacruz',
                'email'      => 'j.delacruz@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleMap['doctor'],
                'status'     => 'active',
                'first_name' => 'Juan',
                'last_name'  => 'Dela Cruz',
                'prc_license' => 'PRC-100001',
                'specialization' => 'General Practice',
                'contact'    => '0912-345-6781',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'dr.anagarcia',
                'email'      => 'a.garcia@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleMap['doctor'],
                'status'     => 'active',
                'first_name' => 'Ana',
                'last_name'  => 'Garcia',
                'prc_license' => 'PRC-100002',
                'specialization' => 'Pediatrics',
                'contact'    => '0912-345-6782',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'dr.mariasantos',
                'email'      => 'm.santos@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleMap['doctor'],
                'status'     => 'active',
                'first_name' => 'Maria',
                'last_name'  => 'Santos',
                'prc_license' => 'PRC-100003',
                'specialization' => 'Cardiology',
                'contact'    => '0912-345-6783',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'dr.carlosreyes',
                'email'      => 'c.reyes@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleMap['doctor'],
                'status'     => 'active',
                'first_name' => 'Carlos',
                'last_name'  => 'Reyes',
                'prc_license' => 'PRC-100004',
                'specialization' => 'Orthopedics',
                'contact'    => '0912-345-6784',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'dr.luisfelipe',
                'email'      => 'l.felipe@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleMap['doctor'],
                'status'     => 'active',
                'first_name' => 'Luis',
                'last_name'  => 'Felipe',
                'prc_license' => 'PRC-100005',
                'specialization' => 'Neurology',
                'contact'    => '0912-345-6785',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'dr.rosariovillanueva',
                'email'      => 'r.villanueva@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleMap['doctor'],
                'status'     => 'active',
                'first_name' => 'Rosario',
                'last_name'  => 'Villanueva',
                'prc_license' => 'PRC-100006',
                'specialization' => 'Internal Medicine',
                'contact'    => '0912-345-6786',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'dr.patriciocruz',
                'email'      => 'p.cruz@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleMap['doctor'],
                'status'     => 'active',
                'first_name' => 'Patricio',
                'last_name'  => 'Cruz',
                'prc_license' => 'PRC-100007',
                'specialization' => 'Dermatology',
                'contact'    => '0912-345-6787',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'dr.elenatorres',
                'email'      => 'e.torres@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleMap['doctor'],
                'status'     => 'active',
                'first_name' => 'Elena',
                'last_name'  => 'Torres',
                'prc_license' => 'PRC-100008',
                'specialization' => 'Obstetrics and Gynecology',
                'contact'    => '0912-345-6788',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'dr.ricardomendoza',
                'email'      => 'r.mendoza@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleMap['doctor'],
                'status'     => 'active',
                'first_name' => 'Ricardo',
                'last_name'  => 'Mendoza',
                'prc_license' => 'PRC-100009',
                'specialization' => 'Surgery',
                'contact'    => '0912-345-6789',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'dr.angelicagomez',
                'email'      => 'a.gomez@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleMap['doctor'],
                'status'     => 'active',
                'first_name' => 'Angelica',
                'last_name'  => 'Gomez',
                'prc_license' => 'PRC-100010',
                'specialization' => 'Pediatrics',
                'contact'    => '0912-345-6790',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Nurse User
            [
                'username'   => 'nurse.reyes',
                'email'      => 'm.reyes@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleMap['nurse'],
                'status'     => 'active',
                'first_name' => 'Maria',
                'last_name'  => 'Reyes',
                'nursing_license' => 'NUR-200001',
                'contact'    => '0912-345-6791',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Receptionist User
            [
                'username'   => 'frontdesk1',
                'email'      => 'frontdesk@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleMap['receptionist'],
                'status'     => 'active',
                'first_name' => 'Maria',
                'last_name'  => 'Cruz',
                'employee_id' => 'EMP-300001',
                'contact'    => '0912-345-6792',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Accounting User
            [
                'username'   => 'acct.dept',
                'email'      => 'accounting@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleMap['finance'],
                'status'     => 'active',
                'first_name' => 'Juan',
                'last_name'  => 'Santos',
                'employee_id' => 'EMP-400001',
                'contact'    => '0912-345-6793',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // IT Staff User
            [
                'username'   => 'it.support',
                'email'      => 'itsupport@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleMap['itstaff'],
                'status'     => 'active',
                'first_name' => 'John',
                'last_name'  => 'Doe',
                'employee_id' => 'EMP-500001',
                'contact'    => '0912-345-6794',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Laboratory Staff
            [
                'username'   => 'lab.tech',
                'email'      => 'laboratory@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleMap['lab_staff'],
                'status'     => 'active',
                'first_name' => 'Pedro',
                'last_name'  => 'Garcia',
                'prc_license' => 'PRC-600001',
                'contact'    => '0912-345-6795',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Pharmacy Staff
            [
                'username'   => 'pharm.staff',
                'email'      => 'pharmacy@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleMap['pharmacy'],
                'status'     => 'active',
                'first_name' => 'Ana',
                'last_name'  => 'Lopez',
                'prc_license' => 'PRC-700001',
                'contact'    => '0912-345-6796',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Check for existing users to prevent duplicates
        $existingUsernames = $this->db->table('users')->select('username')->get()->getResultArray();
        $existingUsernames = array_column($existingUsernames, 'username');
        
        $usersToInsert = [];
        foreach ($users as $user) {
            if (!in_array($user['username'], $existingUsernames)) {
                $usersToInsert[] = $user;
            }
        }
        
        if (!empty($usersToInsert)) {
            // Using Query Builder to insert data
            $this->db->table('users')->insertBatch($usersToInsert);
        }
        
        // Update existing users with new fields based on username
        $this->updateExistingUsers($users);
        
        // Create doctor records for all doctor users
        $this->createDoctors();
        
        // Create schedules for all doctors
        $this->createDoctorSchedules();
    }
    
    /**
     * Update existing users with new fields (first_name, last_name, contact, etc.)
     */
    protected function updateExistingUsers($users)
    {
        foreach ($users as $userData) {
            $username = $userData['username'];
            
            // Check if user exists
            $existingUser = $this->db->table('users')
                ->where('username', $username)
                ->get()
                ->getRowArray();
            
            if ($existingUser) {
                // Prepare update data (exclude password, created_at, updated_at from update)
                $updateData = [];
                if (isset($userData['first_name'])) {
                    $updateData['first_name'] = $userData['first_name'];
                }
                if (isset($userData['middle_name'])) {
                    $updateData['middle_name'] = $userData['middle_name'];
                }
                if (isset($userData['last_name'])) {
                    $updateData['last_name'] = $userData['last_name'];
                }
                if (isset($userData['contact'])) {
                    $updateData['contact'] = $userData['contact'];
                }
                if (isset($userData['address'])) {
                    $updateData['address'] = $userData['address'];
                }
                if (isset($userData['employee_id'])) {
                    $updateData['employee_id'] = $userData['employee_id'];
                }
                if (isset($userData['prc_license'])) {
                    $updateData['prc_license'] = $userData['prc_license'];
                }
                if (isset($userData['nursing_license'])) {
                    $updateData['nursing_license'] = $userData['nursing_license'];
                }
                if (isset($userData['specialization'])) {
                    $updateData['specialization'] = $userData['specialization'];
                }
                
                // Only update if there's data to update
                if (!empty($updateData)) {
                    $updateData['updated_at'] = date('Y-m-d H:i:s');
                    $this->db->table('users')
                        ->where('username', $username)
                        ->update($updateData);
                }
            }
        }
    }
    
    /**
     * Create doctor records in doctors table for all doctor users
     */
    protected function createDoctors()
    {
        $now = date('Y-m-d H:i:s');
        
        // Get all doctor users
        $roleMap = [];
        $roles = $this->db->table('roles')->get()->getResultArray();
        foreach ($roles as $role) {
            $roleMap[$role['name']] = (int)$role['id'];
        }
        
        $doctorRoleId = $roleMap['doctor'] ?? null;
        if (!$doctorRoleId) {
            return;
        }
        
        $doctorUsers = $this->db->table('users')
            ->where('role_id', $doctorRoleId)
            ->get()
            ->getResultArray();
        
        // Doctor specializations mapping (standardized and valid)
        $doctorSpecializations = [
            'dr.delacruz' => 'General Practice',
            'dr.anagarcia' => 'Pediatrics',
            'dr.mariasantos' => 'Cardiology',
            'dr.carlosreyes' => 'Orthopedics',
            'dr.luisfelipe' => 'Neurology',
            'dr.rosariovillanueva' => 'Internal Medicine',
            'dr.patriciocruz' => 'Dermatology',
            'dr.elenatorres' => 'Obstetrics and Gynecology',
            'dr.ricardomendoza' => 'Surgery',
            'dr.angelicagomez' => 'Pediatrics',
        ];
        
        // Valid specializations list (for validation)
        $validSpecializations = [
            'General Practice',
            'Pediatrics',
            'Cardiology',
            'Orthopedics',
            'Neurology',
            'Internal Medicine',
            'Dermatology',
            'Obstetrics and Gynecology',
            'Surgery',
            'Emergency Medicine',
            'Psychiatry',
            'Radiology',
            'Anesthesiology',
            'Pathology',
        ];
        
        foreach ($doctorUsers as $user) {
            $username = strtolower($user['username']);
            
            // Check if doctor record already exists
            $existingDoctor = $this->db->table('doctors')
                ->where('user_id', $user['id'])
                ->get()
                ->getRowArray();
            
            if ($existingDoctor) {
                continue; // Skip if already exists
            }
            
            // Get specialization from mapping
            $specialization = $doctorSpecializations[$username] ?? 'General Practice';
            
            // Format doctor name from username
            $namePart = preg_replace('/^dr\./', '', $username);
            $namePart = str_replace('.', ' ', $namePart);
            $namePart = ucwords($namePart);
            $doctorName = 'Dr. ' . $namePart;
            
            // Insert doctor record
            $doctorData = [
                'doctor_name' => $doctorName,
                'specialization' => $specialization,
                'user_id' => $user['id'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
            
            $this->db->table('doctors')->insert($doctorData);
        }
        
        // Create schedules for all doctors
        $this->createDoctorSchedules();
    }
    
    /**
     * Create doctor schedules for all doctors
     * Working days: Monday to Friday
     * Working hours: 9:00 AM - 12:00 PM and 1:00 PM - 4:00 PM
     */
    protected function createDoctorSchedules()
    {
        // Check if table exists
        if (!$this->db->tableExists('doctor_schedules')) {
            return; // Skip if table doesn't exist
        }
        
        // Get all doctor users
        $roleMap = [];
        $roles = $this->db->table('roles')->get()->getResultArray();
        foreach ($roles as $role) {
            $roleMap[$role['name']] = (int)$role['id'];
        }
        
        $doctorRoleId = $roleMap['doctor'] ?? null;
        if (!$doctorRoleId) {
            return;
        }
        
        $doctorUsers = $this->db->table('users')
            ->where('role_id', $doctorRoleId)
            ->get()
            ->getResultArray();
        
        // Generate schedules for next 6 months (or current year if preferred)
        $startDate = date('Y-m-d'); // Start from today
        $endDate = date('Y-m-d', strtotime('+6 months')); // 6 months ahead
        
        $startDateObj = new \DateTime($startDate);
        $endDateObj = new \DateTime($endDate);
        $endDateObj->modify('+1 day'); // Include the end date
        
        $now = date('Y-m-d H:i:s');
        
        foreach ($doctorUsers as $user) {
            $doctorId = $user['id'];
            
            // Check if schedule already exists for this doctor
            $existingCount = $this->db->table('doctor_schedules')
                ->where('doctor_id', $doctorId)
                ->where('shift_date >=', $startDate)
                ->where('shift_date <=', $endDate)
                ->countAllResults();
            
            // Skip if schedule already exists
            if ($existingCount > 0) {
                continue;
            }
            
            $schedulesToInsert = [];
            $currentDate = clone $startDateObj;
            
            while ($currentDate < $endDateObj) {
                $dayOfWeek = (int)$currentDate->format('w'); // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
                
                // Only generate schedule for Monday (1) to Friday (5)
                if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
                    $dateStr = $currentDate->format('Y-m-d');
                    
                    // Morning shift: 9:00 AM - 12:00 PM
                    $schedulesToInsert[] = [
                        'doctor_id' => $doctorId,
                        'shift_date' => $dateStr,
                        'start_time' => '09:00:00',
                        'end_time' => '12:00:00',
                        'status' => 'active',
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                    
                    // Afternoon shift: 1:00 PM - 4:00 PM
                    $schedulesToInsert[] = [
                        'doctor_id' => $doctorId,
                        'shift_date' => $dateStr,
                        'start_time' => '13:00:00',
                        'end_time' => '16:00:00',
                        'status' => 'active',
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                }
                
                $currentDate->modify('+1 day');
            }
            
            // Batch insert schedules (insert in chunks of 100 to avoid memory issues)
            if (!empty($schedulesToInsert)) {
                $chunks = array_chunk($schedulesToInsert, 100);
                foreach ($chunks as $chunk) {
                    $this->db->table('doctor_schedules')->insertBatch($chunk);
                }
            }
        }
    }

    /**
     * Ensure all required roles exist in the database
     */
    protected function ensureRolesExist()
    {
        $requiredRoles = [
            'admin',
            'doctor',
            'nurse',
            'receptionist',
            'patient',
            'finance',
            'itstaff',
            'lab_staff',
            'pharmacy'
        ];
        $existingRoles = $this->db->table('roles')->select('name')->get()->getResultArray();
        $existingRoles = array_column($existingRoles, 'name');
        
        $now = date('Y-m-d H:i:s');
        $rolesToAdd = [];
        
        foreach ($requiredRoles as $role) {
            if (!in_array($role, $existingRoles)) {
                $rolesToAdd[] = [
                    'name' => $role,
                    'description' => ucfirst($role) . ' role',
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }
        }
        
        if (!empty($rolesToAdd)) {
            $this->db->table('roles')->insertBatch($rolesToAdd);
        }
    }
}

