<?php

namespace App\Controllers\Nurse;

use App\Controllers\BaseController;
use App\Models\AdmissionModel;
use App\Models\ConsultationModel;
use App\Models\AdminPatientModel;

class AdmissionController extends BaseController
{
    protected $admissionModel;
    protected $consultationModel;
    protected $patientModel;

    public function __construct()
    {
        $this->admissionModel = new AdmissionModel();
        $this->consultationModel = new ConsultationModel();
        $this->patientModel = new AdminPatientModel();
    }

    /**
     * List pending admissions (for Nurse/Receptionist)
     */
    public function pending()
    {
        // Check if user is logged in and is a nurse or receptionist
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth')->with('error', 'You must be logged in to access this page.');
        }

        $role = session()->get('role');
        if (!in_array($role, ['nurse', 'receptionist'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $db = \Config\Database::connect();

        // Get consultations marked for admission but not yet admitted
        $pendingAdmissions = $db->table('consultations c')
            ->select('c.*, ap.firstname, ap.lastname, ap.contact, 
                     u.username as doctor_name')
            ->join('admin_patients ap', 'ap.id = c.patient_id', 'left')
            ->join('users u', 'u.id = c.doctor_id', 'left')
            ->where('c.for_admission', 1)
            ->where('c.type', 'completed')
            ->where('c.status', 'approved')
            ->where('c.deleted_at', null)
            ->get()
            ->getResultArray();

        // Filter out already admitted
        $filtered = [];
        foreach ($pendingAdmissions as $consultation) {
            $existingAdmission = $db->table('admissions')
                ->where('consultation_id', $consultation['id'])
                ->where('status !=', 'discharged')
                ->where('status !=', 'cancelled')
                ->where('deleted_at', null)
                ->get()
                ->getRowArray();
            
            if (!$existingAdmission) {
                $filtered[] = $consultation;
            }
        }

        $data = [
            'title' => 'Pending Admissions',
            'pendingAdmissions' => $filtered,
        ];

        return view('nurse/admission/pending', $data);
    }
}

