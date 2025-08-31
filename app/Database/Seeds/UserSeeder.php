<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\UserModel;

class UserSeeder extends Seeder
{
    public function run()
    {
        $userModel = new UserModel();
        $db = \Config\Database::connect();
        
        // Start transaction
        $db->transStart();
        
        try {
            // Truncate users table to start fresh
            $db->table('users')->truncate();
            
            // Define all 8 users with their credentials
            $users = [
                // Super Admin
                [
                    'username' => 'superadmin',
                    'email' => 'superadmin@hospital.com',
                    'password_hash' => password_hash('Admin@123', PASSWORD_DEFAULT),
                    'first_name' => 'Super',
                    'last_name' => 'Admin',
                    'role' => 'super_admin',
                    'status' => 'active',
                ],
                // Doctor
                [
                    'username' => 'doctor1',
                    'email' => 'doctor@hospital.com',
                    'password_hash' => password_hash('Doctor@123', PASSWORD_DEFAULT),
                    'first_name' => 'John',
                    'last_name' => 'Smith',
                    'role' => 'doctor',
                    'status' => 'active',
                ],
                // Nurse
                [
                    'username' => 'nurse1',
                    'email' => 'nurse@hospital.com',
                    'password_hash' => password_hash('Nurse@123', PASSWORD_DEFAULT),
                    'first_name' => 'Sarah',
                    'last_name' => 'Johnson',
                    'role' => 'nurse',
                    'status' => 'active',
                ],
                // Receptionist
                [
                    'username' => 'reception1',
                    'email' => 'reception@hospital.com',
                    'password_hash' => password_hash('Reception@123', PASSWORD_DEFAULT),
                    'first_name' => 'Emma',
                    'last_name' => 'Wilson',
                    'role' => 'receptionist',
                    'status' => 'active',
                ],
                // Laboratory Staff
                [
                    'username' => 'labstaff1',
                    'email' => 'lab@hospital.com',
                    'password_hash' => password_hash('Labstaff@123', PASSWORD_DEFAULT),
                    'first_name' => 'Michael',
                    'last_name' => 'Brown',
                    'role' => 'laboratory_staff',
                    'status' => 'active',
                ],
                // Pharmacist
                [
                    'username' => 'pharmacist1',
                    'email' => 'pharmacy@hospital.com',
                    'password_hash' => password_hash('Pharmacy@123', PASSWORD_DEFAULT),
                    'first_name' => 'David',
                    'last_name' => 'Lee',
                    'role' => 'pharmacist',
                    'status' => 'active',
                ],
                // Accountant
                [
                    'username' => 'accountant1',
                    'email' => 'accounting@hospital.com',
                    'password_hash' => password_hash('Accountant@123', PASSWORD_DEFAULT),
                    'first_name' => 'Jennifer',
                    'last_name' => 'Davis',
                    'role' => 'accountant',
                    'status' => 'active',
                ],
                // IT Staff
                [
                    'username' => 'itstaff1',
                    'email' => 'it@hospital.com',
                    'password_hash' => password_hash('ITstaff@123', PASSWORD_DEFAULT),
                    'first_name' => 'Robert',
                    'last_name' => 'Wilson',
                    'role' => 'it_staff',
                    'status' => 'active',
                ]
            ];

            // Insert all users
            $userModel->insertBatch($users);
            
            // Complete transaction
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            echo "Successfully reset all users with default passwords.\n";
            echo "Here are the login credentials for all users:\n\n";
            
            // Display credentials
            foreach ($users as $user) {
                echo "Username: " . $user['username'] . "\n";
                echo "Password: " . str_replace('@123', '', ucfirst($user['role']) . "@123") . "\n";
                echo "Role: " . ucfirst(str_replace('_', ' ', $user['role'])) . "\n";
                echo "----------------------------------------\n";
            }
            
        } catch (\Exception $e) {
            $db->transRollback();
            die('Error: ' . $e->getMessage() . "\n");
        }
    }
}
