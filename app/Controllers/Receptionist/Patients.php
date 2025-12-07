<?php

namespace App\Controllers\Receptionist;

use App\Controllers\BaseController;
use App\Models\HMSPatientModel;
use App\Models\DepartmentModel;
use App\Models\DoctorModel;
use App\Models\RoomModel;

class Patients extends BaseController
{
    protected $patientModel;
    protected $departmentModel;
    protected $doctorModel;
    protected $roomModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->patientModel = new HMSPatientModel();
        $this->departmentModel = new DepartmentModel();
        $this->doctorModel = new DoctorModel();
        $this->roomModel = new RoomModel();
    }

    public function index()
    {
        $type = $this->request->getGet('type'); // In-Patient | Out-Patient
        $search = trim((string)$this->request->getGet('q'));

        $builder = $this->patientModel->builder();
        $builder->select('patients.*, doctors.doctor_name, departments.department_name')
                ->join('doctors', 'doctors.user_id = patients.doctor_id', 'left') // Fix: use user_id instead of id
                ->join('departments', 'departments.id = patients.department_id', 'left');

        if ($type && in_array($type, ['In-Patient', 'Out-Patient'])) {
            $builder->where('patients.type', $type);
        }
        if ($search !== '') {
            $builder->groupStart()
                    ->like('patients.full_name', $search)
                    ->orLike('patients.patient_id', $search)
                    ->orLike('doctors.doctor_name', $search)
                    ->groupEnd();
        }
        $builder->orderBy('patients.patient_id', 'DESC');
        $patients = $builder->get()->getResultArray();

        return view('Reception/patients/index', [
            'title' => 'Patient Records',
            'patients' => $patients,
            'filterType' => $type,
            'query' => $search,
        ]);
    }

    /**
     * Register In-Patient (dedicated route)
     */
    public function register()
    {
        return $this->createWithType('In-Patient');
    }

    /**
     * Register Out-Patient (dedicated route)
     */
    public function outpatient()
    {
        return $this->createWithType('Out-Patient');
    }

    /**
     * Create patient registration form (backward compatibility - accepts type parameter)
     */
    public function create()
    {
        $prefType = $this->request->getGet('type'); // In-Patient | Out-Patient from link
        if (!in_array($prefType, ['In-Patient', 'Out-Patient'], true)) {
            $prefType = 'Out-Patient';
        }
        return $this->createWithType($prefType);
    }

    /**
     * Internal method to create registration form with specified type
     */
    private function createWithType($prefType)
    {

        $availableRoomsByWard = [];
        $erRooms = [];
        
        // Get ER rooms for Emergency In-Patient registration
        $db = \Config\Database::connect();
        if ($db->tableExists('rooms')) {
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
        }
        
        // Get all available rooms grouped by room type for In-Patient registration
        $availableRoomsByType = [];
        $availableBedsByRoom = [];
        
        if ($prefType === 'In-Patient' && $db->tableExists('rooms')) {
            $roomTypes = ['Private', 'Semi-Private', 'Ward', 'ICU', 'Isolation', 'NICU'];
            foreach ($roomTypes as $roomType) {
                // Get available rooms for this type (case-insensitive status check)
                $rooms = $db->table('rooms')
                    ->select('id, room_number, room_type, ward, bed_count, price, status')
                    ->where('room_type', $roomType)
                    ->where('(status = "available" OR status = "Available" OR status = "AVAILABLE")', null, false)
                    ->where('current_patient_id IS NULL', null, false)
                    ->orderBy('room_number', 'ASC')
                    ->get()
                    ->getResultArray();
                
                // For each room, get available beds
                foreach ($rooms as &$room) {
                    if ($db->tableExists('beds')) {
                        $beds = $db->table('beds')
                            ->select('id, bed_number, status')
                            ->where('room_id', $room['id'])
                            ->where('(status = "available" OR status = "Available" OR status = "AVAILABLE")', null, false)
                            ->where('current_patient_id IS NULL', null, false)
                            ->orderBy('bed_number', 'ASC')
                            ->get()
                            ->getResultArray();
                        
                        $room['available_beds'] = $beds;
                        $availableBedsByRoom[$room['id']] = $beds;
                    } else {
                        $room['available_beds'] = [];
                    }
                }
                
                $availableRoomsByType[$roomType] = $rooms;
            }
            
            // Also get rooms by ward for backward compatibility
            $wardNames = ['Pedia Ward', 'Male Ward', 'Female Ward'];
            foreach ($wardNames as $wardName) {
                $availableRoomsByWard[$wardName] = $this->roomModel->getAvailableByWard($wardName);
            }
        }

        // Get doctors from doctors table
        $allDoctors = $this->doctorModel->getAllDoctors();

        $viewName = $prefType === 'Out-Patient'
            ? 'Reception/patients/Outpatient'
            : 'Reception/patients/register';

        return view($viewName, [
            'title' => 'Register Patient',
            'departments' => $this->departmentModel->findAll(),
            'doctors' => $allDoctors,
            'validation' => \Config\Services::validation(),
            'initialType' => $prefType,
            'availableRoomsByWard' => $availableRoomsByWard,
            'availableRoomsByType' => $availableRoomsByType ?? [],
            'availableBedsByRoom' => $availableBedsByRoom ?? [],
            'erRooms' => $erRooms, // Pass ER rooms to view
        ]);
    }

    public function store()
    {
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
            log_message('error', 'Patient Registration Validation Failed: ' . json_encode($validationErrors));
            log_message('error', 'POST Data: ' . json_encode($this->request->getPost()));
            // withInput() automatically stores validation errors in _ci_validation_errors
            return redirect()->back()->withInput();
        }

        // Additional validation for In-Patient: admission_date must not be in the past
        if ($type === 'In-Patient') {
            $admissionDate = $this->request->getPost('admission_date');
            if (!empty($admissionDate)) {
                $today = date('Y-m-d');
                if ($admissionDate < $today) {
                    return redirect()->back()->withInput()->with('errors', [
                        'admission_date' => 'Ang admission date ay hindi maaaring nasa nakaraan. Dapat ngayon o sa hinaharap.'
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
                return redirect()->back()->withInput()->with('error', 'Ang appointment date ay dapat magsimula sa ngayon. Hindi maaaring pumili ng nakaraang petsa.');
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
                            return redirect()->back()->withInput()->with('error', 'Ang doctor ay walang available schedule para sa napiling petsa. Paki-pili ng ibang petsa o doctor.');
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
                            $hoursText = implode(' at ', $availableHours);
                            
                            return redirect()->back()->withInput()->with('error', "Ang napiling oras ay wala sa available schedule ng doctor. Available hours: {$hoursText}");
                        }
                    }
                }
                
                // If appointment date is today, validate that time is not in the past
                if ($appointmentDate === $today && !empty($appointmentTime)) {
                    $currentTime = date('H:i');
                    if ($appointmentTime < $currentTime) {
                        return redirect()->back()->withInput()->with('error', 'Kung ang appointment date ay ngayon, ang appointment time ay hindi maaaring nasa nakaraan.');
                    }
                }
            }
        }

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

        // Simplified address handling - single field
        $address = trim((string)$this->request->getPost('address'));
        // Keep old address fields for backward compatibility but use simplified address if provided
        $addressStreet = trim((string)$this->request->getPost('address_street'));
        $addressBarangay = trim((string)$this->request->getPost('address_barangay'));
        $addressCity = trim((string)$this->request->getPost('address_city'));
        $addressProvince = trim((string)$this->request->getPost('address_province'));
        $composedAddress = $address ?: trim(implode(', ', array_filter([$addressStreet, $addressBarangay, $addressCity, $addressProvince])));

        // Prevent duplicate: same full_name + date_of_birth or contact
        $exists = $this->patientModel->groupStart()
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

        // For Emergency In-Patient: Require ER room selection (NO AUTO-ASSIGN)
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
            // Insurance Information
            'insurance_provider' => $this->request->getPost('insurance_provider') ?: null,
            'insurance_number' => $this->request->getPost('insurance_number') ?: null,
            // Emergency Contact
            'emergency_name' => $this->request->getPost('emergency_name') ?: null,
            'emergency_relationship' => $this->request->getPost('emergency_relationship') ?: null,
            'emergency_contact' => $this->request->getPost('emergency_contact') ?: null,
        ];

        $this->patientModel->save($data);

        // Get the inserted patient_id (primary key is 'patient_id', not 'id')
        $patientId = $this->patientModel->getInsertID();
        
        // Debug: Log patient registration details
        log_message('info', "In-Patient Registration - patient_id: {$patientId}, doctor_id: {$doctorId}, type: {$type}, visit_type: {$visitType}");
        
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
        
        // Log for debugging
        log_message('debug', "Patient registered - patient_id: {$patientId}, doctor_id: {$doctorId}, type: {$type}, visit_type: {$visitType}");
        
        // Handle ER room assignment for Emergency In-Patient cases
        if ($patientId && $type === 'In-Patient' && $visitType === 'Emergency') {
            $db = \Config\Database::connect();
            
            // Use the ER room that was auto-assigned or selected
            if ($erRoomId) {
                $room = $this->roomModel->find($erRoomId);
                if ($room && ($room['status'] ?? '') !== 'Occupied') {
                    // Update room status
                    $this->roomModel->update($erRoomId, [
                        'current_patient_id' => $patientId,
                        'status' => 'Occupied',
                    ]);
                    
                    // Update patient with ER room info
                    $this->patientModel->update($patientId, [
                        'room_number' => $erRoomNumber,
                        'room_id' => $erRoomId,
                        'triage_status' => 'pending', // Pending triage for vital signs check
                    ]);
                    
                    // Create bed assignment if beds table exists
                    if ($db->tableExists('beds')) {
                        $bed = $db->table('beds')
                            ->where('room_id', $erRoomId)
                            ->where('status', 'available')
                            ->limit(1)
                            ->get()
                            ->getRowArray();
                        
                        if ($bed) {
                            $db->table('beds')
                                ->where('id', $bed['id'])
                                ->update([
                                    'current_patient_id' => $patientId,
                                    'status' => 'occupied',
                                ]);
                        }
                    }
                    
                    log_message('info', "ER room {$erRoomNumber} (ID: {$erRoomId}) assigned to Emergency patient {$patientId}");
                }
            }
        }
        
        // Handle regular room assignment for In-Patient cases (non-emergency)
        if ($patientId && $type === 'In-Patient' && $visitType !== 'Emergency') {
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
                    // AUTOMATIC ASSIGNMENT: Always assign the selected room to the patient
                    // This ensures the patient appears in the room management immediately after registration
                    $this->roomModel->update($selectedRoomId, [
                        'current_patient_id' => $patientId,
                        'status' => 'Occupied',
                    ]);
                    
                    // Update patient with room info (ensure both room_id and room_number are set)
                    $this->patientModel->update($patientId, [
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
                            // Update bed status (assign bed to patient)
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
                    
                    log_message('info', "Room {$selectedRoomNumber} (ID: {$selectedRoomId}) automatically assigned to patient {$patientId} during registration");
                } else {
                    log_message('error', "Room ID {$selectedRoomId} not found when trying to assign to patient {$patientId}");
                }
            } else {
                log_message('warning', "No room selected for In-Patient registration - patient {$patientId} registered without room assignment");
            }
        }

        // For Consultation, ensure patient is immediately assigned to doctor and appears in doctor's patient list
        // Create admin_patients record if doctor is assigned (for doctor orders compatibility)
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
            
            // Check if admin_patients record already exists
            $existingAdminPatient = null;
            if (!empty($nameParts[0]) && !empty($nameParts[1]) && $db->tableExists('admin_patients')) {
                $existingAdminPatient = $db->table('admin_patients')
                    ->where('firstname', $nameParts[0])
                    ->where('lastname', $nameParts[1])
                    ->where('doctor_id', $doctorId)
                    ->where('deleted_at IS NULL', null, false)
                    ->get()
                    ->getRowArray();
            }
            
            $adminPatientId = null;
            if ($existingAdminPatient) {
                $adminPatientId = $existingAdminPatient['id'];
                // Update doctor_id, visit_type, and timestamp
                $db->table('admin_patients')
                    ->where('id', $adminPatientId)
                    ->update([
                        'doctor_id' => $doctorId,
                        'visit_type' => $visitType,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
            } else {
                // Create admin_patients record
                if ($db->tableExists('admin_patients')) {
                    $adminPatientData = [
                        'firstname' => $nameParts[0] ?? '',
                        'lastname' => $nameParts[1] ?? '',
                        'birthdate' => $dob ?: null,
                        'gender' => strtolower($this->request->getPost('gender') ?? 'other'),
                        'contact' => $this->request->getPost('contact') ?: null,
                        'address' => $composedAddress ?: null,
                        'doctor_id' => $doctorId,
                        'visit_type' => $visitType, // Include visit_type
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                    
                    try {
                        $db->table('admin_patients')->insert($adminPatientData);
                        $adminPatientId = $db->insertID();
                        log_message('info', "Receptionist: Created admin_patients record ID {$adminPatientId} for patient_id: {$patientId}");
                    } catch (\Exception $e) {
                        log_message('error', "Receptionist: Failed to create admin_patients record: " . $e->getMessage());
                        // Continue - OrderController will handle it later
                    }
                }
            }
            
            // Create consultation record (uses admin_patients.id)
            // Get appointment_date from hidden field (set by JavaScript from appointment_day dropdown)
            $appointmentDate = $this->request->getPost('appointment_date');
            // If appointment_date is empty, try to get from appointment_day (which is now the actual date)
            if (empty($appointmentDate)) {
                $appointmentDay = $this->request->getPost('appointment_day');
                if (!empty($appointmentDay)) {
                    // appointment_day is now the actual date (YYYY-MM-DD format)
                    $appointmentDate = $appointmentDay;
                } else {
                    $appointmentDate = date('Y-m-d');
                }
            }
            $appointmentTimeInput = $this->request->getPost('appointment_time') ?: '09:00';
            // Convert time format from HH:MM to HH:MM:SS
            $appointmentTime = $appointmentTimeInput . ':00';
            
            // Create consultation record - use patients.patient_id (not admin_patients.id)
            // consultations table has foreign key to patients.patient_id
            if ($db->tableExists('consultations') && !empty($patientId) && !empty($doctorId) && !empty($appointmentDate) && !empty($appointmentTime)) {
                $consultationModel = new \App\Models\ConsultationModel();
                try {
                    $consultationData = [
                        'doctor_id' => $doctorId,
                        'patient_id' => $patientId, // Use patients.patient_id (from patients table)
                        'consultation_date' => $appointmentDate,
                        'consultation_time' => $appointmentTime,
                        'type' => 'upcoming',
                        'status' => 'approved',
                        'notes' => $this->request->getPost('purpose') ?: "Visit Type: {$visitType}",
                        'created_at' => date('Y-m-d H:i:s'),
                    ];
                    
                    log_message('info', "Attempting to create consultation with data: " . json_encode($consultationData));
                    
                    $inserted = $consultationModel->insert($consultationData);
                    
                    if ($inserted) {
                        $consultationId = $consultationModel->getInsertID();
                        log_message('info', "✅ Consultation created successfully! ID: {$consultationId}, patient_id: {$patientId}, doctor_id: {$doctorId}, date: {$appointmentDate}, time: {$appointmentTime}");
                    } else {
                        $errors = $consultationModel->errors();
                        log_message('error', "❌ Failed to create consultation. Errors: " . json_encode($errors));
                    }
                } catch (\Exception $e) {
                    log_message('error', "❌ Exception creating consultation: " . $e->getMessage());
                    log_message('error', "Stack trace: " . $e->getTraceAsString());
                }
            } else {
                $missing = [];
                if (empty($patientId)) $missing[] = 'patientId';
                if (empty($doctorId)) $missing[] = 'doctorId';
                if (empty($appointmentDate)) $missing[] = 'appointmentDate';
                if (empty($appointmentTime)) $missing[] = 'appointmentTime';
                log_message('warning', "Cannot create consultation - missing: " . implode(', ', $missing) . ". patientId: {$patientId}, doctorId: {$doctorId}, appointmentDate: {$appointmentDate}, appointmentTime: {$appointmentTime}");
            }
            
            // Create schedule entry in schedules table for doctor's schedule
            // This ensures the appointment appears immediately in the doctor's schedule
            if ($db->tableExists('schedules') && !empty($adminPatientId) && $doctorId && $appointmentDate) {
                // Get doctor's name from users table
                $doctorUser = $db->table('users')
                    ->where('id', $doctorId)
                    ->get()
                    ->getRowArray();
                
                $doctorName = 'Dr. ' . ($doctorUser['username'] ?? $doctorUser['name'] ?? 'Unknown');
                
                // Also try to get from doctors table if available
                // Note: doctors table has its own id, not related to users.id
                // We'll try to match by doctor_name if it matches the username
                if ($db->tableExists('doctors') && !empty($doctorUser['username'])) {
                    // Try to find doctor by matching doctor_name with username
                    $doctorFromTable = $db->table('doctors')
                        ->where('doctor_name', $doctorUser['username'])
                        ->orLike('doctor_name', $doctorUser['username'])
                        ->get()
                        ->getRowArray();
                    
                    if ($doctorFromTable && !empty($doctorFromTable['doctor_name'])) {
                        $doctorName = $doctorFromTable['doctor_name'];
                    }
                }
                
                // Check if schedule already exists for this patient and date
                $existingSchedule = $db->table('schedules')
                    ->where('patient_id', $adminPatientId)
                    ->where('date', $appointmentDate)
                    ->where('deleted_at IS NULL', null, false)
                    ->get()
                    ->getRowArray();
                
                if (!$existingSchedule) {
                    $scheduleModel = new \App\Models\ScheduleModel();
                    $scheduleModel->insert([
                        'patient_id' => $adminPatientId, // Use admin_patients.id
                        'date' => $appointmentDate,
                        'time' => $appointmentTime,
                        'doctor' => $doctorName,
                        'status' => 'confirmed', // Set as confirmed since it's from registration
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    
                    log_message('info', "Schedule created for patient {$adminPatientId} with doctor {$doctorName} on {$appointmentDate} at {$appointmentTime}");
                } else {
                    log_message('info', "Schedule already exists for patient {$adminPatientId} on {$appointmentDate} - skipping");
                }
            }
            
            // For Consultation, ensure patient record is updated with doctor_id and timestamp
            // This ensures the patient appears immediately in doctor's patient list
            if ($visitType === 'Consultation' && $doctorId) {
                $this->patientModel->update($patientId, [
                    'doctor_id' => $doctorId,
                    'updated_at' => date('Y-m-d H:i:s'), // Force update timestamp for immediate visibility
                ]);
                
                log_message('info', "Consultation patient {$patientId} immediately assigned to doctor {$doctorId} - should appear in doctor's patient list");
            }

            // Audit log
            if ($db->tableExists('audit_logs')) {
                $db->table('audit_logs')->insert([
                    'action' => 'patient_registration_with_doctor',
                    'user_id' => session()->get('user_id'),
                    'user_role' => 'receptionist',
                    'user_name' => session()->get('username') ?? session()->get('name') ?? 'Receptionist',
                    'description' => "Patient {$fullName} registered with visit type: {$visitType}. Assigned to doctor ID: {$doctorId}",
                    'related_id' => $patientId,
                    'related_type' => 'patient',
                    'metadata' => json_encode([
                        'patient_id' => $patientId,
                        'doctor_id' => $doctorId,
                        'visit_type' => $visitType,
                        'appointment_date' => $appointmentDate,
                    ]),
                    'ip_address' => $this->request->getIPAddress(),
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        return redirect()->to('/receptionist/patients')->with('success', 'Patient registered successfully.');
    }
    
    /**
     * Get doctor's available schedule dates (AJAX)
     */
    public function getDoctorScheduleDates()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request'])->setStatusCode(400);
        }
        
        $doctorId = $this->request->getGet('doctor_id');
        
        if (!$doctorId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Doctor ID is required']);
        }
        
        $db = \Config\Database::connect();
        $today = date('Y-m-d');
        
        // Schedules are now only created through Admin > Schedule
        // No auto-generation - doctor must have schedule created by admin
        
        // Get doctor's schedule dates (from today onwards, next 30 days)
        $scheduleDates = [];
        $defaultSchedule = [
            ['start' => '9:00 AM', 'end' => '12:00 PM', 'start_time' => '09:00:00', 'end_time' => '12:00:00'],
            ['start' => '1:00 PM', 'end' => '4:00 PM', 'start_time' => '13:00:00', 'end_time' => '16:00:00']
        ];
        
        if ($db->tableExists('doctor_schedules')) {
            // Get doctor's actual schedules from database
            $schedules = $db->table('doctor_schedules')
                ->select('shift_date, start_time, end_time')
                ->where('doctor_id', $doctorId)
                ->where('shift_date >=', $today)
                ->where('shift_date <=', date('Y-m-d', strtotime('+60 days'))) // Get more days to have enough options
                ->where('status', 'active') // Only active schedules
                ->orderBy('shift_date', 'ASC')
                ->orderBy('start_time', 'ASC')
                ->get()
                ->getResultArray();
            
            // Group by date
            $datesGrouped = [];
            foreach ($schedules as $schedule) {
                $date = $schedule['shift_date'];
                $dayOfWeek = (int)date('w', strtotime($date));
                
                // Only include Monday-Friday (follow doctor's actual schedule)
                if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
                    if (!isset($datesGrouped[$date])) {
                        $datesGrouped[$date] = [];
                    }
                    $datesGrouped[$date][] = [
                        'start' => date('g:i A', strtotime($schedule['start_time'])),
                        'end' => date('g:i A', strtotime($schedule['end_time'])),
                        'start_time' => $schedule['start_time'],
                        'end_time' => $schedule['end_time']
                    ];
                }
            }
            
            // Only use dates that exist in doctor_schedules table (follow actual schedule)
            // Format for response - only include dates from doctor's schedule
            foreach ($datesGrouped as $date => $times) {
                $dayName = date('l', strtotime($date)); // Monday, Tuesday, etc.
                $monthDay = date('M d', strtotime($date)); // Jan 15, Feb 20, etc.
                $fullDate = date('Y-m-d', strtotime($date)); // 2025-01-15
                
                $scheduleDates[] = [
                    'date' => $fullDate,
                    'date_formatted' => date('M d, Y (l)', strtotime($date)),
                    'day_name' => $dayName,
                    'month_day' => $monthDay,
                    'display_text' => $dayName . ', ' . $monthDay, // "Monday, Jan 15"
                    'times' => $times,
                    'available_hours' => array_map(function($t) {
                        return $t['start'] . ' - ' . $t['end'];
                    }, $times)
                ];
            }
            
            // Sort by date
            usort($scheduleDates, function($a, $b) {
                return strcmp($a['date'], $b['date']);
            });
            
            // Limit to next 30 weekdays for display
            $scheduleDates = array_slice($scheduleDates, 0, 30);
        } else {
            // If table doesn't exist, return default schedule for next 30 weekdays
            $currentDate = new \DateTime($today);
            $endDate = new \DateTime(date('Y-m-d', strtotime('+30 days')));
            $count = 0;
            
            while ($currentDate <= $endDate && $count < 30) {
                $dayOfWeek = (int)$currentDate->format('w');
                
                if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
                    $dateStr = $currentDate->format('Y-m-d');
                    $dayName = date('l', strtotime($dateStr)); // Monday, Tuesday, etc.
                    $monthDay = date('M d', strtotime($dateStr)); // Jan 15, Feb 20, etc.
                    
                    $scheduleDates[] = [
                        'date' => $dateStr,
                        'date_formatted' => date('M d, Y (l)', strtotime($dateStr)),
                        'day_name' => $dayName,
                        'month_day' => $monthDay,
                        'display_text' => $dayName . ', ' . $monthDay, // "Monday, Jan 15"
                        'times' => $defaultSchedule,
                        'available_hours' => ['9:00 AM - 12:00 PM', '1:00 PM - 4:00 PM']
                    ];
                    $count++;
                }
                
                $currentDate->modify('+1 day');
            }
        }
        
        return $this->response->setJSON([
            'success' => true,
            'schedule_dates' => $scheduleDates,
            'message' => count($scheduleDates) > 0 ? '' : 'Doctor has no available schedule'
        ]);
    }
    
    /**
     * Get available appointment times for a doctor on a specific date (AJAX)
     */
    public function getAvailableTimes()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request'])->setStatusCode(400);
        }
        
        $doctorId = $this->request->getGet('doctor_id');
        $date = $this->request->getGet('date');
        
        if (!$doctorId || !$date) {
            return $this->response->setJSON(['success' => false, 'message' => 'Doctor ID and date are required']);
        }
        
        // Schedules are now only created through Admin > Schedule
        // No auto-generation - doctor must have schedule created by admin
        
        $db = \Config\Database::connect();
        
        // Get doctor's actual schedule for the selected date from database
        $schedules = [];
        if ($db->tableExists('doctor_schedules')) {
            $schedules = $db->table('doctor_schedules')
                ->select('start_time, end_time')
                ->where('doctor_id', $doctorId)
                ->where('shift_date', $date)
                ->where('status', 'active') // Only active schedules
                ->orderBy('start_time', 'ASC')
                ->get()
                ->getResultArray();
        }
        
        // If no schedule found in database, doctor is not available on this date
        if (empty($schedules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Doctor has no available schedule for this date. Please select a date from the doctor\'s schedule.',
                'times' => [],
                'available_hours' => []
            ]);
        }
        
        // Get already booked appointments/consultations
        $bookedTimes = [];
        $bookedDetails = []; // Store details of booked appointments
        
        if ($db->tableExists('appointments')) {
            $booked = $db->table('appointments')
                ->select('appointment_time, patient_id')
                ->where('doctor_id', $doctorId)
                ->where('appointment_date', $date)
                ->whereNotIn('status', ['cancelled', 'no_show'])
                ->get()
                ->getResultArray();
            
            foreach ($booked as $b) {
                $timeKey = substr($b['appointment_time'], 0, 5); // HH:MM format
                $bookedTimes[] = $timeKey;
                $bookedDetails[$timeKey] = [
                    'time' => $timeKey,
                    'type' => 'appointment',
                    'patient_id' => $b['patient_id'] ?? null
                ];
            }
        }
        
        if ($db->tableExists('consultations')) {
            $bookedConsultations = $db->table('consultations')
                ->select('consultation_time, patient_id')
                ->where('doctor_id', $doctorId)
                ->where('consultation_date', $date)
                ->whereNotIn('status', ['cancelled'])
                ->get()
                ->getResultArray();
            
            foreach ($bookedConsultations as $c) {
                $timeKey = substr($c['consultation_time'], 0, 5); // HH:MM format
                if (!in_array($timeKey, $bookedTimes)) {
                    $bookedTimes[] = $timeKey;
                }
                $bookedDetails[$timeKey] = [
                    'time' => $timeKey,
                    'type' => 'consultation',
                    'patient_id' => $c['patient_id'] ?? null
                ];
            }
        }
        
        // Also check schedules table for booked times
        if ($db->tableExists('schedules')) {
            $bookedSchedules = $db->table('schedules')
                ->select('time, patient_id')
                ->where('date', $date)
                ->where('deleted_at IS NULL', null, false)
                ->get()
                ->getResultArray();
            
            // Get doctor name to match with schedules
            $doctorUser = $db->table('users')
                ->where('id', $doctorId)
                ->get()
                ->getRowArray();
            
            $doctorName = 'Dr. ' . ($doctorUser['username'] ?? 'Unknown');
            
            // Try to get from doctors table
            if ($db->tableExists('doctors') && !empty($doctorUser['username'])) {
                $doctorFromTable = $db->table('doctors')
                    ->where('doctor_name', $doctorUser['username'])
                    ->orLike('doctor_name', $doctorUser['username'])
                    ->get()
                    ->getRowArray();
                
                if ($doctorFromTable && !empty($doctorFromTable['doctor_name'])) {
                    $doctorName = $doctorFromTable['doctor_name'];
                }
            }
            
            foreach ($bookedSchedules as $s) {
                // Check if schedule is for this doctor (by matching doctor name)
                if (stripos($s['doctor'] ?? '', $doctorName) !== false || 
                    stripos($s['doctor'] ?? '', $doctorUser['username'] ?? '') !== false) {
                    $timeKey = substr($s['time'], 0, 5); // HH:MM format
                    if (!in_array($timeKey, $bookedTimes)) {
                        $bookedTimes[] = $timeKey;
                    }
                    $bookedDetails[$timeKey] = [
                        'time' => $timeKey,
                        'type' => 'schedule',
                        'patient_id' => $s['patient_id'] ?? null
                    ];
                }
            }
        }
        
        // Generate available time slots (hourly intervals)
        $availableTimes = [];
        $availableHours = [];
        
        foreach ($schedules as $schedule) {
            $start = new \DateTime($date . ' ' . $schedule['start_time']);
            $end = new \DateTime($date . ' ' . $schedule['end_time']);
            
            // Handle end time that might be next day (e.g., 00:00:00)
            if ($end <= $start) {
                $end->modify('+1 day');
            }
            
            $current = clone $start;
            while ($current < $end) {
                $timeValue = $current->format('H:i');
                $timeLabel = $current->format('g:i A');
                
                // Check if this time slot is already booked
                if (!in_array($timeValue, $bookedTimes)) {
                    $availableTimes[] = [
                        'value' => $timeValue,
                        'label' => $timeLabel
                    ];
                }
                
                $availableHours[] = [
                    'start' => $schedule['start_time'],
                    'end' => $schedule['end_time']
                ];
                
                $current->modify('+1 hour');
            }
        }
        
        // Remove duplicate hours info
        $availableHours = array_unique($availableHours, SORT_REGULAR);
        
        // Get doctor schedule info for display
        $scheduleInfo = [];
        foreach ($schedules as $schedule) {
            $scheduleInfo[] = [
                'start' => date('g:i A', strtotime($schedule['start_time'])),
                'end' => date('g:i A', strtotime($schedule['end_time'])),
                'start_time' => $schedule['start_time'],
                'end_time' => $schedule['end_time']
            ];
        }
        
        return $this->response->setJSON([
            'success' => true,
            'times' => $availableTimes,
            'available_hours' => array_values($availableHours),
            'schedule_info' => $scheduleInfo,
            'booked_times' => array_values($bookedTimes),
            'booked_details' => $bookedDetails,
            'message' => count($availableTimes) > 0 ? '' : 'No available time slots for this date'
        ]);
    }

    public function show($id)
    {
        $patient = $this->patientModel
            ->select('patients.*, doctors.doctor_name, departments.department_name')
            ->join('doctors', 'doctors.user_id = patients.doctor_id', 'left') // Fix: use user_id instead of id
            ->join('departments', 'departments.id = patients.department_id', 'left')
            ->find($id);
        if (!$patient) {
            return redirect()->to('/receptionist/patients')->with('error', 'Patient not found.');
        }
        return view('Reception/patients/view', [
            'title' => 'Patient Details',
            'patient' => $patient,
        ]);
    }

    public function edit($id)
    {
        $patient = $this->patientModel->find($id);
        if (!$patient) {
            return redirect()->to('/receptionist/patients')->with('error', 'Patient not found.');
        }
        return view('Reception/patients/edit', [
            'title' => 'Edit Patient',
            'patient' => $patient,
            'departments' => $this->departmentModel->findAll(),
            'doctors' => $this->doctorModel->findAll(),
            'validation' => \Config\Services::validation()
        ]);
    }

    public function update($id)
    {
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[60]',
            'last_name'  => 'required|min_length[2]|max_length[60]',
            'gender'     => 'required|in_list[male,female,other,Male,Female,Other]',
            'civil_status' => 'permit_empty|in_list[Single,Married,Widowed,Divorced,Separated,Annulled,Other]',
            'date_of_birth' => 'permit_empty|valid_date',
            'type'       => 'required|in_list[In-Patient,Out-Patient]',
            'payment_type' => 'permit_empty|in_list[Cash,Insurance,Credit]',
            'blood_type' => 'permit_empty|in_list[A+,A-,B+,B-,AB+,AB-,O+,O-]'
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Additional validation for In-Patient: admission_date must not be in the past
        $patientType = $this->request->getPost('type');
        if ($patientType === 'In-Patient') {
            $admissionDate = $this->request->getPost('admission_date');
            if (!empty($admissionDate)) {
                $today = date('Y-m-d');
                if ($admissionDate < $today) {
                    return redirect()->back()->withInput()->with('errors', [
                        'admission_date' => 'Ang admission date ay hindi maaaring nasa nakaraan. Dapat ngayon o sa hinaharap.'
                    ]);
                }
            }
        }
        
        $first  = trim((string)$this->request->getPost('first_name'));
        $middle = trim((string)$this->request->getPost('middle_name'));
        $last   = trim((string)$this->request->getPost('last_name'));
        $ext    = trim((string)$this->request->getPost('extension_name'));
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

        // Simplified address handling - single field
        $address = trim((string)$this->request->getPost('address'));
        // Keep old address fields for backward compatibility but use simplified address if provided
        $addressStreet = trim((string)$this->request->getPost('address_street'));
        $addressBarangay = trim((string)$this->request->getPost('address_barangay'));
        $addressCity = trim((string)$this->request->getPost('address_city'));
        $addressProvince = trim((string)$this->request->getPost('address_province'));
        $composedAddress = $address ?: trim(implode(', ', array_filter([$addressStreet, $addressBarangay, $addressCity, $addressProvince])));

        $data = [
            'patient_id' => $id,
            'patient_reg_no' => $this->request->getPost('patient_reg_no') ?: null,
            'first_name' => $first,
            'middle_name' => $middle ?: null,
            'last_name' => $last,
            'full_name' => $fullName,
            'gender' => $this->request->getPost('gender'),
            'civil_status' => $this->request->getPost('civil_status') ?: null,
            'date_of_birth' => $dob ?: null,
            'age' => $age,
            'contact' => $this->request->getPost('contact') ?: null,
            'email' => $this->request->getPost('email') ?: null,
            'address_street' => $addressStreet ?: null,
            'address_barangay' => $addressBarangay ?: null,
            'address_city' => $addressCity ?: null,
            'address_province' => $addressProvince ?: null,
            'address' => $composedAddress ?: null,
            'nationality' => $this->request->getPost('nationality') ?: null,
            'religion' => $this->request->getPost('religion') ?: null,
            'type' => $this->request->getPost('type'),
            'doctor_id' => $this->request->getPost('doctor_id') ?: null,
            'department_id' => $this->request->getPost('department_id') ?: null,
            'purpose' => $this->request->getPost('purpose') ?: null,
            'admission_date' => $this->request->getPost('type') === 'In-Patient' ? $this->request->getPost('admission_date') : null,
            'room_number' => $this->request->getPost('type') === 'In-Patient' ? $this->request->getPost('room_number') : null,
            'emergency_name' => $this->request->getPost('emergency_name') ?: null,
            'emergency_relationship' => $this->request->getPost('emergency_relationship') ?: null,
            'emergency_contact' => $this->request->getPost('emergency_contact') ?: null,
            'emergency_address' => $this->request->getPost('emergency_address') ?: null,
            'blood_type' => $this->request->getPost('blood_type') ?: null,
            'allergies' => $this->request->getPost('allergies') ?: null,
            'existing_conditions' => $this->request->getPost('existing_conditions') ?: null,
            'current_medications' => $this->request->getPost('current_medications') ?: null,
            'past_surgeries' => $this->request->getPost('past_surgeries') ?: null,
            'family_history' => $this->request->getPost('family_history') ?: null,
            'insurance_provider' => $this->request->getPost('insurance_provider') ?: null,
            'insurance_number' => $this->request->getPost('insurance_number') ?: null,
            'philhealth_number' => $this->request->getPost('philhealth_number') ?: null,
            'billing_address' => $this->request->getPost('billing_address') ?: null,
            'payment_type' => $this->request->getPost('payment_type') ?: null,
            'registration_date' => $this->request->getPost('registration_date') ?: null,
            'registered_by' => $this->request->getPost('registered_by') ?: null,
            'signature_patient' => $this->request->getPost('signature_patient') ?: null,
            'signature_staff' => $this->request->getPost('signature_staff') ?: null,
            'date_signed' => $this->request->getPost('date_signed') ?: null,
        ];
        
        // Get current patient data to check if type is changing from In-Patient to Out-Patient
        $currentPatient = $this->patientModel->find($id);
        $newType = $this->request->getPost('type');
        
        // If changing from In-Patient to Out-Patient, automatically vacate room and bed
        if ($currentPatient && ($currentPatient['type'] ?? '') === 'In-Patient' && $newType === 'Out-Patient') {
            $db = \Config\Database::connect();
            
            // Vacate room if patient has a room assigned
            if (!empty($currentPatient['room_id'])) {
                $roomId = $currentPatient['room_id'];
                
                // Update room status
                if ($db->tableExists('rooms')) {
                    $db->table('rooms')
                        ->where('id', $roomId)
                        ->update([
                            'status' => 'Available',
                            'current_patient_id' => null,
                        ]);
                }
                
                // Vacate bed if patient has a bed assigned
                if ($db->tableExists('beds')) {
                    $db->table('beds')
                        ->where('room_id', $roomId)
                        ->where('current_patient_id', $id)
                        ->update([
                            'status' => 'available',
                            'current_patient_id' => null,
                        ]);
                }
            }
        }
        
        $this->patientModel->save($data);
        return redirect()->to('/receptionist/patients')->with('success', 'Patient updated successfully.');
    }

    /**
     * REMOVED: Auto-generation of doctor schedules
     * Schedules are now only created through Admin > Schedule > Create Schedule
     * Doctors and nurses must have schedules created by admin before they can be used
     */
    
    public function delete($id)
    {
        $patient = $this->patientModel->find($id);
        if (!$patient) {
            return redirect()->to('/receptionist/patients')->with('error', 'Patient not found.');
        }
        $this->patientModel->delete($id);
        return redirect()->to('/receptionist/patients')->with('success', 'Patient deleted successfully.');
    }
}
