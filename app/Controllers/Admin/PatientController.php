<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminPatientModel;
use App\Models\HMSPatientModel;
use App\Models\UserModel;
use App\Models\DoctorModel;
use App\Models\RoomModel;
use App\Models\DepartmentModel;

class PatientController extends BaseController
{
    protected $patientModel;
    protected $hmsPatientModel;
    protected $userModel;
    protected $doctorModel;
    protected $roomModel;
    protected $departmentModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->patientModel = new AdminPatientModel();
        $this->hmsPatientModel = new HMSPatientModel();
        $this->userModel = new UserModel();
        $this->doctorModel = new DoctorModel();
        $this->roomModel = new RoomModel();
        $this->departmentModel = new DepartmentModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();
        $type = $this->request->getGet('type'); // In-Patient | Out-Patient
        $roomType = $this->request->getGet('room_type'); // Room type filter
        $search = trim((string)$this->request->getGet('q')); // Combined search
        
        // Build query with search and filter
        $builder = $this->hmsPatientModel->builder();
        $builder->select('patients.*, users.username as doctor_name, doctors.doctor_name as doctor_full_name, rooms.room_type, rooms.room_number as room_num, rooms.ward')
                ->join('users', 'users.id = patients.doctor_id', 'left')
                ->join('doctors', 'doctors.user_id = patients.doctor_id', 'left')
                ->join('rooms', 'rooms.id = patients.room_id', 'left');
        
        // Filter by patient type
        if ($type && in_array($type, ['In-Patient', 'Out-Patient'])) {
            $builder->where('patients.type', $type);
        }
        
        // Filter by room type
        if ($roomType && $roomType !== '') {
            $builder->where('rooms.room_type', $roomType);
        }
        
        // Combined search functionality - patient name, doctor name, room number only
        if ($search !== '') {
            $searchTerm = trim($search);
            $builder->groupStart()
                    ->like('patients.full_name', $searchTerm)
                    ->orLike('patients.first_name', $searchTerm)
                    ->orLike('patients.last_name', $searchTerm)
                    ->orLike('doctors.doctor_name', $searchTerm)
                    ->orLike('users.username', $searchTerm)
                    ->orLike('rooms.room_number', $searchTerm)
                    ->groupEnd();
        }
        
        $builder->orderBy('patients.patient_id', 'ASC');
        $patients = $builder->get()->getResultArray();
        
        // Get bed information for each patient
        foreach ($patients as &$patient) {
            $patientId = $patient['patient_id'] ?? $patient['id'];
            
            if (!empty($patient['room_id'])) {
                $bed = $db->table('beds')
                    ->where('room_id', $patient['room_id'])
                    ->where('current_patient_id', $patientId)
                    ->get()
                    ->getRowArray();
                $patient['bed_number'] = $bed['bed_number'] ?? null;
                $patient['bed_id'] = $bed['id'] ?? null;
            } else {
                $patient['bed_number'] = null;
                $patient['bed_id'] = null;
            }
        }
        
        
        $data = [
            'title' => 'Patient Records',
            'patients' => $patients,
            'filterType' => $type,
            'roomType' => $roomType,
            'query' => $search,
        ];

        return view('admin/patients/index', $data);
    }

    public function create()
    {
        // Get all doctors from doctors table
        $doctors = $this->doctorModel->getAllDoctors();
        
        // Get available rooms for In-Patient registration
        $db = \Config\Database::connect();
        $availableRoomsByType = [];
        $availableBedsByRoom = [];
        $erRooms = [];
        
        if ($db->tableExists('rooms')) {
            // Get ER rooms for Emergency In-Patient registration
            $erRooms = $db->table('rooms')
                ->groupStart()
                    ->where('room_type', 'ER')
                    ->orWhere('ward', 'Emergency')
                    ->orWhere('ward', 'ER')
                ->groupEnd()
                ->where('status', 'available')
                ->orderBy('ward', 'ASC')
                ->orderBy('room_number', 'ASC')
                ->get()
                ->getResultArray();
            
            // Get rooms by type
            $roomTypes = ['Private', 'Semi-Private', 'Ward', 'ICU', 'Isolation', 'NICU'];
            foreach ($roomTypes as $roomType) {
                $rooms = $this->roomModel->getAvailableByType($roomType);
                if (!empty($rooms)) {
                    $availableRoomsByType[$roomType] = $rooms;
                }
            }
            
            // Get beds by room
            if ($db->tableExists('beds')) {
                $allRooms = $db->table('rooms')->get()->getResultArray();
                foreach ($allRooms as $room) {
                    $beds = $db->table('beds')
                        ->where('room_id', $room['id'])
                        ->where('status', 'available')
                        ->get()
                        ->getResultArray();
                    if (!empty($beds)) {
                        $availableBedsByRoom[$room['id']] = $beds;
                    }
                }
            }
        }
        
        $data = [
            'title' => 'Add New Patient',
            'doctors' => $doctors,
            'departments' => $this->departmentModel->findAll(),
            'availableRoomsByType' => $availableRoomsByType,
            'availableBedsByRoom' => $availableBedsByRoom,
            'erRooms' => $erRooms,
            'validation' => \Config\Services::validation(),
        ];

        return view('admin/patients/create', $data);
    }

    public function store()
    {
        // Use the same comprehensive registration logic as Reception
        // This will save to patients table (HMSPatientModel) for comprehensive data
        $type = $this->request->getPost('type');
        
        // Different validation rules for In-Patient vs Out-Patient
        if ($type === 'In-Patient') {
            $rules = [
                'first_name' => 'required|min_length[2]|max_length[60]',
                'last_name' => 'required|min_length[2]|max_length[60]',
                'gender' => 'required|in_list[male,female,other,Male,Female,Other]',
                'date_of_birth' => 'required|valid_date',
                'contact' => 'required|min_length[7]|max_length[20]',
                'address' => 'required|min_length[5]',
                'doctor_id' => 'required|integer',
                'purpose' => 'required|min_length[3]',
                'room_number' => 'required',
                'admission_date' => 'required|valid_date',
                'emergency_name' => 'required|min_length[2]',
                'emergency_relationship' => 'required',
                'emergency_contact' => 'required|min_length[7]',
            ];
        } else {
            $rules = [
                'first_name' => 'required|min_length[2]|max_length[60]',
                'last_name' => 'required|min_length[2]|max_length[60]',
                'gender' => 'required|in_list[male,female,other,Male,Female,Other]',
                'civil_status' => 'permit_empty|in_list[Single,Married,Widowed,Divorced,Separated,Annulled,Other]',
                'date_of_birth' => 'permit_empty|valid_date',
                'type' => 'required|in_list[In-Patient,Out-Patient]',
                'visit_type' => 'required|in_list[Consultation,Check-up,Follow-up,Emergency]',
                'payment_type' => 'permit_empty|in_list[Cash,Insurance,Credit]',
                'blood_type' => 'permit_empty|in_list[A+,A-,B+,B-,AB+,AB-,O+,O-]'
            ];
            
            // Make doctor_id required only for Consultation
            $visitType = $this->request->getPost('visit_type');
            if ($visitType === 'Consultation') {
                $rules['doctor_id'] = 'required|integer|greater_than[0]';
            }
            
            // Validate appointment_date is not in the past
            $rules['appointment_date'] = 'permit_empty|valid_date';
            $rules['appointment_time'] = 'permit_empty|regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/]';
        }
        
        if (!$this->validate($rules)) {
            $validationErrors = $this->validator->getErrors();
            log_message('error', 'Admin Patient Registration Validation Failed: ' . json_encode($validationErrors));
            return redirect()->back()->withInput();
        }

        // Additional validation for In-Patient: admission_date must not be in the past
        if ($type === 'In-Patient') {
            $admissionDate = $this->request->getPost('admission_date');
            if (!empty($admissionDate)) {
                $today = date('Y-m-d');
                if ($admissionDate < $today) {
                    return redirect()->back()->withInput()->with('errors', [
                        'admission_date' => 'Admission date cannot be in the past. Must be today or future.'
                    ]);
                }
            }
        }
        
        // Additional validation for Out-Patient: appointment_date must not be in the past
        if ($type === 'Out-Patient') {
            $appointmentDate = $this->request->getPost('appointment_date');
            $appointmentTime = $this->request->getPost('appointment_time');
            $doctorId = $this->request->getPost('doctor_id');
            
            // Always ensure appointment_date is set and starts from today
            if (empty($appointmentDate)) {
                $appointmentDate = date('Y-m-d');
            }
            
            $today = date('Y-m-d');
            if ($appointmentDate < $today) {
                return redirect()->back()->withInput()->with('error', 'Appointment date must start from today. Cannot select past dates.');
            }
            
            if (!empty($appointmentDate)) {
                // Validate appointment time is within doctor's available schedule
                if (!empty($appointmentTime) && !empty($doctorId) && !empty($appointmentDate)) {
                    $db = \Config\Database::connect();
                    
                    // Get doctor's schedule for the selected date
                    if ($db->tableExists('doctor_schedules')) {
                        $schedules = $db->table('doctor_schedules')
                            ->select('start_time, end_time')
                            ->where('doctor_id', $doctorId)
                            ->where('shift_date', $appointmentDate)
                            ->where('status !=', 'cancelled')
                            ->get()
                            ->getResultArray();
                        
                        if (empty($schedules)) {
                            return redirect()->back()->withInput()->with('error', 'Doctor has no available schedule for the selected date. Please select another date or doctor.');
                        }
                        
                        // Check if selected time is within any schedule block
                        $timeValid = false;
                        $appointmentTimeObj = new \DateTime($appointmentDate . ' ' . $appointmentTime . ':00');
                        
                        foreach ($schedules as $schedule) {
                            $start = new \DateTime($appointmentDate . ' ' . $schedule['start_time']);
                            $end = new \DateTime($appointmentDate . ' ' . $schedule['end_time']);
                            
                            // Handle end time that might be next day
                            if ($end <= $start) {
                                $end->modify('+1 day');
                            }
                            
                            if ($appointmentTimeObj >= $start && $appointmentTimeObj < $end) {
                                $timeValid = true;
                                break;
                            }
                        }
                        
                        if (!$timeValid) {
                            // Get available hours for error message
                            $availableHours = [];
                            foreach ($schedules as $schedule) {
                                $startTime = date('g:i A', strtotime($schedule['start_time']));
                                $endTime = date('g:i A', strtotime($schedule['end_time']));
                                $availableHours[] = "{$startTime} - {$endTime}";
                            }
                            $hoursText = implode(' or ', $availableHours);
                            
                            return redirect()->back()->withInput()->with('error', "Selected time is not within doctor's available schedule. Available hours: {$hoursText}");
                        }
                    }
                }
                
                // If appointment date is today, validate that time is not in the past
                if ($appointmentDate === $today && !empty($appointmentTime)) {
                    $currentTime = date('H:i');
                    if ($appointmentTime < $currentTime) {
                        return redirect()->back()->withInput()->with('error', 'If appointment date is today, appointment time cannot be in the past.');
                    }
                }
            }
        }

        // Process name
        $first = trim((string)$this->request->getPost('first_name'));
        $middle = trim((string)$this->request->getPost('middle_name'));
        $last = trim((string)$this->request->getPost('last_name'));
        $ext = trim((string)$this->request->getPost('extension_name'));
        $nameParts = [$first];
        if ($middle !== '') {
            $nameParts[] = $middle;
        }
        $nameParts[] = $last;
        if ($ext !== '') {
            $nameParts[] = $ext;
        }
        $fullName = trim(implode(' ', array_filter($nameParts)));

        $dob = $this->request->getPost('date_of_birth');
        $age = $this->request->getPost('age');
        if ($dob) {
            try {
                $birth = new \DateTime($dob);
                $today = new \DateTime();
                $age = (int)$today->diff($birth)->y;
            } catch (\Exception $e) {
                $age = $age !== null ? (int)$age : null;
            }
        } else {
            $age = $age !== null ? (int)$age : null;
        }

        // Simplified address handling
        $address = trim((string)$this->request->getPost('address'));
        $addressStreet = trim((string)$this->request->getPost('address_street'));
        $addressBarangay = trim((string)$this->request->getPost('address_barangay'));
        $addressCity = trim((string)$this->request->getPost('address_city'));
        $addressProvince = trim((string)$this->request->getPost('address_province'));
        $composedAddress = $address ?: trim(implode(', ', array_filter([$addressStreet, $addressBarangay, $addressCity, $addressProvince])));

        // Prevent duplicate: same full_name + date_of_birth or contact
        $exists = $this->hmsPatientModel->groupStart()
                ->where('full_name', $fullName)
                ->groupStart()
                    ->where('date_of_birth', $dob ?: null)
                    ->orWhere('contact', $this->request->getPost('contact') ?: null)
                ->groupEnd()
            ->groupEnd()
            ->first();
        if ($exists) {
            return redirect()->back()->withInput()->with('error', 'Duplicate patient detected.');
        }

        $visitType = $this->request->getPost('visit_type') ?: 'Consultation';
        $doctorId = $this->request->getPost('doctor_id') ?: null;
        
        // For Consultation, doctor_id is required
        if ($visitType === 'Consultation' && empty($doctorId)) {
            return redirect()->back()->withInput()->with('error', 'Doctor assignment is required for Consultation. Please select a doctor.');
        }
        
        // Only allow doctor assignment for Consultation, Check-up, Follow-up
        if ($visitType === 'Emergency') {
            $doctorId = null; // Emergency cases go to triage first
        }

        // For Emergency In-Patient: Require ER room selection
        $erRoomId = null;
        $erRoomNumber = null;
        if ($visitType === 'Emergency' && $type === 'In-Patient') {
            $selectedERRoomId = $this->request->getPost('er_room_id');
            
            // Validate that ER room is selected
            if (empty($selectedERRoomId)) {
                return redirect()->back()->withInput()->with('error', 'ER Room assignment is REQUIRED for Emergency In-Patient cases. Please select an ER room.');
            }
            
            // Get and validate selected ER room
            $erRoom = $this->roomModel->find($selectedERRoomId);
            if (!$erRoom) {
                return redirect()->back()->withInput()->with('error', 'Selected ER room not found.');
            }
            
            // Verify it's actually an ER room
            $isERRoom = ($erRoom['room_type'] === 'ER' || $erRoom['ward'] === 'Emergency' || $erRoom['ward'] === 'ER');
            if (!$isERRoom) {
                return redirect()->back()->withInput()->with('error', 'Selected room is not an ER room. Please select a room from Emergency/ER ward.');
            }
            
            // Check if room is available
            if (($erRoom['status'] ?? '') !== 'available' && ($erRoom['status'] ?? '') !== 'Available') {
                return redirect()->back()->withInput()->with('error', 'Selected ER room is not available. Please select another ER room.');
            }
            
            $erRoomId = $selectedERRoomId;
            $erRoomNumber = $erRoom['room_number'];
        }

        // Prepare comprehensive data for patients table
        $data = [
            'patient_reg_no' => $this->request->getPost('patient_reg_no') ?: null,
            'first_name' => $first,
            'middle_name' => $middle ?: null,
            'last_name' => $last,
            'extension_name' => $ext !== '' ? $ext : null,
            'full_name' => $fullName,
            'gender' => $this->request->getPost('gender'),
            'civil_status' => $this->request->getPost('civil_status') ?: null,
            'date_of_birth' => $dob ?: null,
            'age' => $age,
            'contact' => $this->request->getPost('contact') ?: null,
            'address' => $composedAddress ?: null,
            'type' => $type,
            'visit_type' => $visitType,
            'triage_status' => $visitType === 'Emergency' ? 'pending' : null,
            'doctor_id' => $doctorId,
            'purpose' => $this->request->getPost('purpose') ?: null,
            'admission_date' => $type === 'In-Patient' ? $this->request->getPost('admission_date') : null,
            'room_number' => $erRoomNumber ?: ($type === 'In-Patient' ? $this->request->getPost('room_number') : null),
            'room_id' => $erRoomId ?: ($type === 'In-Patient' ? $this->request->getPost('room_id') : null),
            'registration_date' => date('Y-m-d'),
            // Medical Information
            'existing_conditions' => $this->request->getPost('existing_conditions') ?: null,
            'allergies' => $this->request->getPost('allergies') ?: null,
            'current_medications' => $this->request->getPost('current_medications') ?: null,
            'past_surgeries' => $this->request->getPost('past_surgeries') ?: null,
            'family_history' => $this->request->getPost('family_history') ?: null,
            'blood_type' => $this->request->getPost('blood_type') ?: null,
            // Insurance Information
            'insurance_provider' => $this->request->getPost('insurance_provider') ?: null,
            'insurance_number' => $this->request->getPost('insurance_number') ?: null,
            'philhealth_number' => $this->request->getPost('philhealth_number') ?: null,
            'billing_address' => $this->request->getPost('billing_address') ?: null,
            'payment_type' => $this->request->getPost('payment_type') ?: null,
            // Emergency Contact
            'emergency_name' => $this->request->getPost('emergency_name') ?: null,
            'emergency_relationship' => $this->request->getPost('emergency_relationship') ?: null,
            'emergency_contact' => $this->request->getPost('emergency_contact') ?: null,
            'emergency_address' => $this->request->getPost('emergency_address') ?: null,
            // Additional fields
            'email' => $this->request->getPost('email') ?: null,
            'nationality' => $this->request->getPost('nationality') ?: null,
            'religion' => $this->request->getPost('religion') ?: null,
            'address_street' => $addressStreet ?: null,
            'address_barangay' => $addressBarangay ?: null,
            'address_city' => $addressCity ?: null,
            'address_province' => $addressProvince ?: null,
        ];

        // Save to patients table (comprehensive data)
        $this->hmsPatientModel->save($data);
        $patientId = $this->hmsPatientModel->getInsertID();
        
        // If getInsertID() returns 0 or null, try to get it from the saved data
        if (empty($patientId) && !empty($data['patient_id'])) {
            $patientId = $data['patient_id'];
        } elseif (empty($patientId)) {
            // Fallback: get the last inserted patient_id
            $db = \Config\Database::connect();
            $lastPatient = $db->table('patients')
                ->select('patient_id')
                ->where('full_name', $fullName)
                ->where('doctor_id', $doctorId)
                ->orderBy('patient_id', 'DESC')
                ->limit(1)
                ->get()
                ->getRowArray();
            $patientId = $lastPatient['patient_id'] ?? null;
        }
        
        log_message('info', "Admin Patient Registration - patient_id: {$patientId}, doctor_id: {$doctorId}, type: {$type}, visit_type: {$visitType}");
        
        // Handle room assignment for In-Patient
        if ($patientId && $type === 'In-Patient') {
            $selectedRoomId = $this->request->getPost('room_id');
            $selectedRoomNumber = $this->request->getPost('room_number');
            $selectedBedId = $this->request->getPost('bed_id');
            $selectedBedNumber = $this->request->getPost('bed_number');
            
            // Use ER room if Emergency
            if ($visitType === 'Emergency' && $erRoomId) {
                $selectedRoomId = $erRoomId;
                $selectedRoomNumber = $erRoomNumber;
            }
            
            // If room_id is not in POST, try to get it from room_number
            if (empty($selectedRoomId) && !empty($selectedRoomNumber)) {
                $db = \Config\Database::connect();
                $roomByNumber = $db->table('rooms')
                    ->where('room_number', $selectedRoomNumber)
                    ->get()
                    ->getRowArray();
                if ($roomByNumber) {
                    $selectedRoomId = $roomByNumber['id'];
                }
            }
            
            if ($selectedRoomId) {
                $db = \Config\Database::connect();
                $room = $this->roomModel->find($selectedRoomId);
                
                if ($room) {
                    // Assign room to patient
                    $this->roomModel->update($selectedRoomId, [
                        'current_patient_id' => $patientId,
                        'status' => 'Occupied',
                    ]);
                    
                    // Update patient with room info
                    $this->hmsPatientModel->update($patientId, [
                        'room_number' => $selectedRoomNumber ?: $room['room_number'],
                        'room_id' => $selectedRoomId,
                    ]);
                    
                    // Handle bed assignment if bed is selected
                    if ($selectedBedId && $db->tableExists('beds')) {
                        $bed = $db->table('beds')
                            ->where('id', $selectedBedId)
                            ->where('room_id', $selectedRoomId)
                            ->get()
                            ->getRowArray();
                        
                        if ($bed) {
                            // Update bed status
                            $db->table('beds')
                                ->where('id', $selectedBedId)
                                ->update([
                                    'current_patient_id' => $patientId,
                                    'status' => 'occupied',
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                            
                            log_message('info', "Bed {$selectedBedNumber} (ID: {$selectedBedId}) in Room {$selectedRoomNumber} assigned to patient {$patientId}");
                        }
                    }
                    
                    log_message('info', "Room {$selectedRoomNumber} (ID: {$selectedRoomId}) assigned to patient {$patientId}");
                }
            }
        }
        
        // For Consultation, create admin_patients record and consultation
        if ($patientId && $doctorId && in_array($visitType, ['Consultation', 'Check-up', 'Follow-up'])) {
            $db = \Config\Database::connect();
            
            // Extract name parts for admin_patients
            $nameParts = [];
            if (!empty($first)) $nameParts[] = $first;
            if (!empty($last)) $nameParts[] = $last;
            if (empty($nameParts) && !empty($fullName)) {
                $parts = explode(' ', $fullName, 2);
                $nameParts = [$parts[0] ?? '', $parts[1] ?? ''];
            }
            
            // Create or update admin_patients record
            if ($db->tableExists('admin_patients')) {
                $existingAdminPatient = $db->table('admin_patients')
                    ->where('firstname', $nameParts[0] ?? '')
                    ->where('lastname', $nameParts[1] ?? '')
                    ->where('doctor_id', $doctorId)
                    ->where('deleted_at IS NULL', null, false)
                    ->get()
                    ->getRowArray();
                
                if ($existingAdminPatient) {
                    $adminPatientId = $existingAdminPatient['id'];
                    $db->table('admin_patients')
                        ->where('id', $adminPatientId)
                        ->update([
                            'doctor_id' => $doctorId,
                            'visit_type' => $visitType,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                } else {
                    $adminPatientData = [
                        'firstname' => $nameParts[0] ?? '',
                        'lastname' => $nameParts[1] ?? '',
                        'birthdate' => $dob ?: null,
                        'gender' => strtolower($this->request->getPost('gender') ?? 'other'),
                        'contact' => $this->request->getPost('contact') ?: null,
                        'address' => $composedAddress ?: null,
                        'doctor_id' => $doctorId,
                        'visit_type' => $visitType,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                    
                    $db->table('admin_patients')->insert($adminPatientData);
                    $adminPatientId = $db->insertID();
                }
                
                // Create consultation record
                $appointmentDate = $this->request->getPost('appointment_date');
                if (empty($appointmentDate)) {
                    $appointmentDay = $this->request->getPost('appointment_day');
                    if (!empty($appointmentDay)) {
                        $appointmentDate = $appointmentDay;
                    } else {
                        $appointmentDate = date('Y-m-d');
                    }
                }
                $appointmentTimeInput = $this->request->getPost('appointment_time') ?: '09:00';
                $appointmentTime = $appointmentTimeInput . ':00';
                
                if ($db->tableExists('consultations') && !empty($patientId) && !empty($doctorId) && !empty($appointmentDate) && !empty($appointmentTime)) {
                    $consultationModel = new \App\Models\ConsultationModel();
                    try {
                        $consultationData = [
                            'doctor_id' => $doctorId,
                            'patient_id' => $patientId,
                            'consultation_date' => $appointmentDate,
                            'consultation_time' => $appointmentTime,
                            'type' => 'upcoming',
                            'status' => 'approved',
                            'notes' => $this->request->getPost('purpose') ?: "Visit Type: {$visitType}",
                            'created_at' => date('Y-m-d H:i:s'),
                        ];
                        
                        $consultationModel->insert($consultationData);
                        log_message('info', "Consultation created for patient {$patientId} with doctor {$doctorId}");
                    } catch (\Exception $e) {
                        log_message('error', "Failed to create consultation: " . $e->getMessage());
                    }
                }
            }
        }

        return redirect()->to('/admin/patients')->with('success', 'Patient registered successfully.');
    }

    public function edit($id)
    {
        // Get patient from patients table (comprehensive data)
        $patient = $this->hmsPatientModel->find($id);
        
        if (!$patient) {
            return redirect()->to('/admin/patients')->with('error', 'Patient not found.');
        }

        // Get all doctors from doctors table
        $doctors = $this->doctorModel->getAllDoctors();
        
        // Get available rooms for In-Patient registration
        $db = \Config\Database::connect();
        $availableRoomsByType = [];
        $availableBedsByRoom = [];
        $erRooms = [];
        
        if ($db->tableExists('rooms')) {
            // Get ER rooms for Emergency In-Patient registration
            $erRooms = $db->table('rooms')
                ->groupStart()
                    ->where('room_type', 'ER')
                    ->orWhere('ward', 'Emergency')
                    ->orWhere('ward', 'ER')
                ->groupEnd()
                ->where('status', 'available')
                ->orderBy('ward', 'ASC')
                ->orderBy('room_number', 'ASC')
                ->get()
                ->getResultArray();
            
            // Get rooms by type
            $roomTypes = ['Private', 'Semi-Private', 'Ward', 'ICU', 'Isolation', 'NICU'];
            foreach ($roomTypes as $roomType) {
                $rooms = $this->roomModel->getAvailableByType($roomType);
                if (!empty($rooms)) {
                    $availableRoomsByType[$roomType] = $rooms;
                }
            }
            
            // Get beds by room
            if ($db->tableExists('beds')) {
                $allRooms = $db->table('rooms')->get()->getResultArray();
                foreach ($allRooms as $room) {
                    $beds = $db->table('beds')
                        ->where('room_id', $room['id'])
                        ->where('status', 'available')
                        ->get()
                        ->getResultArray();
                    if (!empty($beds)) {
                        $availableBedsByRoom[$room['id']] = $beds;
                    }
                }
            }
        }

        $data = [
            'title' => 'Edit Patient',
            'patient' => $patient,
            'doctors' => $doctors,
            'departments' => $this->departmentModel->findAll(),
            'availableRoomsByType' => $availableRoomsByType,
            'availableBedsByRoom' => $availableBedsByRoom,
            'erRooms' => $erRooms,
            'validation' => \Config\Services::validation(),
        ];

        return view('admin/patients/edit', $data);
    }

    public function update($id)
    {
        // Get patient from patients table (comprehensive data)
        $patient = $this->hmsPatientModel->find($id);
        
        if (!$patient) {
            return redirect()->to('/admin/patients')->with('error', 'Patient not found.');
        }

        // Use the same comprehensive validation logic as store()
        $type = $this->request->getPost('type') ?: $patient['type'] ?? 'In-Patient';
        
        // Different validation rules for In-Patient vs Out-Patient
        if ($type === 'In-Patient') {
            $rules = [
                'first_name' => 'required|min_length[2]|max_length[60]',
                'last_name' => 'required|min_length[2]|max_length[60]',
                'gender' => 'required|in_list[male,female,other,Male,Female,Other]',
                'date_of_birth' => 'required|valid_date',
                'contact' => 'required|min_length[7]|max_length[20]',
                'address' => 'required|min_length[5]',
                'doctor_id' => 'required|integer',
                'purpose' => 'required|min_length[3]',
                'room_number' => 'required',
                'admission_date' => 'required|valid_date',
                'emergency_name' => 'required|min_length[2]',
                'emergency_relationship' => 'required',
                'emergency_contact' => 'required|min_length[7]',
            ];
        } else {
            $rules = [
                'first_name' => 'required|min_length[2]|max_length[60]',
                'last_name' => 'required|min_length[2]|max_length[60]',
                'gender' => 'required|in_list[male,female,other,Male,Female,Other]',
                'civil_status' => 'permit_empty|in_list[Single,Married,Widowed,Divorced,Separated,Annulled,Other]',
                'date_of_birth' => 'permit_empty|valid_date',
                'type' => 'required|in_list[In-Patient,Out-Patient]',
                'visit_type' => 'required|in_list[Consultation,Check-up,Follow-up,Emergency]',
                'payment_type' => 'permit_empty|in_list[Cash,Insurance,Credit]',
                'blood_type' => 'permit_empty|in_list[A+,A-,B+,B-,AB+,AB-,O+,O-]'
            ];
            
            // Make doctor_id required only for Consultation
            $visitType = $this->request->getPost('visit_type');
            if ($visitType === 'Consultation') {
                $rules['doctor_id'] = 'required|integer|greater_than[0]';
            }
            
            // Validate appointment_date is not in the past
            $rules['appointment_date'] = 'permit_empty|valid_date';
            $rules['appointment_time'] = 'permit_empty|regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/]';
        }
        
        if (!$this->validate($rules)) {
            $validationErrors = $this->validator->getErrors();
            log_message('error', 'Admin Patient Update Validation Failed: ' . json_encode($validationErrors));
            return redirect()->back()->withInput();
        }

        // Process name
        $first = trim((string)$this->request->getPost('first_name'));
        $middle = trim((string)$this->request->getPost('middle_name'));
        $last = trim((string)$this->request->getPost('last_name'));
        $ext = trim((string)$this->request->getPost('extension_name'));
        $nameParts = [$first];
        if ($middle !== '') {
            $nameParts[] = $middle;
        }
        $nameParts[] = $last;
        if ($ext !== '') {
            $nameParts[] = $ext;
        }
        $fullName = trim(implode(' ', array_filter($nameParts)));

        $dob = $this->request->getPost('date_of_birth');
        $age = $this->request->getPost('age');
        if ($dob) {
            try {
                $birth = new \DateTime($dob);
                $today = new \DateTime();
                $age = (int)$today->diff($birth)->y;
            } catch (\Exception $e) {
                $age = $age !== null ? (int)$age : null;
            }
        } else {
            $age = $age !== null ? (int)$age : null;
        }

        // Simplified address handling
        $address = trim((string)$this->request->getPost('address'));
        $addressStreet = trim((string)$this->request->getPost('address_street'));
        $addressBarangay = trim((string)$this->request->getPost('address_barangay'));
        $addressCity = trim((string)$this->request->getPost('address_city'));
        $addressProvince = trim((string)$this->request->getPost('address_province'));
        $composedAddress = $address ?: trim(implode(', ', array_filter([$addressStreet, $addressBarangay, $addressCity, $addressProvince])));

        $visitType = $this->request->getPost('visit_type') ?: 'Consultation';
        $doctorId = $this->request->getPost('doctor_id') ?: null;
        
        // For Consultation, doctor_id is required
        if ($visitType === 'Consultation' && empty($doctorId)) {
            return redirect()->back()->withInput()->with('error', 'Doctor assignment is required for Consultation. Please select a doctor.');
        }
        
        // Only allow doctor assignment for Consultation, Check-up, Follow-up
        if ($visitType === 'Emergency') {
            $doctorId = null; // Emergency cases go to triage first
        }

        // Prepare comprehensive data for patients table
        $data = [
            'patient_reg_no' => $this->request->getPost('patient_reg_no') ?: $patient['patient_reg_no'] ?? null,
            'first_name' => $first,
            'middle_name' => $middle ?: null,
            'last_name' => $last,
            'extension_name' => $ext !== '' ? $ext : null,
            'full_name' => $fullName,
            'gender' => $this->request->getPost('gender'),
            'civil_status' => $this->request->getPost('civil_status') ?: null,
            'date_of_birth' => $dob ?: null,
            'age' => $age,
            'contact' => $this->request->getPost('contact') ?: null,
            'address' => $composedAddress ?: null,
            'type' => $type,
            'visit_type' => $visitType,
            'triage_status' => $visitType === 'Emergency' ? 'pending' : ($patient['triage_status'] ?? null),
            'doctor_id' => $doctorId,
            'purpose' => $this->request->getPost('purpose') ?: null,
            'admission_date' => $type === 'In-Patient' ? $this->request->getPost('admission_date') : null,
            'room_number' => $type === 'In-Patient' ? $this->request->getPost('room_number') : null,
            'room_id' => $type === 'In-Patient' ? $this->request->getPost('room_id') : null,
            // Medical Information
            'existing_conditions' => $this->request->getPost('existing_conditions') ?: null,
            'allergies' => $this->request->getPost('allergies') ?: null,
            'current_medications' => $this->request->getPost('current_medications') ?: null,
            'past_surgeries' => $this->request->getPost('past_surgeries') ?: null,
            'family_history' => $this->request->getPost('family_history') ?: null,
            'blood_type' => $this->request->getPost('blood_type') ?: null,
            // Insurance Information
            'insurance_provider' => $this->request->getPost('insurance_provider') ?: null,
            'insurance_number' => $this->request->getPost('insurance_number') ?: null,
            'philhealth_number' => $this->request->getPost('philhealth_number') ?: null,
            'billing_address' => $this->request->getPost('billing_address') ?: null,
            'payment_type' => $this->request->getPost('payment_type') ?: null,
            // Emergency Contact
            'emergency_name' => $this->request->getPost('emergency_name') ?: null,
            'emergency_relationship' => $this->request->getPost('emergency_relationship') ?: null,
            'emergency_contact' => $this->request->getPost('emergency_contact') ?: null,
            'emergency_address' => $this->request->getPost('emergency_address') ?: null,
            // Additional fields
            'email' => $this->request->getPost('email') ?: null,
            'nationality' => $this->request->getPost('nationality') ?: null,
            'religion' => $this->request->getPost('religion') ?: null,
            'address_street' => $addressStreet ?: null,
            'address_barangay' => $addressBarangay ?: null,
            'address_city' => $addressCity ?: null,
            'address_province' => $addressProvince ?: null,
        ];

        // Update patients table (comprehensive data)
        $this->hmsPatientModel->update($id, $data);
        
        log_message('info', "Admin Patient Update - patient_id: {$id}, doctor_id: {$doctorId}, type: {$type}, visit_type: {$visitType}");
        
        // Handle room assignment for In-Patient
        if ($type === 'In-Patient') {
            $selectedRoomId = $this->request->getPost('room_id');
            $selectedRoomNumber = $this->request->getPost('room_number');
            $selectedBedId = $this->request->getPost('bed_id');
            $selectedBedNumber = $this->request->getPost('bed_number');
            
            // If room_id is not in POST, try to get it from room_number
            if (empty($selectedRoomId) && !empty($selectedRoomNumber)) {
                $db = \Config\Database::connect();
                $roomByNumber = $db->table('rooms')
                    ->where('room_number', $selectedRoomNumber)
                    ->get()
                    ->getRowArray();
                if ($roomByNumber) {
                    $selectedRoomId = $roomByNumber['id'];
                }
            }
            
            if ($selectedRoomId) {
                $db = \Config\Database::connect();
                $room = $this->roomModel->find($selectedRoomId);
                
                if ($room) {
                    // Release old room if different
                    $oldRoomId = $patient['room_id'] ?? null;
                    if ($oldRoomId && $oldRoomId != $selectedRoomId) {
                        $oldRoom = $this->roomModel->find($oldRoomId);
                        if ($oldRoom) {
                            $this->roomModel->update($oldRoomId, [
                                'current_patient_id' => null,
                                'status' => 'Available',
                            ]);
                        }
                    }
                    
                    // Assign new room to patient
                    $this->roomModel->update($selectedRoomId, [
                        'current_patient_id' => $id,
                        'status' => 'Occupied',
                    ]);
                    
                    // Handle bed assignment if bed is selected
                    if ($selectedBedId && $db->tableExists('beds')) {
                        // Release old bed if different
                        $oldBedId = null;
                        if ($db->tableExists('beds') && !empty($oldRoomId)) {
                            $oldBed = $db->table('beds')
                                ->where('room_id', $oldRoomId)
                                ->where('current_patient_id', $id)
                                ->get()
                                ->getRowArray();
                            if ($oldBed) {
                                $oldBedId = $oldBed['id'];
                            }
                        }
                        
                        if ($oldBedId && $oldBedId != $selectedBedId) {
                            $db->table('beds')
                                ->where('id', $oldBedId)
                                ->update([
                                    'current_patient_id' => null,
                                    'status' => 'available',
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        }
                        
                        $bed = $db->table('beds')
                            ->where('id', $selectedBedId)
                            ->where('room_id', $selectedRoomId)
                            ->get()
                            ->getRowArray();
                        
                        if ($bed) {
                            // Update bed status
                            $db->table('beds')
                                ->where('id', $selectedBedId)
                                ->update([
                                    'current_patient_id' => $id,
                                    'status' => 'occupied',
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        }
                    }
                }
            }
        }

        return redirect()->to('/admin/patients')->with('success', 'Patient updated successfully.');
    }

    public function delete($id)
    {
        // Get patient from patients table (comprehensive data)
        $patient = $this->hmsPatientModel->find($id);
        
        if (!$patient) {
            return redirect()->to('/admin/patients')->with('error', 'Patient not found.');
        }

        // Release room and bed if assigned
        if (!empty($patient['room_id'])) {
            $room = $this->roomModel->find($patient['room_id']);
            if ($room) {
                $this->roomModel->update($patient['room_id'], [
                    'current_patient_id' => null,
                    'status' => 'Available',
                ]);
            }
            
            // Release bed if assigned
            $db = \Config\Database::connect();
            if ($db->tableExists('beds')) {
                $bed = $db->table('beds')
                    ->where('room_id', $patient['room_id'])
                    ->where('current_patient_id', $id)
                    ->get()
                    ->getRowArray();
                if ($bed) {
                    $db->table('beds')
                        ->where('id', $bed['id'])
                        ->update([
                            'current_patient_id' => null,
                            'status' => 'available',
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                }
            }
        }

        $this->hmsPatientModel->delete($id);

        return redirect()->to('/admin/patients')->with('success', 'Patient deleted successfully.');
    }

    public function show($id)
    {
        $db = \Config\Database::connect();
        // Get patient from patients table (comprehensive data)
        $patient = $this->hmsPatientModel
            ->select('patients.*, users.username as doctor_name, doctors.doctor_name as doctor_full_name, rooms.room_type, rooms.room_number as room_num, rooms.ward, departments.department_name')
            ->join('users', 'users.id = patients.doctor_id', 'left')
            ->join('doctors', 'doctors.user_id = patients.doctor_id', 'left')
            ->join('rooms', 'rooms.id = patients.room_id', 'left')
            ->join('departments', 'departments.id = patients.department_id', 'left')
            ->find($id);
        
        if (!$patient) {
            return redirect()->to('/admin/patients')->with('error', 'Patient not found.');
        }
        
        // Get bed information
        $bed = null;
        if (!empty($patient['room_id'])) {
            $bed = $db->table('beds')
                ->where('room_id', $patient['room_id'])
                ->where('current_patient_id', $patient['patient_id'] ?? $id)
                ->get()
                ->getRowArray();
        }
        
        // Get consultation/appointment info for Out-Patient
        $consultation = null;
        if (($patient['type'] ?? '') === 'Out-Patient' && $db->tableExists('consultations')) {
            $consultation = $db->table('consultations')
                ->where('patient_id', $patient['patient_id'] ?? $id)
                ->orderBy('consultation_date', 'DESC')
                ->orderBy('consultation_time', 'DESC')
                ->get()
                ->getRowArray();
        }
        
        $data = [
            'title' => 'Patient Details',
            'patient' => $patient,
            'bed' => $bed,
            'consultation' => $consultation,
        ];

        return view('admin/patients/view', $data);
    }
}

