<?php

namespace App\Controllers\Doctor;

use App\Controllers\BaseController;
use App\Models\DischargeOrderModel;
use App\Models\AdminPatientModel;

class DischargeController extends BaseController
{
    protected $dischargeOrderModel;
    protected $patientModel;

    public function __construct()
    {
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

        // First, try to find admission by ID
        $admission = $db->table('admissions a')
            ->select('a.*, ap.firstname, ap.lastname, ap.gender, ap.birthdate,
                     r.room_number, r.ward, r.room_type,
                     c.consultation_date, c.diagnosis, c.notes as consultation_notes')
            ->join('admin_patients ap', 'ap.id = a.patient_id', 'left')
            ->join('rooms r', 'r.id = a.room_id', 'left')
            ->join('consultations c', 'c.id = a.consultation_id', 'left')
            ->where('a.id', $admissionId)
            ->where('a.status', 'admitted')
            ->where('a.deleted_at', null)
            ->get()
            ->getRowArray();

        // If admission found, check if doctor has permission
        if ($admission) {
            // Check if doctor is the attending physician OR if patient is assigned to this doctor
            $hasPermission = false;
            
            // Check attending_physician_id
            if ($admission['attending_physician_id'] == $doctorId) {
                $hasPermission = true;
            } else {
                // Check if patient is assigned to this doctor
                $patientId = $admission['patient_id'];
                $patient = $db->table('admin_patients')
                    ->where('id', $patientId)
                    ->where('doctor_id', $doctorId)
                    ->get()
                    ->getRowArray();
                
                if ($patient) {
                    $hasPermission = true;
                }
            }
            
            if (!$hasPermission) {
                return redirect()->to('/doctor/discharge')->with('error', 'Admission not found or you do not have permission.');
            }
        } else {
            // If no admission record found, check if it's a direct admission (patient ID passed)
            // Check if patient exists and is assigned to this doctor
            $patient = $db->table('admin_patients')
                ->where('id', $admissionId)
                ->where('doctor_id', $doctorId)
                ->get()
                ->getRowArray();
            
            if (!$patient) {
                // Try patients table
                $hmsPatient = $db->table('patients')
                    ->where('patient_id', $admissionId)
                    ->where('doctor_id', $doctorId)
                    ->get()
                    ->getRowArray();
                
                if (!$hmsPatient) {
                    return redirect()->to('/doctor/discharge')->with('error', 'Admission not found or you do not have permission.');
                }
                
                // For HMS patients, create a virtual admission record for display
                $admission = [
                    'id' => null,
                    'patient_id' => $admissionId,
                    'patient_source' => 'patients',
                    'firstname' => $hmsPatient['first_name'] ?? '',
                    'lastname' => $hmsPatient['last_name'] ?? '',
                    'gender' => $hmsPatient['gender'] ?? '',
                    'birthdate' => $hmsPatient['date_of_birth'] ?? null,
                    'room_number' => $hmsPatient['room_number'] ?? 'N/A',
                    'ward' => 'N/A',
                    'room_type' => 'N/A',
                    'consultation_date' => null,
                    'diagnosis' => null,
                    'consultation_notes' => null,
                    'is_direct_admission' => true,
                ];
            } else {
                // For admin patients, create a virtual admission record for display
                $admission = [
                    'id' => null,
                    'patient_id' => $admissionId,
                    'patient_source' => 'admin',
                    'firstname' => $patient['firstname'] ?? '',
                    'lastname' => $patient['lastname'] ?? '',
                    'gender' => $patient['gender'] ?? '',
                    'birthdate' => $patient['birthdate'] ?? null,
                    'room_number' => 'N/A',
                    'ward' => 'N/A',
                    'room_type' => 'N/A',
                    'consultation_date' => null,
                    'diagnosis' => null,
                    'consultation_notes' => null,
                    'is_direct_admission' => true,
                ];
            }
        }

        // Check if discharge order already exists (only if admission has an ID)
        $existingOrder = null;
        if (!empty($admission['id'])) {
            $existingOrder = $this->dischargeOrderModel
                ->where('admission_id', $admission['id'])
                ->where('status !=', 'cancelled')
                ->first();
        } else {
            // For direct admissions, check by patient_id
            $existingOrder = $this->dischargeOrderModel
                ->where('patient_id', $admission['patient_id'])
                ->where('status !=', 'cancelled')
                ->first();
        }

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
        $patientId = $this->request->getPost('patient_id');
        
        // Verify admission belongs to this doctor
        $db = \Config\Database::connect();
        $admission = null;
        
        // If admission_id is provided and not empty/null, check admission record
        if (!empty($admissionId) && $admissionId !== 'null' && $admissionId !== '0') {
            $admission = $db->table('admissions')
                ->where('id', $admissionId)
                ->where('status', 'admitted')
                ->get()
                ->getRowArray();
            
            if ($admission) {
                // Check if doctor has permission
                $hasPermission = false;
                if ($admission['attending_physician_id'] == $doctorId) {
                    $hasPermission = true;
                } else {
                    // Check if patient is assigned to this doctor
                    $patient = $db->table('admin_patients')
                        ->where('id', $admission['patient_id'])
                        ->where('doctor_id', $doctorId)
                        ->get()
                        ->getRowArray();
                    
                    if ($patient) {
                        $hasPermission = true;
                    }
                }
                
                if (!$hasPermission) {
                    return redirect()->back()->with('error', 'Admission not found or you do not have permission.');
                }
            }
        }
        
        // If no admission record found, verify patient is assigned to this doctor (direct admission)
        if (!$admission && !empty($patientId)) {
            $patient = $db->table('admin_patients')
                ->where('id', $patientId)
                ->where('doctor_id', $doctorId)
                ->get()
                ->getRowArray();
            
            if (!$patient) {
                // Try patients table
                $hmsPatient = $db->table('patients')
                    ->where('patient_id', $patientId)
                    ->where('doctor_id', $doctorId)
                    ->get()
                    ->getRowArray();
                
                if (!$hmsPatient) {
                    return redirect()->back()->with('error', 'Patient not found or you do not have permission.');
                }
            }
            
            // For direct admission, set admission_id to null
            $admissionId = null;
        } elseif (!$admission) {
            return redirect()->back()->with('error', 'Admission not found or you do not have permission.');
        } else {
            // Use the admission ID from the found admission
            $admissionId = $admission['id'];
            $patientId = $admission['patient_id'];
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Create discharge order
            $dischargeData = [
                'admission_id' => $admissionId, // Can be null for direct admissions
                'patient_id' => $patientId,
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

            // Update admission discharge_status to discharge_pending (only if admission exists)
            if (!empty($admissionId)) {
                $db->table('admissions')
                    ->where('id', $admissionId)
                    ->update([
                        'discharge_status' => 'discharge_pending',
                    ]);
            }

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

