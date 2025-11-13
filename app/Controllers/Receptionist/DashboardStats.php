<?php

namespace App\Controllers\Receptionist;

use App\Controllers\BaseController;
use App\Models\HMSPatientModel;

class DashboardStats extends BaseController
{
    public function stats()
    {
        $today = date('Y-m-d');
        $newRegistrations = 0;

        try {
            $patientModel = new HMSPatientModel();
            // Count patients created today (fallback to registration_date if created_at missing)
            $builder = $patientModel->builder();
            $builder->select('COUNT(*) as cnt');
            // Try created_at first
            $builder->groupStart()
                    ->where('DATE(patients.created_at)', $today)
                    ->orWhere('DATE(patients.registration_date)', $today)
                    ->groupEnd();
            $row = $builder->get()->getRowArray();
            if ($row && isset($row['cnt'])) {
                $newRegistrations = (int)$row['cnt'];
            }
        } catch (\Throwable $e) {
            $newRegistrations = 0;
        }

        // Placeholders for now; can be wired to real data sources later
        $data = [
            'appointments_today' => null,
            'waiting_patients' => null,
            'new_registrations' => $newRegistrations,
            'pending_payments_amount' => null,
            'pending_invoices' => null,
        ];

        return $this->response->setJSON($data);
    }
}
