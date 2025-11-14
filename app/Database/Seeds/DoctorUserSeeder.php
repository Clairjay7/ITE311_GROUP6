<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DoctorUserSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // Ensure 'doctor' role exists and get its id
        $role = $this->db->table('roles')->where('name', 'doctor')->get()->getRowArray();
        if (!$role) {
            // Create the role if missing
            $this->db->table('roles')->insert([
                'name' => 'doctor',
                'description' => 'doctor role',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $roleId = (int)$this->db->insertID();
        } else {
            $roleId = (int)$role['id'];
        }

        $users = [
            [
                'username'   => 'dr.santos',
                'email'      => 'm.santos@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleId,
                'status'     => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'dr.rizal',
                'email'      => 'j.rizal@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleId,
                'status'     => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'dr.mercado',
                'email'      => 'j.mercado@group6.edu.ph',
                'password'   => password_hash('123123', PASSWORD_DEFAULT),
                'role_id'    => $roleId,
                'status'     => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        $this->db->table('users')->insertBatch($users);
    }
}
