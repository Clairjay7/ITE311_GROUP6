<?php

namespace App\Controllers\Doctor;

use App\Controllers\BaseController;
use App\Models\AdminPatientModel;
use App\Models\ConsultationModel;

class DashboardStats extends BaseController
{
    public function stats()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        $doctorId = session()->get('user_id');
        $patientModel = new AdminPatientModel();
        $consultationModel = new ConsultationModel();

        try {
            // Get dashboard statistics
            $today = date('Y-m-d');
            
            // Today's appointments
            $appointmentsCount = $consultationModel
                ->where('doctor_id', $doctorId)
                ->where('consultation_date', $today)
                ->where('status', 'approved')
                ->countAllResults();

            // Patients seen today
            $patientsSeenToday = $consultationModel
                ->where('doctor_id', $doctorId)
                ->where('consultation_date', $today)
                ->where('type', 'completed')
                ->countAllResults();

            // Assigned patients count (from admin_patients table)
            $assignedPatientsCount = $patientModel
                ->where('doctor_id', $doctorId)
                ->countAllResults();

            // Get recent assigned patients (latest 5)
            $assignedPatients = $patientModel
                ->where('doctor_id', $doctorId)
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->findAll();

            // Pending consultations
            $pendingConsultations = $consultationModel
                ->where('doctor_id', $doctorId)
                ->where('status', 'pending')
                ->countAllResults();

            // Upcoming consultations (next 7 days)
            $upcomingConsultations = $consultationModel
                ->where('doctor_id', $doctorId)
                ->where('consultation_date >=', $today)
                ->where('consultation_date <=', date('Y-m-d', strtotime('+7 days')))
                ->where('status', 'approved')
                ->where('type', 'upcoming')
                ->countAllResults();

            $data = [
                'appointments_count' => $appointmentsCount,
                'patients_seen_today' => $patientsSeenToday,
                'assigned_patients_count' => $assignedPatientsCount,
                'pending_consultations' => $pendingConsultations,
                'upcoming_consultations' => $upcomingConsultations,
                'assigned_patients' => $assignedPatients,
                'last_updated' => date('Y-m-d H:i:s')
            ];

            return $this->response->setJSON($data);
        } catch (\Throwable $e) {
            log_message('error', 'Error fetching Doctor Dashboard Stats: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Failed to fetch stats'])->setStatusCode(500);
        }
    }
}

