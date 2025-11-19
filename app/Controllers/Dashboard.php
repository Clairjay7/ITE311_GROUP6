<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        // Get user role and redirect to appropriate dashboard
        $userRole = strtolower(session()->get('role') ?: 'guest');
        $username = session()->get('username') ?? 'User';

        // Map user roles to existing view directories
        $roleToDir = [
            'admin'        => 'admin',
            'doctor'       => 'doctor',
            'nurse'        => 'nurse',
            'finance'      => 'Accountant',
            'lab_staff'    => 'labstaff',
            'itstaff'      => 'itstaff',
            'pharmacy'     => 'pharmacy',
            'receptionist' => 'Reception',
        ];

        $roleDir = $roleToDir[$userRole] ?? null;
        $viewPath = $roleDir ? ($roleDir . '/dashboard') : 'dashboard/default';

        // Check if the computed view exists, else fallback to default dashboard
        if (! file_exists(APPPATH . 'Views/' . $viewPath . '.php')) {
            $viewPath = 'dashboard/default';
        }

        $data = $this->buildDashboardData($userRole, $username);
        
        // Ensure all required view variables are set with default values
        $viewData = array_merge([
            'totalDoctors' => 0,
            'totalPatients' => 0,
            'todaysAppointments' => 0,
            'pendingBills' => 0
        ], $data);
        
        return view($viewPath, $viewData);
    }

    private function buildDashboardData(string $userRole, string $username): array
    {
        $today = date('Y-m-d');
        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-t');

        // Default data structure
        $data = [
            'userRole' => $userRole,
            'username' => $username,
            'appointmentsCount' => 0,
            'patientsCount' => 0,
            'totalDoctors' => 0,
            'totalPatients' => 0,
            'newPatientsToday' => 0,
            'activeCases' => 0,
            'todayRevenue' => 0.0,
            'paidThisMonth' => 0.0,
            'outstanding' => 0.0,
            'pendingBills' => 0,
            'insuranceClaims' => 0,
            'outstandingBalance' => 0.0,
            'prescriptionsCount' => 0,
            'labTestsCount' => 0,
            'labStats' => [],
            'userCounts' => [],
            'systemStatus' => 'Online',
            'roles' => [
                'admin' => 'Administrator',
                'doctor' => 'Doctor',
                'nurse' => 'Nurse',
                'finance' => 'Finance',
                'lab_staff' => 'Laboratory Staff',
                'itstaff' => 'IT Staff',
                'pharmacy' => 'Pharmacy',
                'receptionist' => 'Receptionist'
            ],
            // Lab staff specific data
            'pendingTests' => [],
            'completedToday' => 0,
            'monthlyTests' => 0
        ];

        // If user is lab staff, load lab-specific data
        if ($userRole === 'lab_staff') {
            // In a real application, you would fetch these from your database
            // For example:
            // $labTestModel = new \App\Models\LabTestModel();
            // $data['pendingTests'] = $labTestModel->where('status', 'pending')->findAll();
            // $data['completedToday'] = $labTestModel->where('status', 'completed')
            //     ->where('DATE(completed_at)', $today)
            //     ->countAllResults();
            // $data['monthlyTests'] = $labTestModel->where('created_at >=', $monthStart)
            //     ->where('created_at <=', $monthEnd)
            //     ->countAllResults();
            
            // For now, we'll set some dummy data
            $data['pendingTests'] = [];
            $data['completedToday'] = 0;
            $data['monthlyTests'] = 0;
        }

        return $data;

        // Optional models (may not exist in all setups)
        $prescriptionModel = class_exists('App\\Models\\PrescriptionModel') ? new \App\Models\PrescriptionModel() : null;
        $labRequestModel = class_exists('App\\Models\\LabRequestModel') ? new \App\Models\LabRequestModel() : null;

        try {
            // Appointments & Patients
            if (in_array($userRole, ['admin', 'receptionist', 'nurse', 'doctor'])) {
                // Today's appointments (exclude cancelled/no_show)
                $data['appointmentsCount'] = $appointmentModel
                    ->where('appointment_date', $today)
                    ->whereNotIn('status', ['cancelled','no_show'])
                    ->countAllResults();

                $data['patientsCount'] = $patientModel->countAllResults();
                // New patients today
                if ($patientModel->db->fieldExists('created_at', 'patients')) {
                    $data['newPatientsToday'] = $patientModel->builder()
                        ->select('COUNT(*) AS c')
                        ->where('DATE(created_at)', $today)
                        ->get()->getRow('c') ?? 0;
                }
            }

            // Active cases: confirmed or in_progress today
            if (in_array($userRole, ['admin', 'doctor', 'nurse'])) {
                $data['activeCases'] = $appointmentModel
                    ->where('appointment_date', $today)
                    ->whereIn('status', ['confirmed','in_progress'])
                    ->countAllResults();
            }

            // Billing totals (normalized schema)
            if (in_array($userRole, ['admin', 'finance'])) {
                // Today's paid revenue
                $data['todayRevenue'] = (float) ($billingModel->builder()
                    ->selectSum('final_amount', 'sum')
                    ->where('payment_status', 'paid')
                    ->where('bill_date', $today)
                    ->get()->getRow('sum') ?? 0);

                // Monthly and outstanding via helper
                if (method_exists($billingModel, 'getTotals')) {
                    $totals = $billingModel->getTotals();
                    $data['paidThisMonth'] = (float) ($totals['paidThisMonth'] ?? 0);
                    $data['outstanding'] = (float) ($totals['outstanding'] ?? 0);
                    $data['pendingBills'] = (int) ($totals['pendingCount'] ?? 0);
                } else {
                    // Fallback if helper not available
                    $data['paidThisMonth'] = (float) ($billingModel->builder()
                        ->selectSum('final_amount', 'sum')
                        ->where('payment_status', 'paid')
                        ->where('bill_date >=', $monthStart)
                        ->where('bill_date <=', $monthEnd)
                        ->get()->getRow('sum') ?? 0);

                    $data['outstanding'] = (float) ($billingModel->builder()
                        ->selectSum('final_amount', 'sum')
                        ->where('payment_status', 'pending')
                        ->get()->getRow('sum') ?? 0);

                    $data['pendingBills'] = (int) ($billingModel->builder()
                        ->select('COUNT(*) AS c')
                        ->where('payment_status', 'pending')
                        ->get()->getRow('c') ?? 0);
                }
            }

            // Prescriptions pending
            if ($prescriptionModel && in_array($userRole, ['admin', 'pharmacy'])) {
                $data['prescriptionsCount'] = (int) $prescriptionModel->builder()
                    ->select('COUNT(*) AS c')
                    ->where('status', 'pending')
                    ->get()->getRow('c');
            }

            // Laboratory pending stats
            if ($labRequestModel && in_array($userRole, ['admin', 'labstaff'])) {
                $pending = (int) $labRequestModel->builder()
                    ->select('COUNT(*) AS c')
                    ->where('status', 'pending')
                    ->get()->getRow('c');
                $data['labTestsCount'] = $pending; // backward compat
                $data['labStats'] = ['pending' => $pending];
            }

            // User counts by role (for Users Total)
            $db = \Config\Database::connect();
            if ($db->tableExists('users') && $db->tableExists('roles')) {
                $rows = $db->table('users u')
                    ->select('r.name AS role, COUNT(u.id) AS cnt')
                    ->join('roles r', 'r.id = u.role_id', 'left')
                    ->groupBy('r.name')
                    ->get()->getResultArray();
                $counts = [];
                foreach ($rows as $r) { 
                    $role = strtolower($r['role'] ?: 'unknown');
                    $counts[$role] = (int)$r['cnt']; 
                    
                    // Set totalDoctors if role is doctor
                    if ($role === 'doctor') {
                        $data['totalDoctors'] = (int)$r['cnt'];
                    }
                }
                $data['userCounts'] = $counts;
                
                // If we didn't find any doctors in the roles, try to get the count directly
                if ($data['totalDoctors'] === 0) {
                    $doctorRoleId = $db->table('roles')
                        ->select('id')
                        ->where('LOWER(name)', 'doctor')
                        ->get()
                        ->getRow();
                        
                    if ($doctorRoleId) {
                        $data['totalDoctors'] = (int)$db->table('users')
                            ->where('role_id', $doctorRoleId->id)
                            ->countAllResults();
                    }
                }
            }
            
            // Get total patients if the table exists
            if ($db->tableExists('patients')) {
                $data['totalPatients'] = (int)$db->table('patients')
                    ->countAllResults();
            }
        } catch (\Throwable $e) {
            log_message('error', 'Error loading dashboard data: ' . $e->getMessage());
        }

        return $data;
    }
}
