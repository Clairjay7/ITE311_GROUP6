<?php

namespace App\Controllers\Doctor;

use App\Controllers\BaseController;
use App\Models\AdminPatientModel;
use App\Models\ConsultationModel;

class DashboardController extends BaseController
{
    public function index()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $doctorId = session()->get('user_id');
        $patientModel = new AdminPatientModel();
        $consultationModel = new ConsultationModel();

        // Get dashboard statistics
        $today = date('Y-m-d');
        $appointmentsCount = $consultationModel
            ->where('doctor_id', $doctorId)
            ->where('consultation_date', $today)
            ->where('status', 'approved')
            ->countAllResults();

        $patientsSeenToday = $consultationModel
            ->where('doctor_id', $doctorId)
            ->where('consultation_date', $today)
            ->where('type', 'completed')
            ->countAllResults();

        $pendingLabResults = 0; // This would require additional models/tables

        $prescriptionsCount = 0; // This would require additional models/tables

        // Get assigned patients from admin_patients table
        $assignedPatientsCount = $patientModel
            ->where('doctor_id', $doctorId)
            ->countAllResults();

        // Get recent assigned patients
        $assignedPatients = $patientModel
            ->where('doctor_id', $doctorId)
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->findAll();

        $data = [
            'title' => 'Doctor Dashboard',
            'name' => session()->get('name'),
            'appointmentsCount' => $appointmentsCount,
            'patientsSeenToday' => $patientsSeenToday,
            'pendingLabResults' => $pendingLabResults,
            'prescriptionsCount' => $prescriptionsCount,
            'assignedPatientsCount' => $assignedPatientsCount,
            'assignedPatients' => $assignedPatients
        ];

        return view('doctor/dashboard/index', $data);
    }
}
