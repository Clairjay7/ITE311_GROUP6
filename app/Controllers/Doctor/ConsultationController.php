<?php

namespace App\Controllers\Doctor;

use App\Controllers\BaseController;
use App\Models\ConsultationModel;
use App\Models\AdminPatientModel;
use App\Models\PatientVitalModel;

class ConsultationController extends BaseController
{
    public function upcoming()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $consultationModel = new ConsultationModel();
        $doctorId = session()->get('user_id');

        $upcomingConsultations = $consultationModel->getUpcomingConsultations($doctorId);

        $data = [
            'title' => 'Upcoming Consultations',
            'consultations' => $upcomingConsultations
        ];

        return view('doctor/consultations/upcoming', $data);
    }

    public function mySchedule()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $consultationModel = new ConsultationModel();
        $doctorId = session()->get('user_id');

        $mySchedule = $consultationModel->getDoctorSchedule($doctorId);

        $data = [
            'title' => 'My Schedule',
            'consultations' => $mySchedule
        ];

        return view('doctor/consultations/my_schedule', $data);
    }

    public function create()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $patientModel = new AdminPatientModel();
        $doctorId = session()->get('user_id');

        // Get patients assigned to this doctor from admin_patients table
        $patients = $patientModel
            ->where('doctor_id', $doctorId)
            ->orderBy('lastname', 'ASC')
            ->orderBy('firstname', 'ASC')
            ->findAll();

        $data = [
            'title' => 'Create Consultation',
            'patients' => $patients,
            'validation' => \Config\Services::validation()
        ];

        return view('doctor/consultations/create', $data);
    }

    public function store()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $consultationModel = new ConsultationModel();
        $doctorId = session()->get('user_id');

        $validation = $this->validate([
            'patient_id' => 'required|integer|greater_than[0]',
            'consultation_date' => 'required|valid_date',
            'consultation_time' => 'required|regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/]',
            'type' => 'required|in_list[upcoming,completed]',
            'status' => 'required|in_list[pending,approved,cancelled]',
            'notes' => 'permit_empty|max_length[2000]'
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'doctor_id' => $doctorId,
            'patient_id' => $this->request->getPost('patient_id'),
            'consultation_date' => $this->request->getPost('consultation_date'),
            'consultation_time' => $this->request->getPost('consultation_time'),
            'type' => $this->request->getPost('type'),
            'status' => $this->request->getPost('status'),
            'notes' => $this->request->getPost('notes')
        ];

        if ($consultationModel->insert($data)) {
            return redirect()->to('/doctor/consultations/my-schedule')->with('success', 'Consultation created successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create consultation.');
        }
    }

    public function edit($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $consultationModel = new ConsultationModel();
        $patientModel = new AdminPatientModel();
        $doctorId = session()->get('user_id');

        $consultation = $consultationModel->find($id);
        if (!$consultation) {
            return redirect()->to('/doctor/consultations/my-schedule')->with('error', 'Consultation not found.');
        }

        // Verify this consultation belongs to this doctor
        if ($consultation['doctor_id'] != $doctorId) {
            return redirect()->to('/doctor/consultations/my-schedule')->with('error', 'You do not have access to this consultation.');
        }

        // Get patients assigned to this doctor from admin_patients table
        $patients = $patientModel
            ->where('doctor_id', $doctorId)
            ->orderBy('lastname', 'ASC')
            ->orderBy('firstname', 'ASC')
            ->findAll();

        $data = [
            'title' => 'Edit Consultation',
            'consultation' => $consultation,
            'patients' => $patients,
            'validation' => \Config\Services::validation()
        ];

        return view('doctor/consultations/edit', $data);
    }

    public function update($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $consultationModel = new ConsultationModel();
        $doctorId = session()->get('user_id');

        $consultation = $consultationModel->find($id);
        if (!$consultation) {
            return redirect()->to('/doctor/consultations/my-schedule')->with('error', 'Consultation not found.');
        }

        // Verify this consultation belongs to this doctor
        if ($consultation['doctor_id'] != $doctorId) {
            return redirect()->to('/doctor/consultations/my-schedule')->with('error', 'You do not have access to this consultation.');
        }

        $validation = $this->validate([
            'patient_id' => 'required|integer|greater_than[0]',
            'consultation_date' => 'required|valid_date',
            'consultation_time' => 'required|regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/]',
            'type' => 'required|in_list[upcoming,completed]',
            'status' => 'required|in_list[pending,approved,cancelled]',
            'notes' => 'permit_empty|max_length[2000]'
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'patient_id' => $this->request->getPost('patient_id'),
            'consultation_date' => $this->request->getPost('consultation_date'),
            'consultation_time' => $this->request->getPost('consultation_time'),
            'type' => $this->request->getPost('type'),
            'status' => $this->request->getPost('status'),
            'notes' => $this->request->getPost('notes')
        ];

        if ($consultationModel->update($id, $data)) {
            return redirect()->to('/doctor/consultations/my-schedule')->with('success', 'Consultation updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update consultation.');
        }
    }

    public function delete($id)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $consultationModel = new ConsultationModel();
        $doctorId = session()->get('user_id');

        $consultation = $consultationModel->find($id);
        if (!$consultation) {
            return redirect()->to('/doctor/consultations/my-schedule')->with('error', 'Consultation not found.');
        }

        // Verify this consultation belongs to this doctor
        if ($consultation['doctor_id'] != $doctorId) {
            return redirect()->to('/doctor/consultations/my-schedule')->with('error', 'You do not have access to this consultation.');
        }

        if ($consultationModel->delete($id)) {
            return redirect()->to('/doctor/consultations/my-schedule')->with('success', 'Consultation deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to delete consultation.');
        }
    }

    public function startConsultation($patientId = null, $patientSource = 'admin_patients')
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $doctorId = session()->get('user_id');
        $db = \Config\Database::connect();
        $patientModel = new AdminPatientModel();
        $consultationModel = new ConsultationModel();

        // Handle missing parameters
        if (empty($patientId)) {
            return redirect()->to('/doctor/patients')->with('error', 'Patient ID is required.');
        }

        // Get patient data based on source
        $patient = null;
        $patientSourceActual = 'admin_patients';
        
        if ($patientSource === 'admin_patients' || $patientSource === 'admin') {
            $patient = $patientModel->find($patientId);
        } else {
            // Try patients table (receptionist patients)
            if ($db->tableExists('patients')) {
                $hmsPatient = $db->table('patients')
                    ->where('patient_id', $patientId)
                    ->get()
                    ->getRowArray();
                
                if ($hmsPatient) {
                    // Format patient data to match admin_patients structure
                    $nameParts = [];
                    if (!empty($hmsPatient['first_name'])) $nameParts[] = $hmsPatient['first_name'];
                    if (!empty($hmsPatient['last_name'])) $nameParts[] = $hmsPatient['last_name'];
                    
                    if (empty($nameParts) && !empty($hmsPatient['full_name'])) {
                        $parts = explode(' ', $hmsPatient['full_name'], 2);
                        $nameParts = [$parts[0] ?? '', $parts[1] ?? ''];
                    }
                    
                    // Calculate age
                    $age = null;
                    if (!empty($hmsPatient['date_of_birth'])) {
                        try {
                            $birth = new \DateTime($hmsPatient['date_of_birth']);
                            $today = new \DateTime();
                            $age = (int)$today->diff($birth)->y;
                        } catch (\Exception $e) {
                            $age = $hmsPatient['age'] ?? null;
                        }
                    } elseif (!empty($hmsPatient['age'])) {
                        $age = (int)$hmsPatient['age'];
                    }
                    
                    $patient = [
                        'id' => $hmsPatient['patient_id'] ?? $hmsPatient['id'] ?? null,
                        'patient_id' => $hmsPatient['patient_id'] ?? $hmsPatient['id'] ?? null,
                        'firstname' => $nameParts[0] ?? '',
                        'lastname' => $nameParts[1] ?? '',
                        'full_name' => $hmsPatient['full_name'] ?? implode(' ', $nameParts),
                        'birthdate' => $hmsPatient['date_of_birth'] ?? $hmsPatient['birthdate'] ?? null,
                        'age' => $age,
                        'gender' => strtolower($hmsPatient['gender'] ?? ''),
                        'contact' => $hmsPatient['contact'] ?? null,
                        'address' => $hmsPatient['address'] ?? null,
                        'type' => $hmsPatient['type'] ?? 'Out-Patient',
                        'visit_type' => $hmsPatient['visit_type'] ?? null,
                        'doctor_id' => $hmsPatient['doctor_id'] ?? null,
                        'source' => 'receptionist',
                    ];
                    $patientSourceActual = 'patients';
                }
            }
        }

        if (!$patient) {
            return redirect()->to('/doctor/patients')->with('error', 'Patient not found.');
        }

        // Verify this patient is assigned to this doctor
        if (($patient['doctor_id'] ?? null) != $doctorId) {
            return redirect()->to('/doctor/patients')->with('error', 'You do not have access to this patient.');
        }

        // Calculate age if not already calculated
        if (empty($patient['age']) && !empty($patient['birthdate'])) {
            try {
                $birth = new \DateTime($patient['birthdate']);
                $today = new \DateTime();
                $patient['age'] = (int)$today->diff($birth)->y;
            } catch (\Exception $e) {
                $patient['age'] = null;
            }
        }

        // Get queue number - count existing consultations/appointments for this doctor today
        $today = date('Y-m-d');
        $queueNumber = 1;
        
        if ($db->tableExists('consultations')) {
            $todayConsultations = $db->table('consultations')
                ->where('doctor_id', $doctorId)
                ->where('consultation_date', $today)
                ->whereNotIn('status', ['cancelled'])
                ->countAllResults();
            $queueNumber = $todayConsultations + 1;
        }
        
        // Also check appointments table
        if ($db->tableExists('appointments')) {
            $todayAppointments = $db->table('appointments')
                ->where('doctor_id', $doctorId)
                ->where('appointment_date', $today)
                ->whereNotIn('status', ['cancelled', 'no_show'])
                ->countAllResults();
            $queueNumber = max($queueNumber, $todayAppointments + 1);
        }

        // Get existing consultation for this patient today (if any)
        $existingConsultation = null;
        $patientIdForConsultation = ($patientSourceActual === 'patients') ? ($patient['patient_id'] ?? $patient['id']) : $patient['id'];
        
        // Try to find consultation in consultations table
        // Note: consultations table uses admin_patients.id, so we need to find the admin_patients record
        if ($patientSourceActual === 'patients' && $db->tableExists('admin_patients')) {
            $adminPatient = $db->table('admin_patients')
                ->where('firstname', $patient['firstname'] ?? '')
                ->where('lastname', $patient['lastname'] ?? '')
                ->where('doctor_id', $doctorId)
                ->get()
                ->getRowArray();
            
            if ($adminPatient) {
                $patientIdForConsultation = $adminPatient['id'];
            }
        }
        
        if ($db->tableExists('consultations')) {
            $existingConsultation = $consultationModel
                ->where('patient_id', $patientIdForConsultation)
                ->where('doctor_id', $doctorId)
                ->where('consultation_date', $today)
                ->where('type', 'upcoming')
                ->orderBy('created_at', 'DESC')
                ->first();
            
            // Extract queue number from notes if available
            if ($existingConsultation && !empty($existingConsultation['notes'])) {
                if (preg_match('/Queue #(\d+)/', $existingConsultation['notes'], $matches)) {
                    $queueNumber = (int)$matches[1];
                }
            }
        }

        $data = [
            'title' => 'Start Consultation',
            'patient' => $patient,
            'patient_source' => $patientSourceActual,
            'visit_type' => $patient['visit_type'] ?? 'Consultation',
            'queue_number' => $queueNumber,
            'existing_consultation' => $existingConsultation,
            'validation' => \Config\Services::validation()
        ];

        return view('doctor/consultations/start', $data);
    }

    public function saveConsultation()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $consultationModel = new ConsultationModel();
        $doctorId = session()->get('user_id');
        $db = \Config\Database::connect();

        $validation = $this->validate([
            'patient_id' => 'required|integer|greater_than[0]',
            'patient_source' => 'required|in_list[admin_patients,patients]',
            'consultation_date' => 'required|valid_date',
            'consultation_time' => 'required|regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/]',
            'observations' => 'permit_empty|max_length[5000]',
            'diagnosis' => 'permit_empty|max_length[2000]',
            'notes' => 'permit_empty|max_length[2000]'
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $patientId = $this->request->getPost('patient_id');
        $patientSource = $this->request->getPost('patient_source');
        $queueNumber = $this->request->getPost('queue_number');

        // For patients from patients table, find corresponding admin_patients.id
        $adminPatientId = $patientId;
        if ($patientSource === 'patients' && $db->tableExists('admin_patients')) {
            // Get patient from patients table
            $hmsPatient = $db->table('patients')
                ->where('patient_id', $patientId)
                ->get()
                ->getRowArray();
            
            if ($hmsPatient) {
                // Find or create admin_patients record
                $nameParts = [];
                if (!empty($hmsPatient['first_name'])) $nameParts[] = $hmsPatient['first_name'];
                if (!empty($hmsPatient['last_name'])) $nameParts[] = $hmsPatient['last_name'];
                if (empty($nameParts) && !empty($hmsPatient['full_name'])) {
                    $parts = explode(' ', $hmsPatient['full_name'], 2);
                    $nameParts = [$parts[0] ?? '', $parts[1] ?? ''];
                }
                
                $adminPatient = $db->table('admin_patients')
                    ->where('firstname', $nameParts[0] ?? '')
                    ->where('lastname', $nameParts[1] ?? '')
                    ->where('doctor_id', $doctorId)
                    ->get()
                    ->getRowArray();
                
                if ($adminPatient) {
                    $adminPatientId = $adminPatient['id'];
                } else {
                    // Create admin_patients record
                    $adminPatientData = [
                        'firstname' => $nameParts[0] ?? '',
                        'lastname' => $nameParts[1] ?? '',
                        'birthdate' => $hmsPatient['date_of_birth'] ?? null,
                        'gender' => strtolower($hmsPatient['gender'] ?? 'other'),
                        'contact' => $hmsPatient['contact'] ?? null,
                        'address' => $hmsPatient['address'] ?? null,
                        'doctor_id' => $doctorId,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                    $db->table('admin_patients')->insert($adminPatientData);
                    $adminPatientId = $db->insertID();
                }
            }
        }

        $data = [
            'doctor_id' => $doctorId,
            'patient_id' => $adminPatientId, // Use admin_patients.id for consultations table
            'consultation_date' => $this->request->getPost('consultation_date'),
            'consultation_time' => $this->request->getPost('consultation_time'),
            'type' => 'completed',
            'status' => 'approved',
            'observations' => $this->request->getPost('observations'),
            'diagnosis' => $this->request->getPost('diagnosis'),
            'notes' => $this->request->getPost('notes') . ($queueNumber ? ' | Queue #' . $queueNumber : ''),
        ];

        // Update or create consultation record
        $consultationId = null;
        if ($consultationModel->insert($data)) {
            $consultationId = $consultationModel->getInsertID();
            
            // Update patient status to mark consultation as completed
            // Update in admin_patients table - use direct DB update to ensure updated_at is set
            $db = \Config\Database::connect();
            $db->table('admin_patients')
                ->where('id', $adminPatientId)
                ->update(['updated_at' => date('Y-m-d H:i:s')]);
            
            // If patient is from patients table, also update there
            if ($patientSource === 'patients' && $db->tableExists('patients')) {
                $hmsPatient = $db->table('patients')
                    ->where('patient_id', $patientId)
                    ->get()
                    ->getRowArray();
                
                if ($hmsPatient) {
                    $db->table('patients')
                        ->where('patient_id', $patientId)
                        ->update(['updated_at' => date('Y-m-d H:i:s')]);
                }
            }
            
            // Generate billing entry for consultation
            if ($db->tableExists('billing')) {
                $consultationFee = 500.00; // Default consultation fee
                $billingData = [
                    'patient_id' => $adminPatientId,
                    'billing_service' => 'Consultation',
                    'amount' => $consultationFee,
                    'status' => 'pending',
                    'description' => 'Doctor Consultation - ' . date('Y-m-d'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $db->table('billing')->insert($billingData);
            }
            
            // Notify Accountant about new billing (if notification system exists)
            if ($db->tableExists('accountant_notifications')) {
                $db->table('accountant_notifications')->insert([
                    'type' => 'new_billing',
                    'title' => 'New Consultation Billing',
                    'message' => 'New consultation billing generated for patient ID: ' . $adminPatientId . '. Amount: ₱' . number_format($consultationFee, 2),
                    'related_id' => $adminPatientId,
                    'related_type' => 'billing',
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
            
            // Redirect to doctor orders page with patient_id for easy order creation
            return redirect()->to('/doctor/orders?patient_id=' . $adminPatientId)->with('success', 'Consultation completed and saved successfully. Billing entry created. You can now create orders for this patient.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to save consultation.');
        }
    }
}
