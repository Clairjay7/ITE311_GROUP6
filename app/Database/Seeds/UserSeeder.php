<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\UserModel;

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
            // Surgical Specialists
            [
                'username'   => 'dr.generalsurgery',
                'email'      => 'gen.surgery@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleMap['doctor'],
                'status'     => 'active',
                'first_name' => 'Roberto',
                'last_name'  => 'Fernandez',
                'prc_license' => 'PRC-100011',
                'specialization' => 'General Surgery',
                'contact'    => '0912-345-6797',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'dr.orthopedicsurgery',
                'email'      => 'ortho.surgery@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleMap['doctor'],
                'status'     => 'active',
                'first_name' => 'Miguel',
                'last_name'  => 'Ramos',
                'prc_license' => 'PRC-100012',
                'specialization' => 'Orthopedic Surgery',
                'contact'    => '0912-345-6798',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'dr.obgynesurgery',
                'email'      => 'obgyne.surgery@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleMap['doctor'],
                'status'     => 'active',
                'first_name' => 'Carmen',
                'last_name'  => 'Villanueva',
                'prc_license' => 'PRC-100013',
                'specialization' => 'OB-Gyne Surgery',
                'contact'    => '0912-345-6799',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'dr.entsurgery',
                'email'      => 'ent.surgery@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleMap['doctor'],
                'status'     => 'active',
                'first_name' => 'Antonio',
                'last_name'  => 'Lopez',
                'prc_license' => 'PRC-100014',
                'specialization' => 'ENT Surgery',
                'contact'    => '0912-345-6800',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'dr.urologicsurgery',
                'email'      => 'urologic.surgery@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleMap['doctor'],
                'status'     => 'active',
                'first_name' => 'Fernando',
                'last_name'  => 'Martinez',
                'prc_license' => 'PRC-100015',
                'specialization' => 'Urologic Surgery',
                'contact'    => '0912-345-6801',
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
        
        // Use UserModel to ensure proper field filtering
        $userModel = new UserModel();
        
        $usersToInsert = [];
        foreach ($users as $user) {
            if (!in_array($user['username'], $existingUsernames)) {
                $usersToInsert[] = $user;
            }
        }
        
        if (!empty($usersToInsert)) {
            // Insert one by one using UserModel to ensure proper field handling
            foreach ($usersToInsert as $userData) {
                try {
                    $userModel->insert($userData);
                } catch (\Exception $e) {
                    // Log error but continue with other users
                    log_message('error', 'Failed to insert user: ' . $userData['username'] . ' - ' . $e->getMessage());
                }
            }
        }
        
        // Update existing users with new fields based on username
        $this->updateExistingUsers($users);
        
        // Create doctor records for all doctor users
        $this->createDoctors();
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
        // Surgery specialists are mapped to match exactly with surgery types
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
            // Surgical Specialists - These must match exactly with surgery types
            'dr.generalsurgery' => 'General Surgery',
            'dr.orthopedicsurgery' => 'Orthopedic Surgery',
            'dr.obgynesurgery' => 'OB-Gyne Surgery',
            'dr.entsurgery' => 'ENT Surgery',
            'dr.urologicsurgery' => 'Urologic Surgery',
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
            'General Surgery',
            'Orthopedic Surgery',
            'OB-Gyne Surgery',
            'ENT Surgery',
            'Urologic Surgery',
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
            
            // Get specialization - prioritize user's specialization field, then mapping
            $specialization = $user['specialization'] ?? $doctorSpecializations[$username] ?? 'General Practice';
            
            // Use actual name from user record if available, otherwise format from username
            if (!empty($user['first_name']) && !empty($user['last_name'])) {
                $doctorName = 'Dr. ' . $user['first_name'] . ' ' . $user['last_name'];
            } else {
                // Format doctor name from username (fallback)
                $namePart = preg_replace('/^dr\./', '', $username);
                $namePart = str_replace('.', ' ', $namePart);
                $namePart = ucwords($namePart);
                $doctorName = 'Dr. ' . $namePart;
            }
            
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

