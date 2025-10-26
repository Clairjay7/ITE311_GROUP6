<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class DashboardAPI extends Controller
{
    public function users()
    {
        // Set JSON header
        $this->response->setContentType('application/json');
        
        try {
            // Direct database connection
            $db = \Config\Database::connect();
            
            // Get all users
            $query = $db->query("SELECT * FROM users ORDER BY id ASC");
            $users = $query->getResultArray();
            
            // Format users for dashboard
            $formattedUsers = [];
            foreach ($users as $user) {
                $formattedUsers[] = [
                    'id' => (int)$user['id'],
                    'username' => $user['username'] ?? 'N/A',
                    'first_name' => $user['first_name'] ?? '',
                    'last_name' => $user['last_name'] ?? '',
                    'email' => $user['email'] ?? 'N/A',
                    'role' => $user['role'] ?? 'user',
                    'phone' => $user['phone'] ?? 'N/A',
                    'status' => $user['status'] ?? 'active',
                    'last_login' => $user['last_login'] ?? 'Never',
                    'created_at' => $user['created_at'] ?? null
                ];
            }
            
            return $this->response->setJSON([
                'success' => true,
                'count' => count($formattedUsers),
                'data' => $formattedUsers,
                'message' => 'Users loaded successfully'
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'error' => $e->getMessage(),
                'data' => []
            ]);
        }
    }
    
    public function patients()
    {
        $this->response->setContentType('application/json');
        
        try {
            $db = \Config\Database::connect();
            
            // Check if patients table exists and has data
            if ($db->tableExists('patients')) {
                $query = $db->query("SELECT * FROM patients ORDER BY id ASC");
                $patients = $query->getResultArray();
                
                if (count($patients) > 0) {
                    return $this->response->setJSON([
                        'success' => true,
                        'count' => count($patients),
                        'data' => $patients
                    ]);
                }
            }
            
            // If no patients or table doesn't exist, return sample data
            $samplePatients = [
                [
                    'id' => 1,
                    'patient_id' => 'PAT-2024-001',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'middle_name' => 'M',
                    'contact_number' => '09123456789',
                    'phone' => '09123456789',
                    'email' => 'john.doe@email.com',
                    'date_of_birth' => '1990-05-15',
                    'gender' => 'male',
                    'blood_type' => 'O+',
                    'address' => '123 Main St, Manila',
                    'status' => 'active',
                    'created_at' => '2024-01-15 10:30:00'
                ],
                [
                    'id' => 2,
                    'patient_id' => 'PAT-2024-002',
                    'first_name' => 'Jane',
                    'last_name' => 'Smith',
                    'middle_name' => 'A',
                    'contact_number' => '09987654321',
                    'phone' => '09987654321',
                    'email' => 'jane.smith@email.com',
                    'date_of_birth' => '1985-08-22',
                    'gender' => 'female',
                    'blood_type' => 'A+',
                    'address' => '456 Oak Ave, Quezon City',
                    'status' => 'active',
                    'created_at' => '2024-02-10 14:15:00'
                ],
                [
                    'id' => 3,
                    'patient_id' => 'PAT-2024-003',
                    'first_name' => 'Maria',
                    'last_name' => 'Garcia',
                    'middle_name' => 'L',
                    'contact_number' => '09555666777',
                    'phone' => '09555666777',
                    'email' => 'maria.garcia@email.com',
                    'date_of_birth' => '1992-12-03',
                    'gender' => 'female',
                    'blood_type' => 'B+',
                    'address' => '789 Pine St, Makati',
                    'status' => 'active',
                    'created_at' => '2024-03-05 09:45:00'
                ]
            ];
            
            return $this->response->setJSON([
                'success' => true,
                'count' => count($samplePatients),
                'data' => $samplePatients,
                'message' => 'Sample patient data (no database table found)'
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'error' => $e->getMessage(),
                'data' => []
            ]);
        }
    }
    
    public function appointments()
    {
        $this->response->setContentType('application/json');
        
        try {
            $db = \Config\Database::connect();
            
            // Check if appointments table exists, if not create sample data
            if ($db->tableExists('appointments')) {
                $query = $db->query("SELECT * FROM appointments ORDER BY id ASC");
                $appointments = $query->getResultArray();
                
                if (count($appointments) > 0) {
                    return $this->response->setJSON([
                        'success' => true,
                        'count' => count($appointments),
                        'data' => $appointments
                    ]);
                }
            }
            
            // If no appointments or table doesn't exist, return sample data
            $sampleAppointments = [
                [
                    'id' => 1,
                    'appointment_id' => 'APT-001',
                    'patient_name' => 'John Doe',
                    'patient_phone' => '09123456789',
                    'doctor_name' => 'Dr. Smith',
                    'department' => 'Cardiology',
                    'appointment_date' => '2024-10-05',
                    'appointment_time' => '10:00:00',
                    'appointment_type' => 'consultation',
                    'status' => 'scheduled',
                    'reason' => 'Regular checkup'
                ],
                [
                    'id' => 2,
                    'appointment_id' => 'APT-002',
                    'patient_name' => 'Jane Smith',
                    'patient_phone' => '09987654321',
                    'doctor_name' => 'Dr. Johnson',
                    'department' => 'General Medicine',
                    'appointment_date' => '2024-10-06',
                    'appointment_time' => '14:00:00',
                    'appointment_type' => 'follow-up',
                    'status' => 'confirmed',
                    'reason' => 'Follow-up consultation'
                ],
                [
                    'id' => 3,
                    'appointment_id' => 'APT-003',
                    'patient_name' => 'Maria Garcia',
                    'patient_phone' => '09555666777',
                    'doctor_name' => 'Dr. Brown',
                    'department' => 'Pediatrics',
                    'appointment_date' => '2024-10-07',
                    'appointment_time' => '09:30:00',
                    'appointment_type' => 'consultation',
                    'status' => 'pending',
                    'reason' => 'Child wellness check'
                ]
            ];
            
            return $this->response->setJSON([
                'success' => true,
                'count' => count($sampleAppointments),
                'data' => $sampleAppointments,
                'message' => 'Sample appointment data (no database table found)'
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'error' => $e->getMessage(),
                'data' => []
            ]);
        }
    }
}
