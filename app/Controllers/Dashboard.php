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
            'name' => $username,
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
            'monthlyTests' => 0,
            // Doctor specific
            'patientsSeenToday' => 0,
            'pendingLabResults' => 0,
            // Nurse specific
            'criticalPatients' => 0,
            'patientsUnderCare' => 0,
            'medicationsDue' => 0,
            'vitalsPending' => 0,
            // Pharmacy specific
            'prescriptionsToday' => 0,
            'pendingFulfillment' => 0,
            'lowStockItems' => 0,
            'totalInventory' => 0,
            'criticalItems' => 0,
            'expiringSoon' => 0,
            'outOfStock' => 0,
            'categoriesCount' => 0,
            // IT Staff specific
            'systemUptime' => '99.8%',
            'activeUsers' => 0,
            'systemAlerts' => 0,
            'pendingTasks' => 0,
        ];

        // Initialize models
        $appointmentModel = new \App\Models\AppointmentModel();
        $patientModel = new \App\Models\HMSPatientModel();
        $billingModel = new \App\Models\BillingModel();
        $labServiceModel = new \App\Models\LabServiceModel();
        $pharmacyModel = new \App\Models\PharmacyModel();
        $stockModel = new \App\Models\StockModel();
        $scheduleModel = new \App\Models\ScheduleModel();
        $db = \Config\Database::connect();

        try {
            // Appointments & Patients
            if (in_array($userRole, ['admin', 'receptionist', 'nurse', 'doctor'])) {
                // Today's appointments (exclude cancelled/no_show)
                if ($db->tableExists('appointments')) {
                    $data['appointmentsCount'] = $appointmentModel
                        ->where('appointment_date', $today)
                        ->whereNotIn('status', ['cancelled','no_show'])
                        ->countAllResults();
                }

                if ($db->tableExists('patients')) {
                    $data['patientsCount'] = $patientModel->countAllResults();
                    // New patients today
                    $data['newPatientsToday'] = $patientModel->builder()
                        ->select('COUNT(*) AS c')
                        ->where('DATE(created_at)', $today)
                        ->get()->getRow('c') ?? 0;
                }
            }

            // Active cases: confirmed or in_progress today
            if (in_array($userRole, ['admin', 'doctor', 'nurse'])) {
                if ($db->tableExists('appointments')) {
                    $data['activeCases'] = $appointmentModel
                        ->where('appointment_date', $today)
                        ->whereIn('status', ['confirmed','in_progress'])
                        ->countAllResults();
                }
            }

            // Doctor Dashboard - Specific data
            if ($userRole === 'doctor') {
                $doctorId = session()->get('user_id');
                
                // Today's appointments for this doctor
                if ($db->tableExists('appointments') && $doctorId) {
                    $data['appointmentsCount'] = $appointmentModel
                        ->where('doctor_id', $doctorId)
                        ->where('appointment_date', $today)
                        ->whereNotIn('status', ['cancelled','no_show'])
                        ->countAllResults();
                    
                    // Patients seen today (completed appointments)
                    $data['patientsSeenToday'] = $appointmentModel
                        ->where('doctor_id', $doctorId)
                        ->where('appointment_date', $today)
                        ->where('status', 'completed')
                        ->countAllResults();
                }
                
                // Get assigned patients from admin_patients table
                $adminPatientModel = new \App\Models\AdminPatientModel();
                if ($db->tableExists('admin_patients') && $doctorId) {
                    $assignedPatients = $adminPatientModel
                        ->where('doctor_id', $doctorId)
                        ->findAll();
                    $data['assignedPatients'] = $assignedPatients;
                    $data['assignedPatientsCount'] = count($assignedPatients);
                    
                    // Get patient IDs for lab results query
                    $assignedPatientIds = array_column($assignedPatients, 'id');
                    
                    // Pending lab results for this doctor's assigned patients
                    if ($db->tableExists('lab_services') && !empty($assignedPatientIds)) {
                        $data['pendingLabResults'] = $labServiceModel->builder()
                            ->whereIn('patient_id', $assignedPatientIds)
                            ->groupStart()
                            ->where('result IS NULL')
                            ->orWhere('result', '')
                            ->groupEnd()
                            ->countAllResults();
                    } else {
                        $data['pendingLabResults'] = 0;
                    }
                } else {
                    $data['assignedPatients'] = [];
                    $data['assignedPatientsCount'] = 0;
                    $data['pendingLabResults'] = 0;
                }
                
                // Prescriptions count (if prescriptions table exists)
                if ($db->tableExists('prescriptions')) {
                    $data['prescriptionsCount'] = $db->table('prescriptions')
                        ->where('status', 'pending')
                        ->countAllResults();
                }
            }

            // Nurse Dashboard - Specific data
            if ($userRole === 'nurse') {
                // Critical patients (patients with critical status or in ICU)
                if ($db->tableExists('patients')) {
                    $data['criticalPatients'] = $patientModel->builder()
                        ->where('status', 'critical')
                        ->orLike('room_number', 'ICU')
                        ->countAllResults();
                    
                    // Patients under care (inpatients)
                    $data['patientsUnderCare'] = $patientModel->builder()
                        ->where('type', 'In-Patient')
                        ->countAllResults();
                }
                
                // Medications due (if medications table exists)
                if ($db->tableExists('medications')) {
                    $data['medicationsDue'] = $db->table('medications')
                        ->where('due_date <=', $today)
                        ->where('status', 'pending')
                        ->countAllResults();
                } else {
                    $data['medicationsDue'] = 0;
                }
                
                // Vitals pending (if vitals table exists)
                if ($db->tableExists('vitals')) {
                    $data['vitalsPending'] = $db->table('vitals')
                        ->where('DATE(created_at)', $today)
                        ->where('status', 'pending')
                        ->countAllResults();
                } else {
                    $data['vitalsPending'] = 0;
                }
            }

            // Accountant/Finance Dashboard - Billing data
            if (in_array($userRole, ['admin', 'finance'])) {
                if ($db->tableExists('billing')) {
                    // Today's revenue (paid bills)
                    $todayRevenue = $billingModel->builder()
                        ->selectSum('amount', 'sum')
                        ->where('status', 'paid')
                        ->where('DATE(created_at)', $today)
                        ->get()->getRow('sum');
                    $data['todayRevenue'] = (float) ($todayRevenue ?? 0);

                    // Pending bills
                    $pendingBillsData = $billingModel->where('status', 'pending')->findAll();
                    $data['pendingBills'] = is_array($pendingBillsData) ? count($pendingBillsData) : (int)$pendingBillsData;
                    
                    // Outstanding balance (sum of pending bills)
                    $outstanding = $billingModel->builder()
                        ->selectSum('amount', 'sum')
                        ->where('status', 'pending')
                        ->get()->getRow('sum');
                    $data['outstandingBalance'] = (float) ($outstanding ?? 0);
                    
                    // Paid this month
                    $paidThisMonth = $billingModel->builder()
                        ->selectSum('amount', 'sum')
                        ->where('status', 'paid')
                        ->where('created_at >=', $monthStart)
                        ->where('created_at <=', $monthEnd)
                        ->get()->getRow('sum');
                    $data['paidThisMonth'] = (float) ($paidThisMonth ?? 0);
                }
                
                // Insurance claims (if insurance_claims table exists)
                if ($db->tableExists('insurance_claims')) {
                    $insuranceClaims = $db->table('insurance_claims')
                        ->where('status', 'pending')
                        ->findAll();
                    $data['insuranceClaims'] = is_array($insuranceClaims) ? count($insuranceClaims) : 0;
                } else {
                    $data['insuranceClaims'] = 0;
                }
            }

            // Pharmacy Dashboard - Specific data
            if ($userRole === 'pharmacy') {
                // Prescriptions today
                if ($db->tableExists('prescriptions')) {
                    $data['prescriptionsToday'] = $db->table('prescriptions')
                        ->where('DATE(created_at)', $today)
                        ->countAllResults();
                    
                    // Pending fulfillment
                    $data['pendingFulfillment'] = $db->table('prescriptions')
                        ->where('status', 'pending')
                        ->countAllResults();
                } else {
                    $data['prescriptionsToday'] = 0;
                    $data['pendingFulfillment'] = 0;
                }
                
                // Pharmacy inventory stats
                if ($db->tableExists('pharmacy')) {
                    $data['totalInventory'] = $pharmacyModel->countAllResults();
                    
                    // Low stock items (quantity <= threshold, but we'll use a simple check)
                    $allItems = $pharmacyModel->findAll();
                    $lowStock = 0;
                    foreach ($allItems as $item) {
                        if (isset($item['quantity']) && $item['quantity'] < 10) {
                            $lowStock++;
                        }
                    }
                    $data['lowStockItems'] = $lowStock;
                    
                    // Critical items (quantity = 0)
                    $data['criticalItems'] = $pharmacyModel->builder()
                        ->where('quantity', 0)
                        ->countAllResults();
                    
                    // Out of stock
                    $data['outOfStock'] = $data['criticalItems'];
                    
                    // Categories count
                    $categories = $pharmacyModel->builder()
                        ->select('DISTINCT category')
                        ->get()->getResultArray();
                    $data['categoriesCount'] = count($categories);
                    
                    // Expiring soon (if expiry_date field exists)
                    if ($db->fieldExists('expiry_date', 'pharmacy')) {
                        $futureDate = date('Y-m-d', strtotime('+30 days'));
                        $data['expiringSoon'] = $pharmacyModel->builder()
                            ->where('expiry_date >=', $today)
                            ->where('expiry_date <=', $futureDate)
                            ->countAllResults();
                    } else {
                        $data['expiringSoon'] = 0;
                    }
                }
            }

            // Lab Staff Dashboard - Specific data
            if ($userRole === 'lab_staff' || $userRole === 'labstaff') {
                $labRequestModel = new \App\Models\LabRequestModel();
                
                if ($db->tableExists('lab_requests')) {
                    // Pending test requests
                    $data['pendingTests'] = $labRequestModel
                        ->where('status', 'pending')
                        ->countAllResults();
                    
                    // Pending specimens (pending or in_progress)
                    $data['pendingSpecimens'] = $labRequestModel
                        ->whereIn('status', ['pending', 'in_progress'])
                        ->countAllResults();
                    
                    // Completed today
                    $data['completedToday'] = $labRequestModel
                        ->where('status', 'completed')
                        ->where('DATE(updated_at)', $today)
                        ->countAllResults();
                    
                    // Monthly tests
                    $data['monthlyTests'] = $labRequestModel
                        ->where('status', 'completed')
                        ->where('DATE(updated_at) >=', $monthStart)
                        ->countAllResults();
                } else {
                    // Fallback to lab_services if lab_requests doesn't exist
                    if ($db->tableExists('lab_services')) {
                        // Pending tests (tests without results)
                        $pendingTests = $labServiceModel->builder()
                            ->groupStart()
                            ->where('result IS NULL')
                            ->orWhere('result', '')
                            ->groupEnd()
                            ->get()->getResultArray();
                        $data['pendingTests'] = is_array($pendingTests) ? $pendingTests : [];
                        $data['pendingSpecimens'] = count($pendingTests);
                        
                        // Completed today (tests with results created today)
                        $data['completedToday'] = $labServiceModel->builder()
                            ->where('DATE(created_at)', $today)
                            ->groupStart()
                            ->where('result IS NOT NULL')
                            ->where('result !=', '')
                            ->groupEnd()
                            ->countAllResults();
                        
                        // Monthly tests
                        $data['monthlyTests'] = $labServiceModel->builder()
                            ->where('created_at >=', $monthStart)
                            ->where('created_at <=', $monthEnd)
                            ->countAllResults();
                    } else {
                        $data['pendingTests'] = 0;
                        $data['pendingSpecimens'] = 0;
                        $data['completedToday'] = 0;
                        $data['monthlyTests'] = 0;
                    }
                }
            }

            // IT Staff Dashboard - System stats
            if ($userRole === 'itstaff') {
                // Active users (users logged in today or recently)
                if ($db->tableExists('users')) {
                    $data['activeUsers'] = $db->table('users')
                        ->where('last_login >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
                        ->countAllResults();
                }
                
                // System alerts (if system_alerts table exists)
                if ($db->tableExists('system_alerts')) {
                    $data['systemAlerts'] = $db->table('system_alerts')
                        ->where('status', 'active')
                        ->countAllResults();
                } else {
                    $data['systemAlerts'] = 0;
                }
                
                // Pending tasks (if tasks table exists)
                if ($db->tableExists('tasks')) {
                    $data['pendingTasks'] = $db->table('tasks')
                        ->where('status', 'pending')
                        ->countAllResults();
                } else {
                    $data['pendingTasks'] = 0;
                }
                
                // System uptime (calculated or from config)
                $data['systemUptime'] = '99.8%'; // Can be calculated from logs if needed
            }

            // User counts by role (for Users Total)
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
