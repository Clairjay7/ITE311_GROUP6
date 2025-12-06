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
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'dr.anagarcia',
                'email'      => 'a.garcia@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleMap['doctor'],
                'status'     => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'dr.mariasantos',
                'email'      => 'm.santos@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleMap['doctor'],
                'status'     => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'dr.carlosreyes',
                'email'      => 'c.reyes@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleMap['doctor'],
                'status'     => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'dr.luisfelipe',
                'email'      => 'l.felipe@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleMap['doctor'],
                'status'     => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'dr.rosariovillanueva',
                'email'      => 'r.villanueva@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleMap['doctor'],
                'status'     => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'dr.patriciocruz',
                'email'      => 'p.cruz@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleMap['doctor'],
                'status'     => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'dr.elenatorres',
                'email'      => 'e.torres@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleMap['doctor'],
                'status'     => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'dr.ricardomendoza',
                'email'      => 'r.mendoza@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleMap['doctor'],
                'status'     => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'dr.angelicagomez',
                'email'      => 'a.gomez@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleMap['doctor'],
                'status'     => 'active',
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
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Laboratory Staff (using nurse role as there's no specific lab role)
            [
                'username'   => 'lab.tech',
                'email'      => 'laboratory@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleMap['lab_staff'],
                'status'     => 'active',
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
        
        // Create doctor records for all doctor users
        $this->createDoctors();
        
        // Create schedules for all doctors
        $this->createDoctorSchedules();
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

