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

        $data = [
            'title' => 'Patient List',
            'patients' => $patients
        ];

        return view('doctor/patients/index', $data);
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
            $hmsPatient = $db->table('patients')
                ->where('patient_id', $id)
                ->get()
                ->getRowArray();
            
            if ($hmsPatient) {
                // Verify this patient is assigned to this doctor
                if (($hmsPatient['doctor_id'] ?? null) != $doctorId) {
                    return redirect()->to('/doctor/patients')->with('error', 'You do not have access to this patient.');
                }
                
                // Format patient data to match admin_patients structure
                $nameParts = [];
                if (!empty($hmsPatient['first_name'])) $nameParts[] = $hmsPatient['first_name'];
                if (!empty($hmsPatient['last_name'])) $nameParts[] = $hmsPatient['last_name'];
                
                // If no first_name/last_name, parse full_name
                if (empty($nameParts) && !empty($hmsPatient['full_name'])) {
                    $parts = explode(' ', $hmsPatient['full_name'], 2);
                    $nameParts = [
                        $parts[0] ?? '',
                        $parts[1] ?? ''
                    ];
                }
                
                $patient = [
                    'id' => $hmsPatient['patient_id'] ?? $hmsPatient['id'] ?? null,
                    'patient_id' => $hmsPatient['patient_id'] ?? $hmsPatient['id'] ?? null,
                    'firstname' => $nameParts[0] ?? '',
                    'lastname' => $nameParts[1] ?? '',
                    'full_name' => $hmsPatient['full_name'] ?? implode(' ', $nameParts),
                    'birthdate' => $hmsPatient['date_of_birth'] ?? $hmsPatient['birthdate'] ?? null,
                    'gender' => strtolower($hmsPatient['gender'] ?? ''),
                    'contact' => $hmsPatient['contact'] ?? null,
                    'address' => $hmsPatient['address'] ?? null,
                    'type' => $hmsPatient['type'] ?? 'Out-Patient',
                    'visit_type' => $hmsPatient['visit_type'] ?? null,
                    'doctor_id' => $hmsPatient['doctor_id'] ?? null,
                    'source' => 'receptionist',
                ];
                $patientSource = 'patients';
            }
        }
        
        if (!$patient) {
            return redirect()->to('/doctor/patients')->with('error', 'Patient not found.');
        }

        // Verify this patient is assigned to this doctor (double check)
        if (($patient['doctor_id'] ?? null) != $doctorId) {
            return redirect()->to('/doctor/patients')->with('error', 'You do not have access to this patient.');
        }

        // Get patient's consultation history
        // For patients table, use patient_id; for admin_patients, use id
        $patientIdForConsultation = ($patientSource === 'patients') ? ($patient['patient_id'] ?? $patient['id']) : $patient['id'];
        
        $consultations = $consultationModel
            ->where('patient_id', $patientIdForConsultation)
            ->where('doctor_id', $doctorId)
            ->orderBy('consultation_date', 'DESC')
            ->orderBy('consultation_time', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Patient Details',
            'patient' => $patient,
            'patient_source' => $patientSource,
            'consultations' => $consultations
        ];

        return view('doctor/patients/view', $data);
    }

    public function create()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $data = [
            'title' => 'Create New Patient',
            'validation' => \Config\Services::validation()
        ];

        return view('doctor/patients/create', $data);
    }

    public function store()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $patientModel = new AdminPatientModel();

        $validation = $this->validate([
            'firstname' => 'required|min_length[2]|max_length[100]',
            'lastname' => 'required|min_length[2]|max_length[100]',
            'birthdate' => 'required|valid_date',
            'gender' => 'required|in_list[male,female,other]',
            'contact' => 'permit_empty|max_length[20]',
            'address' => 'permit_empty|max_length[255]'
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
            'doctor_id' => session()->get('user_id')
        ];

        if ($patientModel->insert($data)) {
            return redirect()->to('/doctor/patients')->with('success', 'Patient created successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create patient.');
        }
    }

    public function edit($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $patientModel = new AdminPatientModel();
        $doctorId = session()->get('user_id');
        $db = \Config\Database::connect();

        // Try to find patient in admin_patients table first
        $patient = $patientModel->find($id);
        $patientSource = 'admin_patients';
        
        // If not found in admin_patients, try patients table (receptionist patients)
        if (!$patient && $db->tableExists('patients')) {
            $hmsPatient = $db->table('patients')
                ->where('patient_id', $id)
                ->get()
                ->getRowArray();
            
            if ($hmsPatient) {
                // Verify this patient is assigned to this doctor
                if (($hmsPatient['doctor_id'] ?? null) != $doctorId) {
                    return redirect()->to('/doctor/patients')->with('error', 'You do not have access to this patient.');
                }
                
                // Format patient data to match admin_patients structure
                $nameParts = [];
                if (!empty($hmsPatient['first_name'])) $nameParts[] = $hmsPatient['first_name'];
                if (!empty($hmsPatient['last_name'])) $nameParts[] = $hmsPatient['last_name'];
                
                // If no first_name/last_name, parse full_name
                if (empty($nameParts) && !empty($hmsPatient['full_name'])) {
                    $parts = explode(' ', $hmsPatient['full_name'], 2);
                    $nameParts = [
                        $parts[0] ?? '',
                        $parts[1] ?? ''
                    ];
                }
                
                $patient = [
                    'id' => $hmsPatient['patient_id'] ?? $hmsPatient['id'] ?? null,
                    'patient_id' => $hmsPatient['patient_id'] ?? $hmsPatient['id'] ?? null,
                    'firstname' => $nameParts[0] ?? '',
                    'lastname' => $nameParts[1] ?? '',
                    'full_name' => $hmsPatient['full_name'] ?? implode(' ', $nameParts),
                    'birthdate' => $hmsPatient['date_of_birth'] ?? $hmsPatient['birthdate'] ?? null,
                    'gender' => strtolower($hmsPatient['gender'] ?? ''),
                    'contact' => $hmsPatient['contact'] ?? null,
                    'address' => $hmsPatient['address'] ?? null,
                    'type' => $hmsPatient['type'] ?? 'Out-Patient',
                    'visit_type' => $hmsPatient['visit_type'] ?? null,
                    'doctor_id' => $hmsPatient['doctor_id'] ?? null,
                    'source' => 'receptionist',
                ];
                $patientSource = 'patients';
            }
        }
        
        if (!$patient) {
            return redirect()->to('/doctor/patients')->with('error', 'Patient not found.');
        }

        // Verify this patient is assigned to this doctor (double check)
        if (($patient['doctor_id'] ?? null) != $doctorId) {
            return redirect()->to('/doctor/patients')->with('error', 'You do not have access to this patient.');
        }

        $data = [
            'title' => 'Edit Patient',
            'patient' => $patient,
            'patient_source' => $patientSource,
            'validation' => \Config\Services::validation()
        ];

        return view('doctor/patients/edit', $data);
    }

    public function update($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $patientModel = new AdminPatientModel();
        $doctorId = session()->get('user_id');
        $db = \Config\Database::connect();

        // Try to find patient in admin_patients table first
        $patient = $patientModel->find($id);
        $patientSource = 'admin_patients';
        
        // If not found in admin_patients, try patients table (receptionist patients)
        if (!$patient && $db->tableExists('patients')) {
            $hmsPatient = $db->table('patients')
                ->where('patient_id', $id)
                ->get()
                ->getRowArray();
            
            if ($hmsPatient) {
                $patient = $hmsPatient;
                $patientSource = 'patients';
            }
        }
        
        if (!$patient) {
            return redirect()->to('/doctor/patients')->with('error', 'Patient not found.');
        }

        // Verify this patient is assigned to this doctor
        if (($patient['doctor_id'] ?? null) != $doctorId) {
            return redirect()->to('/doctor/patients')->with('error', 'You do not have access to this patient.');
        }

        $validation = $this->validate([
            'firstname' => 'required|min_length[2]|max_length[100]',
            'lastname' => 'required|min_length[2]|max_length[100]',
            'birthdate' => 'required|valid_date',
            'gender' => 'required|in_list[male,female,other,Male,Female,Other]',
            'contact' => 'permit_empty|max_length[20]',
            'address' => 'permit_empty|max_length[500]'
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        if ($patientSource === 'patients') {
            // Update in patients table
            $firstname = $this->request->getPost('firstname');
            $lastname = $this->request->getPost('lastname');
            $fullName = trim($firstname . ' ' . $lastname);
            
            $updateData = [
                'first_name' => $firstname,
                'last_name' => $lastname,
                'full_name' => $fullName,
                'date_of_birth' => $this->request->getPost('birthdate'),
                'gender' => $this->request->getPost('gender'),
                'contact' => $this->request->getPost('contact'),
                'address' => $this->request->getPost('address'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $patientId = $patient['patient_id'] ?? $patient['id'];
            if ($db->table('patients')->where('patient_id', $patientId)->update($updateData)) {
                return redirect()->to('/doctor/patients')->with('success', 'Patient updated successfully.');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to update patient.');
            }
        } else {
            // Update in admin_patients table
            $data = [
                'firstname' => $this->request->getPost('firstname'),
                'lastname' => $this->request->getPost('lastname'),
                'birthdate' => $this->request->getPost('birthdate'),
                'gender' => $this->request->getPost('gender'),
                'contact' => $this->request->getPost('contact'),
                'address' => $this->request->getPost('address'),
                'doctor_id' => session()->get('user_id')
            ];

            if ($patientModel->update($id, $data)) {
                return redirect()->to('/doctor/patients')->with('success', 'Patient updated successfully.');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to update patient.');
            }
        }
    }

    public function delete($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $patientModel = new AdminPatientModel();
        $doctorId = session()->get('user_id');
        $db = \Config\Database::connect();

        // Try to find patient in admin_patients table first
        $patient = $patientModel->find($id);
        $patientSource = 'admin_patients';
        
        // If not found in admin_patients, try patients table (receptionist patients)
        if (!$patient && $db->tableExists('patients')) {
            $hmsPatient = $db->table('patients')
                ->where('patient_id', $id)
                ->get()
                ->getRowArray();
            
            if ($hmsPatient) {
                $patient = $hmsPatient;
                $patientSource = 'patients';
            }
        }
        
        if (!$patient) {
            return redirect()->to('/doctor/patients')->with('error', 'Patient not found.');
        }

        // Verify this patient is assigned to this doctor
        if (($patient['doctor_id'] ?? null) != $doctorId) {
            return redirect()->to('/doctor/patients')->with('error', 'You do not have access to this patient.');
        }

        if ($patientSource === 'patients') {
            // Delete from patients table
            $patientId = $patient['patient_id'] ?? $patient['id'];
            if ($db->table('patients')->where('patient_id', $patientId)->delete()) {
                return redirect()->to('/doctor/patients')->with('success', 'Patient deleted successfully.');
            } else {
                return redirect()->back()->with('error', 'Failed to delete patient.');
            }
        } else {
            // Delete from admin_patients table
            if ($patientModel->delete($id)) {
                return redirect()->to('/doctor/patients')->with('success', 'Patient deleted successfully.');
            } else {
                return redirect()->back()->with('error', 'Failed to delete patient.');
            }
        }
    }
}
