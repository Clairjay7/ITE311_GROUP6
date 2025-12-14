<?php

namespace App\Controllers\Receptionist;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class FollowUpController extends BaseController
{
    public function index()
    {
        // Check if user is logged in and has appropriate role
        if (!session()->get('logged_in') || session()->get('role') !== 'receptionist') {
            return redirect()->to('login')->with('error', 'Please login to access this page.');
        }

        $db = \Config\Database::connect();
        
        // Get filter parameter
        $patientFilter = $this->request->getGet('patient_id');
        
        // Get all follow-up appointments
        $followUpAppointments = [];
        if ($db->tableExists('appointments')) {
            $query = $db->table('appointments')
                ->select('appointments.*, 
                    COALESCE(patients.first_name, admin_patients.firstname) as patient_first_name,
                    COALESCE(patients.last_name, admin_patients.lastname) as patient_last_name,
                    COALESCE(patients.contact, admin_patients.contact) as patient_contact,
                    COALESCE(patients.patient_id, admin_patients.id) as patient_id_for_filter,
                    users.username as doctor_name,
                    doctors.specialization')
                ->join('patients', 'patients.patient_id = appointments.patient_id', 'left')
                ->join('admin_patients', 'admin_patients.id = appointments.patient_id', 'left')
                ->join('users', 'users.id = appointments.doctor_id', 'left')
                ->join('doctors', 'doctors.user_id = users.id', 'left')
                ->where('appointments.appointment_type', 'follow_up')
                ->whereNotIn('appointments.status', ['cancelled', 'completed', 'no_show'])
                ->orderBy('appointments.appointment_date', 'ASC')
                ->orderBy('appointments.appointment_time', 'ASC');
            
            // Apply patient filter if provided
            if ($patientFilter) {
                $query->where('appointments.patient_id', $patientFilter);
            }
            
            $followUpAppointments = $query->get()->getResultArray();
        }
        
        // Get unique patients with follow-up appointments for dropdown
        $patientsWithFollowUp = [];
        if ($db->tableExists('appointments')) {
            // Get unique patient IDs from follow-up appointments
            $patientIds = $db->table('appointments')
                ->select('appointments.patient_id')
                ->where('appointments.appointment_type', 'follow_up')
                ->whereNotIn('appointments.status', ['cancelled', 'completed', 'no_show'])
                ->distinct()
                ->get()
                ->getResultArray();
            
            foreach ($patientIds as $pid) {
                $patientId = $pid['patient_id'];
                
                // Try to get from patients table first
                $patient = $db->table('patients')
                    ->select('patient_id as id, CONCAT(first_name, " ", last_name) as full_name, contact')
                    ->where('patient_id', $patientId)
                    ->get()
                    ->getRowArray();
                
                // If not found, try admin_patients table
                if (!$patient) {
                    $patient = $db->table('admin_patients')
                        ->select('id, CONCAT(firstname, " ", lastname) as full_name, contact')
                        ->where('id', $patientId)
                        ->get()
                        ->getRowArray();
                }
                
                if ($patient) {
                    $patientsWithFollowUp[] = [
                        'id' => $patient['id'],
                        'name' => trim($patient['full_name'] ?? 'Unknown Patient'),
                        'contact' => $patient['contact'] ?? ''
                    ];
                }
            }
            
            // Sort by name
            usort($patientsWithFollowUp, function($a, $b) {
                return strcmp($a['name'], $b['name']);
            });
        }

        $data = [
            'title' => 'Follow Up Appointments',
            'active_menu' => 'follow-up',
            'appointments' => $followUpAppointments,
            'patients' => $patientsWithFollowUp,
            'selectedPatientId' => $patientFilter
        ];

        return view('Reception/follow-up/index', $data);
    }
}

