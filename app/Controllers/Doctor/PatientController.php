<?php

namespace App\Controllers\Doctor;

use App\Controllers\BaseController;
use App\Models\AdminPatientModel;
use App\Models\ConsultationModel;

class PatientController extends BaseController
{
    public function index()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $patientModel = new AdminPatientModel();
        $doctorId = session()->get('user_id');
        $db = \Config\Database::connect();

        // Get patients assigned to this doctor from admin_patients table
        $adminPatientsRaw = $patientModel
            ->where('doctor_id', $doctorId)
            ->orderBy('lastname', 'ASC')
            ->orderBy('firstname', 'ASC')
            ->findAll();
        
        // Format admin patients to include visit_type and source
        $adminPatients = [];
        foreach ($adminPatientsRaw as $patient) {
            $patient['visit_type'] = $patient['visit_type'] ?? null;
            $patient['source'] = 'admin'; // Mark as from admin panel
            $adminPatients[] = $patient;
        }

        // Get patients from patients table (HMSPatientModel) - includes Out-Patients registered by receptionist
        $hmsPatients = [];
        if ($db->tableExists('patients')) {
            $hmsPatientsRaw = $db->table('patients')
                ->select('patients.*')
                ->where('patients.doctor_id', $doctorId)
                ->where('patients.doctor_id IS NOT NULL')
                ->where('patients.doctor_id !=', 0)
                ->orderBy('patients.last_name', 'ASC')
                ->orderBy('patients.first_name', 'ASC')
                ->get()
                ->getResultArray();
            
            // Format hmsPatients to match admin_patients structure for the view
            foreach ($hmsPatientsRaw as $patient) {
                $nameParts = [];
                if (!empty($patient['first_name'])) $nameParts[] = $patient['first_name'];
                if (!empty($patient['last_name'])) $nameParts[] = $patient['last_name'];
                
                // If no first_name/last_name, try to parse full_name
                if (empty($nameParts) && !empty($patient['full_name'])) {
                    $parts = explode(' ', $patient['full_name'], 2);
                    $nameParts = [
                        $parts[0] ?? '',
                        $parts[1] ?? ''
                    ];
                }
                
                $hmsPatients[] = [
                    'id' => $patient['patient_id'] ?? $patient['id'] ?? null,
                    'patient_id' => $patient['patient_id'] ?? $patient['id'] ?? null,
                    'firstname' => $nameParts[0] ?? '',
                    'lastname' => $nameParts[1] ?? '',
                    'full_name' => $patient['full_name'] ?? implode(' ', $nameParts),
                    'birthdate' => $patient['date_of_birth'] ?? $patient['birthdate'] ?? null,
                    'gender' => strtolower($patient['gender'] ?? ''),
                    'contact' => $patient['contact'] ?? null,
                    'address' => $patient['address'] ?? null,
                    'type' => $patient['type'] ?? 'Out-Patient',
                    'visit_type' => $patient['visit_type'] ?? null,
                    'source' => 'receptionist', // Mark as from receptionist
                ];
            }
        }

        // Merge both patient lists
        $patients = array_merge($adminPatients, $hmsPatients);
        
        // Deduplicate: If same patient exists in both tables (same name + doctor_id), keep only admin_patients version
        $deduplicated = [];
        $seenKeys = [];
        
        foreach ($patients as $patient) {
            // Create a unique key based on name (case-insensitive) and doctor_id
            $nameKey = strtolower(trim(($patient['firstname'] ?? '') . ' ' . ($patient['lastname'] ?? '')));
            $key = md5($nameKey . '|' . $doctorId);
            
            // If we've seen this patient before, prefer admin_patients version (source = 'admin')
            if (isset($seenKeys[$key])) {
                // If current patient is from admin_patients and previous was from receptionist, replace it
                if (($patient['source'] ?? '') === 'admin' && ($seenKeys[$key]['source'] ?? '') === 'receptionist') {
                    $deduplicated[$seenKeys[$key]['index']] = $patient;
                    $seenKeys[$key] = ['index' => $seenKeys[$key]['index'], 'source' => $patient['source'] ?? 'admin'];
                }
                // Otherwise, skip this duplicate
                continue;
            }
            
            // Add to deduplicated list
            $index = count($deduplicated);
            $deduplicated[] = $patient;
            $seenKeys[$key] = ['index' => $index, 'source' => $patient['source'] ?? 'unknown'];
        }
        
        // Re-index array
        $patients = array_values($deduplicated);
        
        // Sort by lastname, then firstname
        usort($patients, function($a, $b) {
            $lastA = strtolower($a['lastname'] ?? '');
            $lastB = strtolower($b['lastname'] ?? '');
            if ($lastA === $lastB) {
                $firstA = strtolower($a['firstname'] ?? '');
                $firstB = strtolower($b['firstname'] ?? '');
                return $firstA <=> $firstB;
            }
            return $lastA <=> $lastB;
        });

        // Get patients marked for admission by nurse (pending doctor approval)
        $patientsForAdmission = [];
        if ($db->tableExists('admission_requests')) {
            $admissionRequests = $db->table('admission_requests')
                ->select('admission_requests.*, 
                         admin_patients.firstname, admin_patients.lastname, admin_patients.id as admin_patient_id,
                         patients.full_name, patients.patient_id as hms_patient_id,
                         triage.triage_level, triage.chief_complaint, triage.disposition, triage.doctor_id as triage_doctor_id, triage.assigned_doctor_id')
                ->join('admin_patients', 'admin_patients.id = admission_requests.patient_id', 'left')
                ->join('patients', 'patients.patient_id = admission_requests.patient_id', 'left')
                ->join('triage', 'triage.id = admission_requests.triage_id', 'left')
                ->where('admission_requests.status', 'pending_doctor_approval')
                ->groupStart()
                    // Get patients assigned to this doctor OR patients from triage assigned to this doctor
                    ->where('admin_patients.doctor_id', $doctorId)
                    ->orWhere('patients.doctor_id', $doctorId)
                    ->orWhere('triage.doctor_id', $doctorId)
                    ->orWhere('triage.assigned_doctor_id', $doctorId)
                ->groupEnd()
                ->orderBy('admission_requests.created_at', 'DESC')
                ->get()
                ->getResultArray();
            
            foreach ($admissionRequests as $request) {
                $patientId = $request['admin_patient_id'] ?? $request['hms_patient_id'] ?? $request['patient_id'] ?? null;
                $patientName = '';
                if ($request['firstname'] && $request['lastname']) {
                    $patientName = $request['firstname'] . ' ' . $request['lastname'];
                } elseif ($request['full_name']) {
                    $patientName = $request['full_name'];
                } else {
                    $patientName = 'Patient ' . $patientId;
                }
                
                $patientsForAdmission[] = [
                    'admission_request_id' => $request['id'] ?? null,
                    'patient_id' => $patientId,
                    'patient_name' => $patientName,
                    'triage_level' => $request['triage_level'] ?? 'N/A',
                    'disposition' => $request['disposition'] ?? 'Admission',
                    'chief_complaint' => $request['chief_complaint'] ?? 'N/A',
                    'admission_reason' => $request['admission_reason'] ?? '',
                    'requested_by' => $request['requested_by'] ?? null,
                    'requested_by_role' => $request['requested_by_role'] ?? 'nurse',
                    'created_at' => $request['created_at'] ?? date('Y-m-d H:i:s'),
                ];
            }
        }

        $data = [
            'title' => 'Patient List',
            'patients' => $patients,
            'patientsForAdmission' => $patientsForAdmission // Patients marked for admission by nurse
        ];

        return view('doctor/patients/index', $data);
    }

    /**
     * Approve admission request (Doctor approves nurse's recommendation)
     */
    public function approveAdmission()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied'])->setStatusCode(401);
        }

        $admissionRequestId = $this->request->getPost('admission_request_id');
        $doctorNotes = $this->request->getPost('doctor_notes') ?? '';

        if (!$admissionRequestId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Admission request ID is required']);
        }

        $db = \Config\Database::connect();
        $doctorId = session()->get('user_id');
        $db->transStart();

        try {
            // Get admission request
            $admissionRequest = $db->table('admission_requests')
                ->where('id', $admissionRequestId)
                ->where('status', 'pending_doctor_approval')
                ->get()
                ->getRowArray();

            if (!$admissionRequest) {
                throw new \Exception('Admission request not found or already processed');
            }

            // Update admission request status to "doctor_approved" (ready for receptionist to assign bed)
            $db->table('admission_requests')
                ->where('id', $admissionRequestId)
                ->update([
                    'status' => 'doctor_approved', // Doctor approved - ready for receptionist to assign ER bed
                    'approved_by' => $doctorId,
                    'approved_by_role' => 'doctor',
                    'approved_at' => date('Y-m-d H:i:s'),
                    'doctor_notes' => $doctorNotes,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            // Update consultation if exists
            if ($db->tableExists('consultations')) {
                $db->table('consultations')
                    ->where('patient_id', $admissionRequest['patient_id'])
                    ->where('consultation_date', date('Y-m-d'))
                    ->update([
                        'for_admission' => 1,
                        'status' => 'approved',
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }

            // Audit log
            if ($db->tableExists('audit_logs')) {
                $db->table('audit_logs')->insert([
                    'action' => 'admission_approved_by_doctor',
                    'user_id' => $doctorId,
                    'user_role' => 'doctor',
                    'user_name' => session()->get('username') ?? session()->get('name') ?? 'Doctor',
                    'description' => "Doctor approved admission request for patient ID: {$admissionRequest['patient_id']}. Ready for receptionist to assign ER bed.",
                    'related_id' => $admissionRequest['patient_id'],
                    'related_type' => 'patient',
                    'metadata' => json_encode([
                        'admission_request_id' => $admissionRequestId,
                        'patient_id' => $admissionRequest['patient_id'],
                        'doctor_notes' => $doctorNotes,
                    ]),
                    'ip_address' => $this->request->getIPAddress(),
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Admission approved. Receptionist will now assign ER bed.'
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Admission approval error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function view($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $patientModel = new AdminPatientModel();
        $consultationModel = new ConsultationModel();
        $doctorId = session()->get('user_id');
        $db = \Config\Database::connect();

        // Try to find patient in admin_patients table first
        $patient = $patientModel->find($id);
        $patientSource = 'admin_patients';
        
        // If not found in admin_patients, try patients table (receptionist patients)
        if (!$patient && $db->tableExists('patients')) {
            $patient = $db->table('patients')
                ->where('patient_id', $id)
                ->get()
                ->getRowArray();
            $patientSource = 'patients';
        }

        if (!$patient) {
            return redirect()->back()->with('error', 'Patient not found.');
        }

        // Get patient consultations
        $consultations = [];
        if ($db->tableExists('consultations')) {
            if ($patientSource === 'admin_patients') {
                $consultations = $consultationModel
                    ->where('patient_id', $id)
                    ->orderBy('consultation_date', 'DESC')
                    ->orderBy('consultation_time', 'DESC')
                    ->findAll();
            }
        }

        // Get admission request if exists
        $admissionRequest = null;
        if ($db->tableExists('admission_requests')) {
            $admissionRequest = $db->table('admission_requests')
                ->where('patient_id', $id)
                ->where('status', 'pending_doctor_approval')
                ->orderBy('created_at', 'DESC')
                ->get()
                ->getRowArray();
        }

        $data = [
            'title' => 'Patient Details',
            'patient' => $patient,
            'patientSource' => $patientSource,
            'consultations' => $consultations,
            'admissionRequest' => $admissionRequest,
        ];

        return view('doctor/patients/view', $data);
    }

    public function create()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        return view('doctor/patients/create', [
            'title' => 'Register New Patient'
        ]);
    }

    public function store()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $patientModel = new AdminPatientModel();
        $doctorId = session()->get('user_id');

        $validation = $this->validate([
            'firstname' => 'required|min_length[2]|max_length[100]',
            'lastname' => 'required|min_length[2]|max_length[100]',
            'birthdate' => 'required|valid_date',
            'gender' => 'required|in_list[male,female,other]',
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'firstname' => $this->request->getPost('firstname'),
            'lastname' => $this->request->getPost('lastname'),
            'birthdate' => $this->request->getPost('birthdate'),
            'gender' => $this->request->getPost('gender'),
            'contact' => $this->request->getPost('contact'),
            'address' => $this->request->getPost('address'),
            'doctor_id' => $doctorId,
        ];

        if ($patientModel->insert($data)) {
            return redirect()->to('/doctor/patients')->with('success', 'Patient registered successfully.');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to register patient.');
    }

    public function edit($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $patientModel = new AdminPatientModel();
        $patient = $patientModel->find($id);

        if (!$patient) {
            return redirect()->back()->with('error', 'Patient not found.');
        }

        return view('doctor/patients/edit', [
            'title' => 'Edit Patient',
            'patient' => $patient
        ]);
    }

    public function update($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $patientModel = new AdminPatientModel();
        $patient = $patientModel->find($id);

        if (!$patient) {
            return redirect()->back()->with('error', 'Patient not found.');
        }

        $validation = $this->validate([
            'firstname' => 'required|min_length[2]|max_length[100]',
            'lastname' => 'required|min_length[2]|max_length[100]',
            'birthdate' => 'required|valid_date',
            'gender' => 'required|in_list[male,female,other]',
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'firstname' => $this->request->getPost('firstname'),
            'lastname' => $this->request->getPost('lastname'),
            'birthdate' => $this->request->getPost('birthdate'),
            'gender' => $this->request->getPost('gender'),
            'contact' => $this->request->getPost('contact'),
            'address' => $this->request->getPost('address'),
        ];

        if ($patientModel->update($id, $data)) {
            return redirect()->to('/doctor/patients')->with('success', 'Patient updated successfully.');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update patient.');
    }

    public function delete($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $patientModel = new AdminPatientModel();
        $patient = $patientModel->find($id);

        if (!$patient) {
            return redirect()->back()->with('error', 'Patient not found.');
        }

        if ($patientModel->delete($id)) {
            return redirect()->to('/doctor/patients')->with('success', 'Patient deleted successfully.');
        }

        return redirect()->back()->with('error', 'Failed to delete patient.');
    }
}
