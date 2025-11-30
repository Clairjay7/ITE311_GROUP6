<?php

namespace App\Controllers\Receptionist;

use App\Controllers\BaseController;
use App\Models\HMSPatientModel;
use App\Models\AdminPatientModel;
use App\Models\AppointmentModel;
use App\Models\BillingModel;

class DashboardStats extends BaseController
{
    public function stats()
    {
        $today = date('Y-m-d');
        $newRegistrations = 0;
        $totalInPatients = 0;
        $totalOutPatients = 0;
        $appointmentsToday = 0;
        $waitingPatients = 0;
        $pendingPaymentsAmount = 0.0;
        $pendingInvoices = 0;

        try {
            $db = \Config\Database::connect();
            $patientModel = new HMSPatientModel();
            $adminPatientModel = new AdminPatientModel();
            $appointmentModel = new AppointmentModel();
            $billingModel = new BillingModel();

            // Count patients created today from BOTH tables
            // From patients table (HMSPatientModel)
            $patientsToday = 0;
            if ($db->tableExists('patients')) {
                $builder = $patientModel->builder();
                $builder->select('COUNT(*) as cnt');
                $builder->groupStart()
                        ->where('DATE(patients.created_at)', $today)
                        ->orWhere('DATE(patients.registration_date)', $today)
                        ->groupEnd();
                $row = $builder->get()->getRowArray();
                if ($row && isset($row['cnt'])) {
                    $patientsToday += (int)$row['cnt'];
                }
            }

            // From admin_patients table (AdminPatientModel)
            $adminPatientsToday = 0;
            if ($db->tableExists('admin_patients')) {
                $adminBuilder = $adminPatientModel->builder();
                $adminBuilder->select('COUNT(*) as cnt')
                            ->where('DATE(admin_patients.created_at)', $today);
                $adminRow = $adminBuilder->get()->getRowArray();
                if ($adminRow && isset($adminRow['cnt'])) {
                    $adminPatientsToday += (int)$adminRow['cnt'];
                }
            }

            $newRegistrations = $patientsToday + $adminPatientsToday;

            // Overall totals by type from patients table
            if ($db->tableExists('patients')) {
                $builderIn = $patientModel->builder();
                $builderIn->select('COUNT(*) as cnt')
                          ->where('type', 'In-Patient');
                $rowIn = $builderIn->get()->getRowArray();
                if ($rowIn && isset($rowIn['cnt'])) {
                    $totalInPatients += (int)$rowIn['cnt'];
                }

                $builderOut = $patientModel->builder();
                $builderOut->select('COUNT(*) as cnt')
                           ->where('type', 'Out-Patient');
                $rowOut = $builderOut->get()->getRowArray();
                if ($rowOut && isset($rowOut['cnt'])) {
                    $totalOutPatients += (int)$rowOut['cnt'];
                }
            }

            // Count admin_patients (they don't have type, so count all as additional)
            // Note: admin_patients are separate, so we add them to totals
            if ($db->tableExists('admin_patients')) {
                $adminTotal = $adminPatientModel->countAllResults();
                // Since admin_patients don't have type field, we can add to outpatients as default
                // Or you can modify the admin_patients table to include type field later
                $totalOutPatients += $adminTotal;
            }

            // Today's appointments
            if ($db->tableExists('appointments')) {
                $appointmentsToday = $appointmentModel
                    ->where('appointment_date', $today)
                    ->whereNotIn('status', ['cancelled', 'no_show', 'completed'])
                    ->countAllResults();
            }

            // Waiting patients (appointments with status 'scheduled' or 'confirmed' for today)
            if ($db->tableExists('appointments')) {
                $waitingPatients = $appointmentModel
                    ->where('appointment_date', $today)
                    ->whereIn('status', ['scheduled', 'confirmed'])
                    ->countAllResults();
            }

            // Pending payments amount
            if ($db->tableExists('billing')) {
                $pendingAmount = $billingModel->builder()
                    ->selectSum('amount', 'sum')
                    ->where('status', 'pending')
                    ->get()->getRow('sum');
                $pendingPaymentsAmount = (float) ($pendingAmount ?? 0);
            }

            // Pending invoices count
            if ($db->tableExists('billing')) {
                $pendingInvoices = $billingModel
                    ->where('status', 'pending')
                    ->countAllResults();
            }

        } catch (\Throwable $e) {
            log_message('error', 'DashboardStats error: ' . $e->getMessage());
            $newRegistrations = 0;
            $totalInPatients = 0;
            $totalOutPatients = 0;
            $appointmentsToday = 0;
            $waitingPatients = 0;
            $pendingPaymentsAmount = 0.0;
            $pendingInvoices = 0;
        }

        $data = [
            'appointments_today' => $appointmentsToday,
            'waiting_patients' => $waitingPatients,
            'new_registrations' => $newRegistrations,
            'total_inpatients' => $totalInPatients,
            'total_outpatients' => $totalOutPatients,
            'pending_payments_amount' => $pendingPaymentsAmount,
            'pending_invoices' => $pendingInvoices,
        ];

        return $this->response->setJSON($data);
    }
}
