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
            // Doctor User
            [
                'username'   => 'dr.delacruz',
                'email'      => 'j.delacruz@group6.edu.ph',
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

        // Using Query Builder to insert data
        $this->db->table('users')->insertBatch($users);
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

