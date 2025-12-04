<?php

namespace App\Controllers\Receptionist;

use App\Controllers\BaseController;
use App\Models\HMSPatientModel;
use App\Models\AdminPatientModel;
use App\Models\DoctorDirectoryModel;
use App\Models\ConsultationModel;
use App\Models\AppointmentModel;
use App\Models\DoctorScheduleModel;

class AssignDoctorController extends BaseController
{
    protected $patientModel;
    protected $adminPatientModel;
    protected $doctorModel;
    protected $consultationModel;
    protected $appointmentModel;
    protected $scheduleModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->patientModel = new HMSPatientModel();
        $this->adminPatientModel = new AdminPatientModel();
        $this->doctorModel = new DoctorDirectoryModel();
        $this->consultationModel = new ConsultationModel();
        $this->appointmentModel = new AppointmentModel();
        $this->scheduleModel = new DoctorScheduleModel();
    }

    /**
     * Get waiting list (patients without assigned doctor)
     */
    public function waitingList()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'receptionist') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $db = \Config\Database::connect();

        // Get patients from both tables without assigned doctor
        $waitingPatients = [];

        // From patients table (HMSPatientModel)
        // Get patients without assigned doctor AND with visit_type that requires doctor assignment
        // OR patients with Emergency visit_type that need triage
        if ($db->tableExists('patients')) {
            $patients = $this->patientModel
                ->groupStart()
                    ->groupStart()
                        ->where('doctor_id IS NULL')
                        ->orWhere('doctor_id', 0)
                    ->groupEnd()
                    ->groupStart()
                        ->where('visit_type', 'Consultation')
                        ->orWhere('visit_type', 'Check-up')
                        ->orWhere('visit_type', 'Follow-up')
                        ->orWhere('visit_type', 'Emergency')
                        ->orWhere('visit_type IS NULL', null)
                    ->groupEnd()
                ->groupEnd()
                ->orderBy('created_at', 'DESC')
                ->findAll();

            foreach ($patients as $patient) {
                // Calculate age if date_of_birth exists
                $age = $patient['age'] ?? null;
                if (empty($age) && !empty($patient['date_of_birth'])) {
                    try {
                        $birth = new \DateTime($patient['date_of_birth']);
                        $today = new \DateTime();
                        $age = (int)$today->diff($birth)->y;
                    } catch (\Exception $e) {
                        $age = null;
                    }
                }

                $waitingPatients[] = [
                    'id' => $patient['patient_id'],
                    'source' => 'patients',
                    'name' => $patient['full_name'] ?? ($patient['first_name'] . ' ' . $patient['last_name']),
                    'age' => $age,
                    'gender' => $patient['gender'] ?? 'N/A',
                    'visit_type' => $patient['visit_type'] ?? ($patient['type'] ?? 'Out-Patient'),
                    'triage_status' => $patient['triage_status'] ?? null,
                    'reason' => $patient['purpose'] ?? 'General Consultation',
                    'registration_date' => $patient['created_at'] ?? $patient['registration_date'] ?? date('Y-m-d'),
                ];
            }
        }

        // From admin_patients table
        if ($db->tableExists('admin_patients')) {
            $adminPatients = $this->adminPatientModel
                ->where('doctor_id IS NULL')
                ->orWhere('doctor_id', 0)
                ->orderBy('created_at', 'DESC')
                ->findAll();

            foreach ($adminPatients as $patient) {
                // Calculate age
                $age = null;
                if (!empty($patient['birthdate'])) {
                    try {
                        $birth = new \DateTime($patient['birthdate']);
                        $today = new \DateTime();
                        $age = (int)$today->diff($birth)->y;
                    } catch (\Exception $e) {
                        $age = null;
                    }
                }

                $waitingPatients[] = [
                    'id' => $patient['id'],
                    'source' => 'admin_patients',
                    'name' => ($patient['firstname'] ?? '') . ' ' . ($patient['lastname'] ?? ''),
                    'age' => $age,
                    'gender' => $patient['gender'] ?? 'N/A',
                    'visit_type' => $patient['visit_type'] ?? 'Out-Patient',
                    'triage_status' => $patient['triage_status'] ?? null,
                    'reason' => 'General Consultation',
                    'registration_date' => $patient['created_at'] ?? date('Y-m-d'),
                ];
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'patients' => $waitingPatients
        ]);
    }

    /**
     * Get available doctors with schedules
     */
    public function getAvailableDoctors()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'receptionist') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $db = \Config\Database::connect();
        $date = $this->request->getGet('date') ?: date('Y-m-d');
        $time = $this->request->getGet('time'); // Optional time parameter

        // Get doctors from users table (primary source - used by consultations/appointments)
        $availableDoctors = [];
        if ($db->tableExists('users')) {
            $userDoctors = $db->table('users')
                ->select('users.id, users.username, users.email')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('roles.name', 'doctor')
                ->where('users.status', 'active')
                ->get()
                ->getResultArray();

            foreach ($userDoctors as $userDoctor) {
                // If time is provided, check if doctor is already booked at that time
                if (!empty($time)) {
                    $hasConflict = false;
                    
                    // Check for appointment conflict
                    if ($db->tableExists('appointments')) {
                        $appointmentConflict = $db->table('appointments')
                            ->where('doctor_id', $userDoctor['id'])
                            ->where('appointment_date', $date)
                            ->where('appointment_time', $time)
                            ->whereNotIn('status', ['cancelled', 'no_show'])
                            ->countAllResults();
                        
                        if ($appointmentConflict > 0) {
                            $hasConflict = true;
                        }
                    }
                    
                    // Check for consultation conflict
                    if (!$hasConflict && $db->tableExists('consultations')) {
                        $consultationConflict = $db->table('consultations')
                            ->where('doctor_id', $userDoctor['id'])
                            ->where('consultation_date', $date)
                            ->where('consultation_time', $time)
                            ->whereNotIn('status', ['cancelled'])
                            ->countAllResults();
                        
                        if ($consultationConflict > 0) {
                            $hasConflict = true;
                        }
                    }
                    
                    // Skip this doctor if they have a conflict at the requested time
                    if ($hasConflict) {
                        continue;
                    }
                }
                // Get doctor details from doctors table if exists (for specialization)
                $doctorDetails = null;
                if ($db->tableExists('doctors')) {
                    // Try to find matching doctor by name or create a mapping
                    $doctorDetails = $db->table('doctors')
                        ->where('doctor_name', $userDoctor['username'])
                        ->orWhere('doctor_name', $userDoctor['name'] ?? '')
                        ->get()
                        ->getRowArray();
                }

                // Get schedule for this doctor (doctor_schedules uses users.id)
                $schedules = [];
                if ($db->tableExists('doctor_schedules')) {
                    $schedules = $db->table('doctor_schedules')
                        ->where('doctor_id', $userDoctor['id'])
                        ->where('shift_date', $date)
                        ->where('status !=', 'cancelled')
                        ->orderBy('start_time', 'ASC')
                        ->get()
                        ->getResultArray();
                }

                // Count current appointments/consultations for capacity check
                $currentAppointments = 0;
                if ($db->tableExists('appointments')) {
                    $currentAppointments += $db->table('appointments')
                        ->where('doctor_id', $userDoctor['id'])
                        ->where('appointment_date', $date)
                        ->whereNotIn('status', ['cancelled', 'no_show'])
                        ->countAllResults();
                }
                
                if ($db->tableExists('consultations')) {
                    $currentAppointments += $db->table('consultations')
                        ->where('doctor_id', $userDoctor['id'])
                        ->where('consultation_date', $date)
                        ->whereNotIn('status', ['cancelled'])
                        ->countAllResults();
                }

                // Get queue number (next in line)
                $queueNumber = $currentAppointments + 1;

                $availableDoctors[] = [
                    'id' => $userDoctor['id'],
                    'name' => $userDoctor['username'] ?? 'Dr. ' . $userDoctor['id'],
                    'specialization' => $doctorDetails['specialization'] ?? 'General Practice',
                    'schedules' => $schedules,
                    'current_appointments' => $currentAppointments,
                    'queue_number' => $queueNumber,
                    'max_capacity' => 20, // Default max capacity per day
                ];
            }
        }

        // Also include doctors from doctors table (if not already in list)
        if ($db->tableExists('doctors')) {
            $doctors = $this->doctorModel->findAll();
            foreach ($doctors as $doctor) {
                // Check if this doctor ID exists in users table
                $existsInUsers = false;
                if ($db->tableExists('users')) {
                    $userDoctor = $db->table('users')
                        ->join('roles', 'roles.id = users.role_id', 'left')
                        ->where('users.id', $doctor['id'])
                        ->where('roles.name', 'doctor')
                        ->get()
                        ->getRowArray();
                    $existsInUsers = !empty($userDoctor);
                }

                // Only add if not already in list (from users table)
                $alreadyAdded = false;
                foreach ($availableDoctors as $doc) {
                    if ($doc['id'] == $doctor['id']) {
                        $alreadyAdded = true;
                        break;
                    }
                }

                if (!$alreadyAdded) {
                    // If time is provided, check if doctor is already booked at that time
                    if (!empty($time)) {
                        $hasConflict = false;
                        
                        // Check for appointment conflict
                        if ($db->tableExists('appointments')) {
                            $appointmentConflict = $db->table('appointments')
                                ->where('doctor_id', $doctor['id'])
                                ->where('appointment_date', $date)
                                ->where('appointment_time', $time)
                                ->whereNotIn('status', ['cancelled', 'no_show'])
                                ->countAllResults();
                            
                            if ($appointmentConflict > 0) {
                                $hasConflict = true;
                            }
                        }
                        
                        // Check for consultation conflict
                        if (!$hasConflict && $db->tableExists('consultations')) {
                            $consultationConflict = $db->table('consultations')
                                ->where('doctor_id', $doctor['id'])
                                ->where('consultation_date', $date)
                                ->where('consultation_time', $time)
                                ->whereNotIn('status', ['cancelled'])
                                ->countAllResults();
                            
                            if ($consultationConflict > 0) {
                                $hasConflict = true;
                            }
                        }
                        
                        // Skip this doctor if they have a conflict at the requested time
                        if ($hasConflict) {
                            continue;
                        }
                    }
                    
                    // Get schedule
                    $schedules = [];
                    if ($db->tableExists('doctor_schedules')) {
                        $schedules = $db->table('doctor_schedules')
                            ->where('doctor_id', $doctor['id'])
                            ->where('shift_date', $date)
                            ->where('status !=', 'cancelled')
                            ->orderBy('start_time', 'ASC')
                            ->get()
                            ->getResultArray();
                    }

                    $currentAppointments = 0;
                    if ($db->tableExists('appointments')) {
                        $currentAppointments = $db->table('appointments')
                            ->where('doctor_id', $doctor['id'])
                            ->where('appointment_date', $date)
                            ->whereNotIn('status', ['cancelled', 'no_show'])
                            ->countAllResults();
                    }

                    $availableDoctors[] = [
                        'id' => $doctor['id'],
                        'name' => $doctor['doctor_name'] ?? 'Dr. ' . $doctor['id'],
                        'specialization' => $doctor['specialization'] ?? 'General Practice',
                        'schedules' => $schedules,
                        'current_appointments' => $currentAppointments,
                        'queue_number' => $currentAppointments + 1,
                        'max_capacity' => 20,
                    ];
                }
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'doctors' => $availableDoctors,
            'date' => $date
        ]);
    }

    /**
     * Assign doctor to patient
     */
    public function assign()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'receptionist') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $patientId = $this->request->getPost('patient_id');
        $patientSource = $this->request->getPost('patient_source'); // 'patients' or 'admin_patients'
        $doctorId = $this->request->getPost('doctor_id');
        $visitType = $this->request->getPost('visit_type') ?: 'Out-Patient';
        $reason = $this->request->getPost('reason') ?: 'General Consultation';
        $appointmentDate = $this->request->getPost('appointment_date') ?: date('Y-m-d');
        $appointmentTime = $this->request->getPost('appointment_time') ?: date('H:i:s');
        $queueNumber = $this->request->getPost('queue_number');
        $room = $this->request->getPost('room') ?: null;
        $department = $this->request->getPost('department') ?: null;

        // Validation
        if (empty($patientId) || empty($doctorId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Patient ID and Doctor ID are required'
            ])->setStatusCode(400);
        }

        // Prevent assignment for Emergency cases - they must go through triage first
        if ($visitType === 'Emergency') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Emergency cases must go through Nurse Triage first. Please refer patient to triage.'
            ])->setStatusCode(400);
        }

        // Only allow assignment for Consultation, Check-up, or Follow-up
        if (!in_array($visitType, ['Consultation', 'Check-up', 'Follow-up'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Doctor assignment is only available for Consultation, Check-up, or Follow-up visits.'
            ])->setStatusCode(400);
        }

        $db = \Config\Database::connect();

        try {
            // Check if doctor schedule is full
            if ($db->tableExists('doctor_schedules')) {
                $schedule = $db->table('doctor_schedules')
                    ->where('doctor_id', $doctorId)
                    ->where('shift_date', $appointmentDate)
                    ->where('status !=', 'cancelled')
                    ->get()
                    ->getRowArray();

                if ($schedule) {
                    // Count appointments for this doctor on this date
                    $appointmentCount = 0;
                    if ($db->tableExists('appointments')) {
                        $appointmentCount = $db->table('appointments')
                            ->where('doctor_id', $doctorId)
                            ->where('appointment_date', $appointmentDate)
                            ->whereNotIn('status', ['cancelled', 'no_show'])
                            ->countAllResults();
                    }

                    // Check capacity (default 20 per day)
                    $maxCapacity = 20;
                    if ($appointmentCount >= $maxCapacity) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Doctor schedule is full for this date. Please select another date or doctor.'
                        ])->setStatusCode(400);
                    }
                }
            }

            // Get doctor name for logging
            $doctor = $this->doctorModel->find($doctorId);
            $doctorName = $doctor['doctor_name'] ?? 'Dr. ' . $doctorId;
            
            // Also check users table for doctor name
            if ($db->tableExists('users')) {
                $userDoctor = $db->table('users')
                    ->join('roles', 'roles.id = users.role_id', 'left')
                    ->where('users.id', $doctorId)
                    ->where('roles.name', 'doctor')
                    ->get()
                    ->getRowArray();
                if ($userDoctor) {
                    $doctorName = $userDoctor['username'] ?? $doctorName;
                }
            }

            // Update patient record
            $patientName = '';
            if ($patientSource === 'patients') {
                $patient = $this->patientModel->find($patientId);
                if (!$patient) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Patient not found'
                    ])->setStatusCode(404);
                }

                $patientName = $patient['full_name'] ?? ($patient['first_name'] . ' ' . $patient['last_name']);
                
                // Update patient - patients table uses users.id for doctor_id
                // Ensure updated_at is set to current timestamp so patient appears immediately in doctor dashboard
                $updateData = [
                    'doctor_id' => $doctorId, // This is users.id from the assignment
                    'updated_at' => date('Y-m-d H:i:s'), // Force update timestamp
                ];
                
                if ($department) {
                    $updateData['department_id'] = $department;
                }
                
                // Update via model
                $updateResult = $this->patientModel->update($patientId, $updateData);
                
                // Also update directly via database to ensure it's saved
                $dbUpdateResult = $db->table('patients')
                    ->where('patient_id', $patientId)
                    ->update($updateData);
                
                // Verify the update was successful
                $verifyPatient = $db->table('patients')
                    ->where('patient_id', $patientId)
                    ->get()
                    ->getRowArray();
                
                log_message('info', "Patient assignment - Patient ID: {$patientId}, Doctor ID: {$doctorId}, Update Result: " . ($updateResult ? 'success' : 'failed') . ", DB Update Result: " . ($dbUpdateResult ? 'success' : 'failed'));
                log_message('info', "Verified patient doctor_id: " . ($verifyPatient['doctor_id'] ?? 'NULL'));
            } else {
                // admin_patients - uses users.id (doctor user ID)
                $patient = $this->adminPatientModel->find($patientId);
                if (!$patient) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Patient not found'
                    ])->setStatusCode(404);
                }

                $patientName = ($patient['firstname'] ?? '') . ' ' . ($patient['lastname'] ?? '');

                // Update patient - admin_patients uses users.id
                // Ensure updated_at is set to current timestamp so patient appears immediately in doctor dashboard
                $updateData = [
                    'doctor_id' => $doctorId, // This should be users.id
                    'updated_at' => date('Y-m-d H:i:s'), // Force update timestamp
                ];
                
                $this->adminPatientModel->update($patientId, $updateData);
                
                // Also update directly via database to ensure it's saved
                $db->table('admin_patients')
                    ->where('id', $patientId)
                    ->update($updateData);
            }

            // Create consultation/appointment records
            // Note: consultations table uses admin_patients and users.id (doctor)
            // appointments table uses patients and users.id (doctor)
            $adminPatientId = $patientId;
            
            if ($patientSource === 'patients') {
                // Check if patient exists in admin_patients, if not create one
                $existingAdminPatient = $db->table('admin_patients')
                    ->where('firstname', $patient['first_name'] ?? '')
                    ->where('lastname', $patient['last_name'] ?? '')
                    ->get()
                    ->getRowArray();
                
                if ($existingAdminPatient) {
                    $adminPatientId = $existingAdminPatient['id'];
                    // Update doctor_id and visit_type in admin_patients (use users.id)
                    // Ensure updated_at is set so patient appears immediately in doctor dashboard
                    $db->table('admin_patients')
                        ->where('id', $adminPatientId)
                        ->update([
                            'doctor_id' => $doctorId,
                            'visit_type' => $patient['visit_type'] ?? $visitType ?? null,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                } else {
                    // Create new admin_patient record
                    $adminPatientData = [
                        'firstname' => $patient['first_name'] ?? explode(' ', $patient['full_name'] ?? 'Unknown')[0] ?? 'Unknown',
                        'lastname' => $patient['last_name'] ?? (count(explode(' ', $patient['full_name'] ?? '')) > 1 ? explode(' ', $patient['full_name'], 2)[1] : ''),
                        'birthdate' => $patient['date_of_birth'] ?? null,
                        'gender' => strtolower($patient['gender'] ?? 'other'),
                        'contact' => $patient['contact'] ?? null,
                        'address' => $patient['address'] ?? null,
                        'doctor_id' => $doctorId, // users.id
                        'visit_type' => $patient['visit_type'] ?? $visitType ?? null, // Include visit_type
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                    $db->table('admin_patients')->insert($adminPatientData);
                    $adminPatientId = $db->insertID();
                }
            }
            
            // Create consultation record (uses admin_patients.id and users.id)
            if ($db->tableExists('consultations')) {
                $consultationData = [
                    'doctor_id' => $doctorId, // users.id
                    'patient_id' => $adminPatientId, // admin_patients.id
                    'consultation_date' => $appointmentDate,
                    'consultation_time' => $appointmentTime,
                    'type' => 'upcoming',
                    'status' => 'approved',
                    'notes' => 'Assigned by Receptionist. Reason: ' . $reason . '. Queue #' . ($queueNumber ?? 'N/A'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                $this->consultationModel->insert($consultationData);
            }

            // Create appointment record (appointments table uses patients.patient_id and users.id)
            if ($db->tableExists('appointments') && $patientSource === 'patients') {
                $appointmentData = [
                    'patient_id' => $patientId, // patients.patient_id
                    'doctor_id' => $doctorId, // users.id (must be from users table)
                    'appointment_date' => $appointmentDate,
                    'appointment_time' => $appointmentTime,
                    'appointment_type' => 'consultation',
                    'reason' => $reason,
                    'status' => 'scheduled',
                    'notes' => 'Assigned by Receptionist. Queue #' . ($queueNumber ?? 'N/A'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                $this->appointmentModel->insert($appointmentData);
            }

            // Create audit log
            if ($db->tableExists('audit_logs')) {
                $db->table('audit_logs')->insert([
                    'action' => 'doctor_assignment',
                    'user_id' => session()->get('user_id'),
                    'user_role' => 'receptionist',
                    'user_name' => session()->get('username') ?? session()->get('name') ?? 'Receptionist',
                    'description' => "Receptionist assigned Dr. {$doctorName} to Patient {$patientName}",
                    'related_id' => $patientId,
                    'related_type' => 'patient',
                    'metadata' => json_encode([
                        'patient_id' => $patientId,
                        'patient_source' => $patientSource,
                        'admin_patient_id' => $adminPatientId ?? null,
                        'doctor_id' => $doctorId,
                        'doctor_name' => $doctorName,
                        'visit_type' => $visitType,
                        'reason' => $reason,
                        'appointment_date' => $appointmentDate,
                        'appointment_time' => $appointmentTime,
                        'queue_number' => $queueNumber,
                        'room' => $room,
                        'department' => $department,
                    ]),
                    'ip_address' => $this->request->getIPAddress(),
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }

            // Notify nurse (optional)
            if ($db->tableExists('nurse_notifications')) {
                $db->table('nurse_notifications')->insert([
                    'nurse_id' => null, // Broadcast to all nurses
                    'type' => 'patient_assigned',
                    'title' => 'New Patient Assigned to Doctor',
                    'message' => "Patient {$patientName} has been assigned to {$doctorName}. Queue #{$queueNumber}",
                    'related_id' => $patientId,
                    'related_type' => 'patient',
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }

            // Notify doctor
            if ($db->tableExists('doctor_notifications')) {
                $db->table('doctor_notifications')->insert([
                    'doctor_id' => $doctorId,
                    'type' => 'new_patient_assigned',
                    'title' => 'New Patient Assigned',
                    'message' => "Patient {$patientName} has been assigned to you. Queue #{$queueNumber}. Reason: {$reason}",
                    'related_id' => $patientId,
                    'related_type' => 'patient',
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => "Doctor {$doctorName} has been successfully assigned to {$patientName}",
                'queue_number' => $queueNumber
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Assign Doctor Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to assign doctor: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}

