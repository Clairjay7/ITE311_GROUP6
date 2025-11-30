<?php

namespace App\Controllers\Accountant;

use App\Controllers\BaseController;

class DashboardStats extends BaseController
{
    public function stats()
    {
        // Check if user is logged in and is finance/accountant
        if (!session()->get('logged_in') || session()->get('role') !== 'finance') {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        $db = \Config\Database::connect();
        $today = date('Y-m-d');
        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-t');

        try {
            // Today's revenue (paid bills)
            $todayRevenue = 0.0;
            if ($db->tableExists('billing')) {
                $revenue = $db->table('billing')
                    ->selectSum('amount', 'sum')
                    ->where('status', 'paid')
                    ->where('DATE(created_at)', $today)
                    ->get()->getRow();
                $todayRevenue = (float) ($revenue->sum ?? 0);
            }

            // Pending bills
            $pendingBills = 0;
            $pendingBillsList = [];
            if ($db->tableExists('billing')) {
                $pendingBills = $db->table('billing')
                    ->where('status', 'pending')
                    ->countAllResults();
                
                $pendingBillsList = $db->table('billing')
                    ->select('billing.*, admin_patients.firstname, admin_patients.lastname')
                    ->join('admin_patients', 'admin_patients.id = billing.patient_id', 'left')
                    ->where('billing.status', 'pending')
                    ->orderBy('billing.created_at', 'DESC')
                    ->limit(10)
                    ->get()->getResultArray();
            }

            // Outstanding balance (sum of pending bills)
            $outstandingBalance = 0.0;
            if ($db->tableExists('billing')) {
                $outstanding = $db->table('billing')
                    ->selectSum('amount', 'sum')
                    ->where('status', 'pending')
                    ->get()->getRow();
                $outstandingBalance = (float) ($outstanding->sum ?? 0);
            }

            // Insurance claims
            $insuranceClaims = 0;
            if ($db->tableExists('insurance_claims')) {
                $insuranceClaims = $db->table('insurance_claims')
                    ->where('status', 'pending')
                    ->countAllResults();
            }

            // Paid this month
            $paidThisMonth = 0.0;
            if ($db->tableExists('billing')) {
                $paid = $db->table('billing')
                    ->selectSum('amount', 'sum')
                    ->where('status', 'paid')
                    ->where('created_at >=', $monthStart)
                    ->where('created_at <=', $monthEnd . ' 23:59:59')
                    ->get()->getRow();
                $paidThisMonth = (float) ($paid->sum ?? 0);
            }

            // Receptionist → Patient Payments
            $patientPaymentsToday = 0.0;
            $totalPatientPayments = 0;
            if ($db->tableExists('payment_reports')) {
                $patientPaymentsToday = $db->table('payment_reports')
                    ->selectSum('amount', 'sum')
                    ->where('status', 'completed')
                    ->where('DATE(report_date)', $today)
                    ->get()->getRow();
                $patientPaymentsToday = (float) ($patientPaymentsToday->sum ?? 0);
                
                $totalPatientPayments = $db->table('payment_reports')
                    ->where('status', 'completed')
                    ->countAllResults();
            }

            // Doctor/Nurse → Treatment and Lab Charges (from billing table)
            $consultationCharges = 0.0;
            $labCharges = 0.0;
            $treatmentCharges = 0.0;
            $consultationCount = 0;
            $labCount = 0;
            $treatmentCount = 0;
            
            // Count consultations (charges are in billing table with service = 'consultation')
            if ($db->tableExists('consultations')) {
                $consultationCount = $db->table('consultations')
                    ->where('type', 'completed')
                    ->where('DATE(consultation_date)', $today)
                    ->countAllResults();
            }
            
            // Get consultation charges from billing table
            if ($db->tableExists('billing')) {
                $consultationBilling = $db->table('billing')
                    ->selectSum('amount', 'sum')
                    ->like('service', 'consultation')
                    ->where('status', 'paid')
                    ->where('DATE(created_at)', $today)
                    ->get()->getRow();
                $consultationCharges = (float) ($consultationBilling->sum ?? 0);
            }
            
            // Count lab requests
            if ($db->tableExists('lab_requests')) {
                $labCount = $db->table('lab_requests')
                    ->where('status', 'completed')
                    ->where('DATE(created_at)', $today)
                    ->countAllResults();
            }
            
            // Get lab charges from billing table
            if ($db->tableExists('billing')) {
                $labBilling = $db->table('billing')
                    ->selectSum('amount', 'sum')
                    ->like('service', 'lab')
                    ->where('status', 'paid')
                    ->where('DATE(created_at)', $today)
                    ->get()->getRow();
                $labCharges = (float) ($labBilling->sum ?? 0);
            }
            
            // Count doctor orders
            if ($db->tableExists('doctor_orders')) {
                $treatmentCount = $db->table('doctor_orders')
                    ->where('status', 'completed')
                    ->where('DATE(created_at)', $today)
                    ->countAllResults();
            }
            
            // Get treatment charges from billing table
            if ($db->tableExists('billing')) {
                $treatmentBilling = $db->table('billing')
                    ->selectSum('amount', 'sum')
                    ->whereIn('service', ['treatment', 'medication', 'procedure'])
                    ->where('status', 'paid')
                    ->where('DATE(created_at)', $today)
                    ->get()->getRow();
                $treatmentCharges = (float) ($treatmentBilling->sum ?? 0);
            }

            // Pharmacy → Medication Expenses
            $pharmacyRevenue = 0.0;
            $pharmacyExpenses = 0.0;
            if ($db->tableExists('pharmacy')) {
                // Revenue from pharmacy (if there's a sales/prescriptions table)
                // For now, we'll calculate from inventory value
                $pharmacyExpenses = $db->table('pharmacy')
                    ->selectSum('price', 'sum')
                    ->get()->getRow();
                $pharmacyExpenses = (float) ($pharmacyExpenses->sum ?? 0);
            }

            // Lab Staff → Lab Test Charges (from billing table)
            $labTestRevenue = 0.0;
            $totalLabTests = 0;
            if ($db->tableExists('lab_requests')) {
                $totalLabTests = $db->table('lab_requests')
                    ->where('status', 'completed')
                    ->countAllResults();
            }
            
            // Get lab test revenue from billing table
            if ($db->tableExists('billing')) {
                $labTestBilling = $db->table('billing')
                    ->selectSum('amount', 'sum')
                    ->like('service', 'lab')
                    ->where('status', 'paid')
                    ->get()->getRow();
                $labTestRevenue = (float) ($labTestBilling->sum ?? 0);
            }

            // Finance Admin → Budgets and Reports
            $totalBudgets = 0;
            $activeReports = 0;
            if ($db->tableExists('finance_overview')) {
                $totalBudgets = $db->table('finance_overview')->countAllResults();
                $activeReports = $db->table('finance_overview')
                    ->where('period_end >=', date('Y-m-d'))
                    ->countAllResults();
            }

            // IT Staff → System and User Management
            $totalUsers = 0;
            $activeUsers = 0;
            $systemLogs = 0;
            if ($db->tableExists('users')) {
                $totalUsers = $db->table('users')->countAllResults();
                $activeUsers = $db->table('users')
                    ->where('status', 'active')
                    ->countAllResults();
            }
            
            if ($db->tableExists('system_logs')) {
                $systemLogs = $db->table('system_logs')
                    ->where('created_at >=', date('Y-m-d'))
                    ->countAllResults();
            }

            // Calculate total revenue from all sources
            $totalRevenue = $todayRevenue + $patientPaymentsToday + $consultationCharges + $labCharges + $treatmentCharges + $labTestRevenue;

            $data = [
                'today_revenue' => $todayRevenue,
                'pending_bills' => $pendingBills,
                'pending_bills_list' => $pendingBillsList,
                'outstanding_balance' => $outstandingBalance,
                'insurance_claims' => $insuranceClaims,
                'paid_this_month' => $paidThisMonth,
                // Cross-role data
                'patient_payments_today' => $patientPaymentsToday,
                'total_patient_payments' => $totalPatientPayments,
                'consultation_charges' => $consultationCharges,
                'consultation_count' => $consultationCount,
                'lab_charges' => $labCharges,
                'lab_count' => $labCount,
                'treatment_charges' => $treatmentCharges,
                'treatment_count' => $treatmentCount,
                'pharmacy_revenue' => $pharmacyRevenue,
                'pharmacy_expenses' => $pharmacyExpenses,
                'lab_test_revenue' => $labTestRevenue,
                'total_lab_tests' => $totalLabTests,
                'total_budgets' => $totalBudgets,
                'active_reports' => $activeReports,
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'system_logs' => $systemLogs,
                'total_revenue' => $totalRevenue,
                'last_updated' => date('Y-m-d H:i:s')
            ];

            return $this->response->setJSON($data);
        } catch (\Throwable $e) {
            log_message('error', 'Error fetching Accountant Dashboard Stats: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Failed to fetch stats'])->setStatusCode(500);
        }
    }
}

