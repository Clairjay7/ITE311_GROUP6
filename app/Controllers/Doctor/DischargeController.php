<?php

namespace App\Controllers\Doctor;

use App\Controllers\BaseController;
use App\Models\AdmissionModel;
use App\Models\DischargeOrderModel;
use App\Models\AdminPatientModel;

class DischargeController extends BaseController
{
    protected $admissionModel;
    protected $dischargeOrderModel;
    protected $patientModel;

    public function __construct()
    {
        $this->admissionModel = new AdmissionModel();
        $this->dischargeOrderModel = new DischargeOrderModel();
        $this->patientModel = new AdminPatientModel();
    }

    /**
     * List active admissions for discharge
     */
    public function index()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor.');
        }

        $doctorId = session()->get('user_id');
        $db = \Config\Database::connect();

        // Get admissions where this doctor is the attending physician
        $admissions = $db->table('admissions a')
            ->select('a.*, ap.firstname, ap.lastname, ap.gender, ap.birthdate,
                     r.room_number, r.ward, r.room_type,
                     c.consultation_date, c.diagnosis')
            ->join('admin_patients ap', 'ap.id = a.patient_id', 'left')
            ->join('rooms r', 'r.id = a.room_id', 'left')
            ->join('consultations c', 'c.id = a.consultation_id', 'left')
            ->where('a.attending_physician_id', $doctorId)
            ->where('a.status', 'admitted')
            ->where('a.discharge_status', 'admitted')
            ->where('a.deleted_at', null)
            ->orderBy('a.admission_date', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Discharge Patients',
            'admissions' => $admissions,
        ];

        return view('doctor/discharge/index', $data);
    }

    /**
     * Show discharge order form
     */
    public function create($admissionId)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor.');
        }

        $doctorId = session()->get('user_id');
        $db = \Config\Database::connect();

        $admission = $db->table('admissions a')
            ->select('a.*, ap.firstname, ap.lastname, ap.gender, ap.birthdate,
                     r.room_number, r.ward, r.room_type,
                     c.consultation_date, c.diagnosis, c.notes as consultation_notes')
            ->join('admin_patients ap', 'ap.id = a.patient_id', 'left')
            ->join('rooms r', 'r.id = a.room_id', 'left')
            ->join('consultations c', 'c.id = a.consultation_id', 'left')
            ->where('a.id', $admissionId)
            ->where('a.attending_physician_id', $doctorId)
            ->where('a.status', 'admitted')
            ->where('a.deleted_at', null)
            ->get()
            ->getRowArray();

        if (!$admission) {
            return redirect()->to('/doctor/discharge')->with('error', 'Admission not found or you do not have permission.');
        }

        // Check if discharge order already exists
        $existingOrder = $this->dischargeOrderModel
            ->where('admission_id', $admissionId)
            ->where('status !=', 'cancelled')
            ->first();

        $data = [
            'title' => 'Create Discharge Order',
            'admission' => $admission,
            'existingOrder' => $existingOrder,
        ];

        return view('doctor/discharge/create', $data);
    }

    /**
     * Store discharge order
     */
    public function store()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor.');
        }

        $doctorId = session()->get('user_id');

        $validation = $this->validate([
            'admission_id' => 'required|integer|greater_than[0]',
            'patient_id' => 'required|integer|greater_than[0]',
            'final_diagnosis' => 'required|max_length[2000]',
            'treatment_summary' => 'permit_empty|max_length[2000]',
            'recommendations' => 'permit_empty|max_length[2000]',
            'follow_up_instructions' => 'permit_empty|max_length[2000]',
            'medications_prescribed' => 'permit_empty|max_length[2000]',
            'discharge_date' => 'required|valid_date',
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $admissionId = $this->request->getPost('admission_id');
        
        // Verify admission belongs to this doctor
        $admission = $this->admissionModel
            ->where('id', $admissionId)
            ->where('attending_physician_id', $doctorId)
            ->where('status', 'admitted')
            ->first();

        if (!$admission) {
            return redirect()->back()->with('error', 'Admission not found or you do not have permission.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Create discharge order
            $dischargeData = [
                'admission_id' => $admissionId,
                'patient_id' => $this->request->getPost('patient_id'),
                'doctor_id' => $doctorId,
                'final_diagnosis' => $this->request->getPost('final_diagnosis'),
                'treatment_summary' => $this->request->getPost('treatment_summary'),
                'recommendations' => $this->request->getPost('recommendations'),
                'follow_up_instructions' => $this->request->getPost('follow_up_instructions'),
                'medications_prescribed' => $this->request->getPost('medications_prescribed'),
                'discharge_date' => $this->request->getPost('discharge_date') . ' ' . date('H:i:s'),
                'status' => 'pending',
            ];

            if (!$this->dischargeOrderModel->insert($dischargeData)) {
                throw new \Exception('Failed to create discharge order');
            }

            // Update admission discharge_status to discharge_pending
            $this->admissionModel->update($admissionId, [
                'discharge_status' => 'discharge_pending',
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return redirect()->to('/doctor/discharge')->with('success', 'Discharge order created successfully. Patient is now pending discharge. Billing will finalize charges before discharge.');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Failed to create discharge order: ' . $e->getMessage());
        }
    }

    /**
     * View discharge order
     */
    public function view($id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor.');
        }

        $doctorId = session()->get('user_id');
        $db = \Config\Database::connect();

        $order = $db->table('discharge_orders do')
            ->select('do.*, ap.firstname, ap.lastname, ap.gender, ap.birthdate,
                     a.room_id, r.room_number, r.ward,
                     u.username as doctor_name')
            ->join('admin_patients ap', 'ap.id = do.patient_id', 'left')
            ->join('admissions a', 'a.id = do.admission_id', 'left')
            ->join('rooms r', 'r.id = a.room_id', 'left')
            ->join('users u', 'u.id = do.doctor_id', 'left')
            ->where('do.id', $id)
            ->where('do.doctor_id', $doctorId)
            ->get()
            ->getRowArray();

        if (!$order) {
            return redirect()->to('/doctor/discharge')->with('error', 'Discharge order not found.');
        }

        $data = [
            'title' => 'Discharge Order',
            'order' => $order,
        ];

        return view('doctor/discharge/view', $data);
    }
}

