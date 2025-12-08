<?php

namespace App\Controllers\Doctor;

use App\Controllers\BaseController;
use App\Models\ConsultationModel;
use App\Models\AdminPatientModel;
use App\Models\HMSPatientModel;
use App\Models\PatientVitalModel;
use App\Models\ChargeModel;
use App\Models\BillingItemModel;
use App\Models\DoctorOrderModel;
use App\Models\OrderStatusLogModel;
use App\Models\DoctorModel;

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

        $doctorId = session()->get('user_id');
        $db = \Config\Database::connect();
        $scheduleModel = new \App\Models\DoctorScheduleModel();

        // Check if table exists
        if (!$db->tableExists('doctor_schedules')) {
            return redirect()->back()->with('error', 'Doctor schedules table does not exist. Please contact administrator to run migrations.');
        }

        // Generate 1-year schedule if not exists
        // COMMENTED OUT: Schedules should be created manually through the admin interface
        // $this->generateYearlySchedule($doctorId);

        // Get all schedules for the current year, grouped by month
        $currentYear = date('Y');
        $startDate = $currentYear . '-01-01';
        $endDate = $currentYear . '-12-31';

        $schedules = $db->table('doctor_schedules')
            ->where('doctor_id', $doctorId)
            ->where('shift_date >=', $startDate)
            ->where('shift_date <=', $endDate)
            ->where('status !=', 'cancelled')
            ->orderBy('shift_date', 'ASC')
            ->orderBy('start_time', 'ASC')
            ->get()
            ->getResultArray();

        // Organize schedules by month and day
        $scheduleByMonth = [];
        foreach ($schedules as $schedule) {
            $date = new \DateTime($schedule['shift_date']);
            $month = $date->format('F Y'); // e.g., "January 2025"
            $day = $date->format('d'); // Day number
            $dayName = $date->format('l'); // Day name (Monday, Tuesday, etc.)
            
            if (!isset($scheduleByMonth[$month])) {
                $scheduleByMonth[$month] = [];
            }
            
            if (!isset($scheduleByMonth[$month][$day])) {
                $scheduleByMonth[$month][$day] = [
                    'date' => $schedule['shift_date'],
                    'day_name' => $dayName,
                    'time_slots' => [],
                    'admitted_patients' => []
                ];
            }
            
            // Format time
            $startTime = date('g:i A', strtotime($schedule['start_time']));
            $endTime = date('g:i A', strtotime($schedule['end_time']));
            $timeSlot = $startTime . ' - ' . $endTime;
            
            // Avoid duplicates
            if (!in_array($timeSlot, $scheduleByMonth[$month][$day]['time_slots'])) {
                $scheduleByMonth[$month][$day]['time_slots'][] = $timeSlot;
            }
        }
        
        // Also create entries for all days in the year (to show admitted patients even on non-working days)
        $startDate = new \DateTime($currentYear . '-01-01');
        $endDate = new \DateTime($currentYear . '-12-31');
        $currentDate = clone $startDate;
        
        while ($currentDate <= $endDate) {
            $month = $currentDate->format('F Y');
            $day = $currentDate->format('d');
            $dayName = $currentDate->format('l');
            $dateStr = $currentDate->format('Y-m-d');
            
            if (!isset($scheduleByMonth[$month])) {
                $scheduleByMonth[$month] = [];
            }
            
            if (!isset($scheduleByMonth[$month][$day])) {
                $scheduleByMonth[$month][$day] = [
                    'date' => $dateStr,
                    'day_name' => $dayName,
                    'time_slots' => [],
                    'admitted_patients' => []
                ];
            }
            
            $currentDate->modify('+1 day');
        }

        // Get admitted patients for this doctor
        $admittedPatients = [];
        
        // Get from admissions table (admin_patients)
        if ($db->tableExists('admissions')) {
            $admittedFromAdmin = $db->table('admissions a')
                ->select('a.*, ap.firstname, ap.lastname, ap.contact, r.room_number, r.ward')
                ->join('admin_patients ap', 'ap.id = a.patient_id', 'left')
                ->join('rooms r', 'r.id = a.room_id', 'left')
                ->where('a.attending_physician_id', $doctorId)
                ->where('a.status', 'admitted')
                ->where('a.discharge_status', 'admitted')
                ->where('a.deleted_at', null)
                ->where('YEAR(a.admission_date)', $currentYear)
                ->orderBy('a.admission_date', 'ASC')
                ->get()
                ->getResultArray();
            
            foreach ($admittedFromAdmin as $adm) {
                if (empty($adm['admission_date'])) {
                    continue; // Skip if no admission date
                }
                
                try {
                    $admissionDate = new \DateTime($adm['admission_date']);
                    $month = $admissionDate->format('F Y');
                    $day = $admissionDate->format('d');
                    $admissionYear = (int)$admissionDate->format('Y');
                    
                    // Only show for current year
                    if ($admissionYear == $currentYear) {
                        // Ensure the day exists in scheduleByMonth (create if doesn't exist)
                        if (!isset($scheduleByMonth[$month])) {
                            $scheduleByMonth[$month] = [];
                        }
                        if (!isset($scheduleByMonth[$month][$day])) {
                            $scheduleByMonth[$month][$day] = [
                                'date' => $adm['admission_date'],
                                'day_name' => $admissionDate->format('l'),
                                'time_slots' => [],
                                'admitted_patients' => []
                            ];
                        }
                        
                        $scheduleByMonth[$month][$day]['admitted_patients'][] = [
                            'name' => ($adm['firstname'] ?? '') . ' ' . ($adm['lastname'] ?? ''),
                            'room_number' => $adm['room_number'] ?? 'N/A',
                            'ward' => $adm['ward'] ?? 'N/A',
                            'admission_date' => $adm['admission_date'],
                            'source' => 'admin',
                            'patient_id' => $adm['patient_id'],
                        ];
                    }
                } catch (\Exception $e) {
                    // Skip invalid dates
                    continue;
                }
            }
        }
        
        // Get In-Patients from patients table (receptionist-registered)
        if ($db->tableExists('patients')) {
            $inPatientsRaw = $db->table('patients p')
                ->select('p.*, r.room_number, r.ward, r.room_type')
                ->join('rooms r', 'r.id = p.room_id', 'left')
                ->where('p.doctor_id', $doctorId)
                ->where('p.type', 'In-Patient')
                ->where('p.doctor_id IS NOT NULL')
                ->where('p.doctor_id !=', 0)
                ->where('(YEAR(p.admission_date) = ' . $currentYear . ' OR YEAR(p.created_at) = ' . $currentYear . ' OR p.admission_date IS NULL)', null, false)
                ->orderBy('p.admission_date', 'ASC')
                ->orderBy('p.created_at', 'ASC')
                ->get()
                ->getResultArray();
            
            foreach ($inPatientsRaw as $patient) {
                // Use admission_date if available, otherwise use created_at
                $admissionDateStr = $patient['admission_date'] ?? $patient['created_at'] ?? date('Y-m-d');
                
                try {
                    $admissionDate = new \DateTime($admissionDateStr);
                    $admissionYear = (int)$admissionDate->format('Y');
                    
                    // Only include if in current year
                    if ($admissionYear == $currentYear) {
                        $month = $admissionDate->format('F Y');
                        $day = $admissionDate->format('d');
                        
                        // Ensure the day exists in scheduleByMonth (create if doesn't exist)
                        if (!isset($scheduleByMonth[$month])) {
                            $scheduleByMonth[$month] = [];
                        }
                        if (!isset($scheduleByMonth[$month][$day])) {
                            $scheduleByMonth[$month][$day] = [
                                'date' => $admissionDateStr,
                                'day_name' => $admissionDate->format('l'),
                                'time_slots' => [],
                                'admitted_patients' => []
                            ];
                        }
                        
                        $nameParts = [];
                        if (!empty($patient['first_name'])) $nameParts[] = $patient['first_name'];
                        if (!empty($patient['last_name'])) $nameParts[] = $patient['last_name'];
                        if (empty($nameParts) && !empty($patient['full_name'])) {
                            $parts = explode(' ', $patient['full_name'], 2);
                            $nameParts = [$parts[0] ?? '', $parts[1] ?? ''];
                        }
                        
                        $scheduleByMonth[$month][$day]['admitted_patients'][] = [
                            'name' => implode(' ', $nameParts),
                            'room_number' => $patient['room_number'] ?? 'N/A',
                            'ward' => $patient['ward'] ?? $patient['room_type'] ?? 'N/A',
                            'admission_date' => $admissionDateStr,
                            'source' => 'receptionist',
                            'patient_id' => $patient['patient_id'],
                        ];
                    }
                } catch (\Exception $e) {
                    // Skip invalid dates
                    continue;
                }
            }
        }
        
        // Get consultations/appointments for this doctor to show in schedule
        $consultations = [];
        if ($db->tableExists('consultations')) {
            // consultations.patient_id references patients.patient_id
            // Get all upcoming consultations for this doctor (not just current year, but also future dates)
            $startDate = $currentYear . '-01-01';
            $endDate = ($currentYear + 1) . '-12-31'; // Include next year too
            
            // First, let's check all consultations for this doctor (for debugging)
            $allConsultations = $db->table('consultations')
                ->where('doctor_id', $doctorId)
                ->where('deleted_at IS NULL', null, false)
                ->get()
                ->getResultArray();
            
            log_message('info', "🔍 Total consultations for doctor {$doctorId} (all statuses): " . count($allConsultations));
            foreach ($allConsultations as $c) {
                log_message('info', "  - Consultation ID: {$c['id']}, patient_id: {$c['patient_id']}, date: {$c['consultation_date']}, time: {$c['consultation_time']}, type: {$c['type']}, status: {$c['status']}");
            }
            
            // For schedule display, show all consultations in the date range
            // (Calendar view should show all scheduled consultations)
            $consultationsRaw = $db->table('consultations c')
                ->select('c.*, p.full_name, p.patient_id, p.first_name, p.last_name')
                ->join('patients p', 'p.patient_id = c.patient_id', 'left')
                ->where('c.doctor_id', $doctorId)
                ->where('c.type', 'upcoming')
                ->whereIn('c.status', ['approved', 'pending'])
                ->where('c.deleted_at IS NULL', null, false)
                ->where('c.consultation_date >=', $startDate)
                ->where('c.consultation_date <=', $endDate)
                ->orderBy('c.consultation_date', 'ASC')
                ->orderBy('c.consultation_time', 'ASC')
                ->get()
                ->getResultArray();
            
            log_message('info', "🔍 Querying consultations for doctor {$doctorId} from {$startDate} to {$endDate}");
            log_message('info', "✅ Found " . count($consultationsRaw) . " upcoming consultations for doctor {$doctorId}");
            
            // Debug: Log each consultation found
            foreach ($consultationsRaw as $idx => $consult) {
                log_message('info', "  Consultation #{$idx}: ID={$consult['id']}, patient_id={$consult['patient_id']}, date={$consult['consultation_date']}, time={$consult['consultation_time']}, status={$consult['status']}, patient_name=" . ($consult['full_name'] ?? 'N/A'));
            }
            
            foreach ($consultationsRaw as $consult) {
                $consultDate = $consult['consultation_date'];
                try {
                    $dateObj = new \DateTime($consultDate);
                    $month = $dateObj->format('F Y');
                    $day = $dateObj->format('d');
                    $consultYear = (int)$dateObj->format('Y');
                    
                    // Include consultations from current year and next year
                    if ($consultYear >= $currentYear) {
                        log_message('debug', "Processing consultation for date: {$consultDate}, month: {$month}, day: {$day}");
                        // Ensure the day exists in scheduleByMonth
                        if (!isset($scheduleByMonth[$month])) {
                            $scheduleByMonth[$month] = [];
                        }
                        if (!isset($scheduleByMonth[$month][$day])) {
                            $scheduleByMonth[$month][$day] = [
                                'date' => $consultDate,
                                'day_name' => $dateObj->format('l'),
                                'time_slots' => [],
                                'admitted_patients' => [],
                                'consultations' => []
                            ];
                        }
                        
                        // Initialize consultations array if not exists
                        if (!isset($scheduleByMonth[$month][$day]['consultations'])) {
                            $scheduleByMonth[$month][$day]['consultations'] = [];
                        }
                        
                        // Get patient name
                        $patientName = $consult['full_name'] ?? '';
                        if (empty($patientName)) {
                            $patientName = trim(($consult['first_name'] ?? '') . ' ' . ($consult['last_name'] ?? ''));
                        }
                        if (empty($patientName)) {
                            $patientName = 'Patient #' . ($consult['patient_id'] ?? 'Unknown');
                        }
                        
                        $scheduleByMonth[$month][$day]['consultations'][] = [
                            'id' => $consult['id'],
                            'patient_name' => $patientName,
                            'patient_id' => $consult['patient_id'],
                            'consultation_time' => $consult['consultation_time'],
                            'consultation_date' => $consultDate,
                            'status' => $consult['status'],
                            'notes' => $consult['notes'] ?? ''
                        ];
                    }
                } catch (\Exception $e) {
                    // Skip invalid dates
                    continue;
                }
            }
        }

        $data = [
            'title' => 'My Working Schedule',
            'scheduleByMonth' => $scheduleByMonth,
            'currentYear' => $currentYear
        ];

        return view('doctor/consultations/my_schedule', $data);
    }

    /**
     * Generate 1-year working schedule for doctor
     * Working days: Monday to Friday
     * Working hours: 9:00 AM - 12:00 PM and 1:00 PM - 4:00 PM
     * No schedule on Saturday and Sunday
     */
    private function generateYearlySchedule($doctorId)
    {
        $db = \Config\Database::connect();
        $scheduleModel = new \App\Models\DoctorScheduleModel();

        // Check if table exists
        if (!$db->tableExists('doctor_schedules')) {
            log_message('error', 'doctor_schedules table does not exist. Please run migrations.');
            return;
        }

        // Check if schedule already exists for this year
        $currentYear = date('Y');
        $startDate = $currentYear . '-01-01';
        $endDate = $currentYear . '-12-31';

        $existingCount = $db->table('doctor_schedules')
            ->where('doctor_id', $doctorId)
            ->where('shift_date >=', $startDate)
            ->where('shift_date <=', $endDate)
            ->countAllResults();

        // If schedule already exists, skip generation
        if ($existingCount > 0) {
            return;
        }

        // Generate schedule for the entire year
        $startDateObj = new \DateTime($startDate);
        $endDateObj = new \DateTime($endDate);
        $endDateObj->modify('+1 day'); // Include the end date

        $currentDate = clone $startDateObj;
        $schedulesToInsert = [];

        while ($currentDate < $endDateObj) {
            $dayOfWeek = (int)$currentDate->format('w'); // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
            
            // Only generate schedule for Monday (1) to Friday (5)
            if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
                $dateStr = $currentDate->format('Y-m-d');
                
                // Morning shift: 9:00 AM - 12:00 PM
                $schedulesToInsert[] = [
                    'doctor_id' => $doctorId,
                    'shift_date' => $dateStr,
                    'start_time' => '09:00:00',
                    'end_time' => '12:00:00',
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                // Afternoon shift: 1:00 PM - 4:00 PM
                $schedulesToInsert[] = [
                    'doctor_id' => $doctorId,
                    'shift_date' => $dateStr,
                    'start_time' => '13:00:00',
                    'end_time' => '16:00:00',
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            }
            
            $currentDate->modify('+1 day');
        }

        // Batch insert schedules (insert in chunks of 100 to avoid memory issues)
        if (!empty($schedulesToInsert)) {
            $chunks = array_chunk($schedulesToInsert, 100);
            foreach ($chunks as $chunk) {
                $db->table('doctor_schedules')->insertBatch($chunk);
            }
        }
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

        $consultationDate = $this->request->getPost('consultation_date');
        $consultationTime = $this->request->getPost('consultation_time');
        
        // Check if doctor already has consultation or appointment at this time
        $db = \Config\Database::connect();
        
        // Check for existing consultations
        $existingConsultation = $db->table('consultations')
            ->where('doctor_id', $doctorId)
            ->where('consultation_date', $consultationDate)
            ->where('consultation_time', $consultationTime)
            ->whereNotIn('status', ['cancelled'])
            ->get()
            ->getRowArray();
        
        if ($existingConsultation) {
            return redirect()->back()->withInput()->with('error', 'Mayroon nang consultation ang doctor sa oras na ito. Pumili ng ibang oras.');
        }
        
        // Check for existing appointments
        if ($db->tableExists('appointments')) {
            $existingAppointment = $db->table('appointments')
                ->where('doctor_id', $doctorId)
                ->where('appointment_date', $consultationDate)
                ->where('appointment_time', $consultationTime)
                ->whereNotIn('status', ['cancelled', 'no_show'])
                ->get()
                ->getRowArray();
            
            if ($existingAppointment) {
                return redirect()->back()->withInput()->with('error', 'Mayroon nang appointment ang doctor sa oras na ito. Pumili ng ibang oras.');
            }
        }

        $data = [
            'doctor_id' => $doctorId,
            'patient_id' => $this->request->getPost('patient_id'),
            'consultation_date' => $consultationDate,
            'consultation_time' => $consultationTime,
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

        $consultationDate = $this->request->getPost('consultation_date');
        $consultationTime = $this->request->getPost('consultation_time');
        
        // Check if doctor already has consultation or appointment at this time (excluding current consultation)
        $db = \Config\Database::connect();
        
        // Check for existing consultations (excluding current one)
        $existingConsultation = $db->table('consultations')
            ->where('doctor_id', $doctorId)
            ->where('consultation_date', $consultationDate)
            ->where('consultation_time', $consultationTime)
            ->where('id !=', $id)
            ->whereNotIn('status', ['cancelled'])
            ->get()
            ->getRowArray();
        
        if ($existingConsultation) {
            return redirect()->back()->withInput()->with('error', 'Mayroon nang consultation ang doctor sa oras na ito. Pumili ng ibang oras.');
        }
        
        // Check for existing appointments
        if ($db->tableExists('appointments')) {
            $existingAppointment = $db->table('appointments')
                ->where('doctor_id', $doctorId)
                ->where('appointment_date', $consultationDate)
                ->where('appointment_time', $consultationTime)
                ->whereNotIn('status', ['cancelled', 'no_show'])
                ->get()
                ->getRowArray();
            
            if ($existingAppointment) {
                return redirect()->back()->withInput()->with('error', 'Mayroon nang appointment ang doctor sa oras na ito. Pumili ng ibang oras.');
            }
        }

        $data = [
            'patient_id' => $this->request->getPost('patient_id'),
            'consultation_date' => $consultationDate,
            'consultation_time' => $consultationTime,
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
        $db = \Config\Database::connect();

        // Try to find the consultation (including soft-deleted ones to check if it exists)
        $consultation = $consultationModel->withDeleted()->find($id);
        
        if (!$consultation) {
            return redirect()->to('/doctor/consultations/my-schedule')->with('error', 'Consultation not found. It may have been already deleted.');
        }

        // Check if already soft-deleted
        if (!empty($consultation['deleted_at'])) {
            return redirect()->to('/doctor/consultations/my-schedule')->with('error', 'This consultation has already been deleted.');
        }

        // Verify this consultation belongs to this doctor
        if ($consultation['doctor_id'] != $doctorId) {
            return redirect()->to('/doctor/consultations/my-schedule')->with('error', 'You do not have access to this consultation.');
        }

        // Check if consultation has related records (for warning only, not blocking)
        $hasCharges = $db->table('charges')
            ->where('consultation_id', $id)
            ->where('deleted_at', null)
            ->countAllResults() > 0;

        $hasAdmission = $db->table('admissions')
            ->where('consultation_id', $id)
            ->where('status !=', 'cancelled')
            ->where('deleted_at', null)
            ->countAllResults() > 0;

        // Check for discharge orders through admission
        $hasDischargeOrder = false;
        if ($hasAdmission) {
            $admission = $db->table('admissions')
                ->where('consultation_id', $id)
                ->where('status !=', 'cancelled')
                ->where('deleted_at', null)
                ->get()
                ->getRowArray();
            
            if ($admission) {
                $hasDischargeOrder = $db->table('discharge_orders')
                    ->where('admission_id', $admission['id'])
                    ->countAllResults() > 0;
            }
        }

        // Proceed with deletion (allow deletion even with related records)
        if ($consultationModel->delete($id)) {
            $message = 'Consultation deleted successfully.';
            
            // Add warning if there were related records
            if ($hasCharges || $hasAdmission || $hasDischargeOrder) {
                $warnings = [];
                if ($hasCharges) $warnings[] = 'billing charges';
                if ($hasAdmission) $warnings[] = 'admission record';
                if ($hasDischargeOrder) $warnings[] = 'discharge order';
                
                $message .= ' Note: This consultation had associated ' . implode(', ', $warnings) . 
                           '. Related records may need manual review.';
            }
            
            return redirect()->to('/doctor/consultations/my-schedule')->with('success', $message);
        } else {
            return redirect()->back()->with('error', 'Failed to delete consultation. Please try again.');
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
                        'room_id' => $hmsPatient['room_id'] ?? null,
                        'room_number' => $hmsPatient['room_number'] ?? null,
                        'admission_date' => $hmsPatient['admission_date'] ?? null,
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

        // Check if there's an upcoming appointment and if the time has arrived
        if ($db->tableExists('consultations')) {
            // For receptionist patients, consultations use patients.patient_id
            // For admin patients, consultations use admin_patients.id
            $patientIdForConsultation = $patientId;
            
            // Try to find upcoming consultation where date and time have arrived
            $today = date('Y-m-d');
            $currentTime = date('H:i:s');
            $upcomingConsultation = $db->table('consultations')
                ->where('patient_id', $patientIdForConsultation)
                ->where('doctor_id', $doctorId)
                ->where('type', 'upcoming')
                ->whereIn('status', ['approved', 'pending'])
                ->groupStart()
                    ->where('consultation_date >', $today) // Future dates
                    ->orGroupStart()
                        ->where('consultation_date', $today) // Today's date
                        ->where('consultation_time <=', $currentTime) // Time has arrived
                    ->groupEnd()
                ->groupEnd()
                ->orderBy('consultation_date', 'ASC')
                ->orderBy('consultation_time', 'ASC')
                ->get()
                ->getRowArray();
            
            // If not found and patient is from admin_patients, try to find by matching name
            if (!$upcomingConsultation && $patientSourceActual === 'admin_patients') {
                // Consultations might be linked to patients table, so try finding by name match
                // This is a fallback for edge cases
            }
            
            if ($upcomingConsultation) {
                // Check if appointment time has arrived
                $appointmentDateTime = $upcomingConsultation['consultation_date'] . ' ' . $upcomingConsultation['consultation_time'];
                $appointmentTimestamp = strtotime($appointmentDateTime);
                $currentTimestamp = time();
                
                if ($currentTimestamp < $appointmentTimestamp) {
                    $formattedDate = date('M d, Y', strtotime($upcomingConsultation['consultation_date']));
                    $formattedTime = date('g:i A', strtotime($upcomingConsultation['consultation_time']));
                    return redirect()->to('/doctor/patients')->with('error', "Cannot start consultation yet. Appointment is scheduled for {$formattedDate} at {$formattedTime}.");
                }
            }
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

        // Get all available medicines from pharmacy for medication prescription
        $medicines = [];
        if ($db->tableExists('pharmacy')) {
            $medicines = $db->table('pharmacy')
                ->where('quantity >', 0)
                ->orderBy('item_name', 'ASC')
                ->get()
                ->getResultArray();
        }

        // Get all active nurses (for medication orders)
        $nurses = [];
        if ($db->tableExists('users') && $db->tableExists('roles')) {
            $nurses = $db->table('users')
                ->select('users.id, users.username')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('LOWER(roles.name)', 'nurse')
                ->where('users.status', 'active')
                ->orderBy('users.username', 'ASC')
                ->get()
                ->getResultArray();
        }

        // Get active lab tests grouped by category (for lab test requests)
        $labTests = [];
        if ($db->tableExists('lab_tests')) {
            $labTestModel = new \App\Models\LabTestModel();
            $labTests = $labTestModel->getActiveTestsGroupedByCategory();
        }

        // Check if patient is already admitted
        $isAlreadyAdmitted = false;
        $admissionInfo = null;
        
        if ($patientSourceActual === 'patients') {
            // Check if In-Patient with room assignment
            if (($patient['type'] ?? '') === 'In-Patient' && !empty($patient['room_id'])) {
                $isAlreadyAdmitted = true;
                $admissionInfo = [
                    'room_number' => $patient['room_number'] ?? null,
                    'admission_date' => $patient['admission_date'] ?? null,
                ];
            }
        } else {
            // Check admissions table for admin_patients
            if ($db->tableExists('admissions')) {
                $admission = $db->table('admissions')
                    ->where('patient_id', $patient['id'])
                    ->where('status', 'admitted')
                    ->where('discharge_status', 'admitted')
                    ->where('deleted_at', null)
                    ->get()
                    ->getRowArray();
                
                if ($admission) {
                    $isAlreadyAdmitted = true;
                    $admissionInfo = [
                        'admission_id' => $admission['id'],
                        'room_number' => null,
                        'admission_date' => $admission['admission_date'] ?? null,
                    ];
                    
                    // Get room info if available
                    if (!empty($admission['room_id']) && $db->tableExists('rooms')) {
                        $room = $db->table('rooms')
                            ->where('id', $admission['room_id'])
                            ->get()
                            ->getRowArray();
                        if ($room) {
                            $admissionInfo['room_number'] = $room['room_number'] ?? null;
                        }
                    }
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
            'medicines' => $medicines,
            'nurses' => $nurses,
            'labTests' => $labTests,
            'is_already_admitted' => $isAlreadyAdmitted,
            'admission_info' => $admissionInfo,
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
            'notes' => 'permit_empty|max_length[2000]',
            'for_admission' => 'permit_empty|in_list[0,1]'
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

        // Check if doctor already has consultation or appointment at this time
        $consultationDate = $this->request->getPost('consultation_date');
        $consultationTime = $this->request->getPost('consultation_time');
        
        // Check for existing consultations
        $existingConsultation = $db->table('consultations')
            ->where('doctor_id', $doctorId)
            ->where('consultation_date', $consultationDate)
            ->where('consultation_time', $consultationTime)
            ->whereNotIn('status', ['cancelled'])
            ->get()
            ->getRowArray();
        
        if ($existingConsultation) {
            return redirect()->back()->withInput()->with('error', 'Mayroon nang consultation ang doctor sa oras na ito. Pumili ng ibang oras.');
        }
        
        // Check for existing appointments
        if ($db->tableExists('appointments')) {
            $existingAppointment = $db->table('appointments')
                ->where('doctor_id', $doctorId)
                ->where('appointment_date', $consultationDate)
                ->where('appointment_time', $consultationTime)
                ->whereNotIn('status', ['cancelled', 'no_show'])
                ->get()
                ->getRowArray();
            
            if ($existingAppointment) {
                return redirect()->back()->withInput()->with('error', 'Mayroon nang appointment ang doctor sa oras na ito. Pumili ng ibang oras.');
            }
        }

        $data = [
            'doctor_id' => $doctorId,
            'patient_id' => $adminPatientId, // Use admin_patients.id for consultations table
            'consultation_date' => $consultationDate,
            'consultation_time' => $consultationTime,
            'type' => 'completed',
            'status' => 'approved',
            'observations' => $this->request->getPost('observations'),
            'diagnosis' => $this->request->getPost('diagnosis'),
            'notes' => $this->request->getPost('notes') . ($queueNumber ? ' | Queue #' . $queueNumber : ''),
            'for_admission' => $this->request->getPost('for_admission') ? 1 : 0,
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
            
            // AUTO-GENERATE CHARGES: Create charge record and billing items
            $chargeModel = new ChargeModel();
            $billingItemModel = new BillingItemModel();
            
            if ($db->tableExists('charges') && $db->tableExists('billing_items')) {
                // Generate unique charge number
                $chargeNumber = $chargeModel->generateChargeNumber();
                
                // Initialize total amount
                $totalAmount = 0.00;
                $billingItems = [];
                
                // 1. Consultation Fee
                $consultationFee = 500.00; // Default consultation fee
                $billingItems[] = [
                    'item_type' => 'consultation',
                    'item_name' => 'Doctor Consultation',
                    'description' => 'Consultation on ' . date('Y-m-d'),
                    'quantity' => 1.00,
                    'unit_price' => $consultationFee,
                    'total_price' => $consultationFee,
                    'related_id' => $consultationId,
                    'related_type' => 'consultation',
                ];
                $totalAmount += $consultationFee;
                
                // 2. Lab Test Fees (if lab requests exist and are completed)
                if ($db->tableExists('lab_requests')) {
                    $labRequests = $db->table('lab_requests')
                        ->where('patient_id', $adminPatientId)
                        ->where('doctor_id', $doctorId)
                        ->where('status', 'completed')
                        ->where('requested_date', $this->request->getPost('consultation_date'))
                        ->get()
                        ->getResultArray();
                    
                    foreach ($labRequests as $labRequest) {
                        // Default lab test fee (can be made configurable)
                        $labTestFee = 300.00; // Default fee per test
                        $billingItems[] = [
                            'item_type' => 'lab_test',
                            'item_name' => $labRequest['test_name'] ?? $labRequest['test_type'] ?? 'Lab Test',
                            'description' => 'Lab Test: ' . ($labRequest['test_type'] ?? 'N/A'),
                            'quantity' => 1.00,
                            'unit_price' => $labTestFee,
                            'total_price' => $labTestFee,
                            'related_id' => $labRequest['id'],
                            'related_type' => 'lab_request',
                        ];
                        $totalAmount += $labTestFee;
                    }
                }
                
                // 3. Medication Charges (only if dispensed by pharmacy)
                if ($db->tableExists('doctor_orders') && $db->tableExists('pharmacy')) {
                    $medicationOrders = $db->table('doctor_orders')
                        ->where('patient_id', $adminPatientId)
                        ->where('doctor_id', $doctorId)
                        ->where('order_type', 'medication')
                        ->where('pharmacy_status', 'dispensed') // Only dispensed medications
                        ->get()
                        ->getResultArray();
                    
                    foreach ($medicationOrders as $order) {
                        // Get medicine price from pharmacy
                        $medicine = $db->table('pharmacy')
                            ->where('item_name', $order['medicine_name'])
                            ->get()
                            ->getRowArray();
                        
                        if ($medicine) {
                            $medicinePrice = (float)($medicine['price'] ?? 0.00);
                            $quantity = 1.00; // Default quantity
                            
                            $billingItems[] = [
                                'item_type' => 'medication',
                                'item_name' => $order['medicine_name'] ?? 'Medication',
                                'description' => 'Medication: ' . ($order['dosage'] ?? 'N/A'),
                                'quantity' => $quantity,
                                'unit_price' => $medicinePrice,
                                'total_price' => $medicinePrice * $quantity,
                                'related_id' => $order['id'],
                                'related_type' => 'doctor_order',
                            ];
                            $totalAmount += ($medicinePrice * $quantity);
                        }
                    }
                }
                
                // Create charge record
                $chargeData = [
                    'consultation_id' => $consultationId,
                    'patient_id' => $adminPatientId,
                    'charge_number' => $chargeNumber,
                    'total_amount' => $totalAmount,
                    'status' => 'pending',
                    'notes' => 'Auto-generated charge for consultation #' . $consultationId,
                ];
                
                if ($chargeModel->insert($chargeData)) {
                    $chargeId = $chargeModel->getInsertID();
                    
                    // Insert all billing items
                    foreach ($billingItems as $item) {
                        $item['charge_id'] = $chargeId;
                        $billingItemModel->insert($item);
                    }
                    
                    // Notify Accountant about new charge
                    if ($db->tableExists('accountant_notifications')) {
                        $db->table('accountant_notifications')->insert([
                            'type' => 'new_charge',
                            'title' => 'New Charge Generated',
                            'message' => 'New charge ' . $chargeNumber . ' generated for patient ID: ' . $adminPatientId . '. Total Amount: ₱' . number_format($totalAmount, 2),
                            'related_id' => $chargeId,
                            'related_type' => 'charge',
                            'is_read' => 0,
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }
            
            // Handle medication prescription if provided
            $prescribeMedication = $this->request->getPost('prescribe_medication');
            if ($prescribeMedication === 'yes') {
                $medicineName = $this->request->getPost('medicine_name');
                $dosage = $this->request->getPost('dosage');
                $frequency = $this->request->getPost('frequency');
                $duration = $this->request->getPost('duration');
                $purchaseLocation = $this->request->getPost('purchase_location');
                $nurseId = $this->request->getPost('nurse_id'); // Only required if hospital_pharmacy
                
                if ($medicineName && $dosage && $frequency && $duration && $purchaseLocation) {
                    // Only require nurse_id if hospital pharmacy
                    if ($purchaseLocation === 'hospital_pharmacy' && !$nurseId) {
                        return redirect()->back()->withInput()->with('error', 'Please assign a nurse for hospital pharmacy medication orders.');
                    }
                    
                    // Create medication order
                    $orderModel = new \App\Models\DoctorOrderModel();
                    $orderData = [
                        'patient_id' => $adminPatientId,
                        'doctor_id' => $doctorId,
                        'nurse_id' => $nurseId ?: null, // Only set if hospital pharmacy
                        'order_type' => 'medication',
                        'medicine_name' => $medicineName,
                        'dosage' => $dosage,
                        'frequency' => $frequency,
                        'duration' => $duration,
                        'order_description' => "Prescribed during consultation: {$medicineName} - {$dosage}, {$frequency}, for {$duration}",
                        'status' => $purchaseLocation === 'outside' ? 'completed' : 'pending', // Auto-complete if outside hospital
                        'purchase_location' => $purchaseLocation, // hospital_pharmacy or outside
                        'pharmacy_status' => $purchaseLocation === 'hospital_pharmacy' ? 'pending' : null, // Only set if hospital pharmacy
                        'completed_by' => $purchaseLocation === 'outside' ? $doctorId : null, // Doctor completed the prescription for outside purchase
                        'completed_at' => $purchaseLocation === 'outside' ? date('Y-m-d H:i:s') : null, // Auto-complete timestamp for outside
                    ];
                    
                    if ($orderModel->insert($orderData)) {
                        $orderId = $orderModel->getInsertID();
                        
                        // Create order status log entry
                        if ($db->tableExists('order_status_logs')) {
                            $logModel = new \App\Models\OrderStatusLogModel();
                            $logModel->insert([
                                'order_id' => $orderId,
                                'status' => $orderData['status'],
                                'changed_by' => $doctorId,
                                'notes' => $purchaseLocation === 'outside' 
                                    ? 'Prescription completed by doctor. Patient will purchase medication from outside pharmacy.' 
                                    : 'Medication order created. Sent to hospital pharmacy.',
                                'created_at' => date('Y-m-d H:i:s'),
                            ]);
                        }
                        
                        // If hospital pharmacy, notify pharmacy
                        if ($purchaseLocation === 'hospital_pharmacy' && $db->tableExists('pharmacy_notifications')) {
                            $db->table('pharmacy_notifications')->insert([
                                'order_id' => $orderId,
                                'patient_id' => $adminPatientId,
                                'medicine_name' => $medicineName,
                                'message' => "New medication order from Dr. " . (session()->get('username') ?? 'Doctor') . " for patient consultation.",
                                'is_read' => 0,
                                'created_at' => date('Y-m-d H:i:s'),
                            ]);
                        }
                        
                        // Notify nurse only if hospital pharmacy (nurse will administer)
                        if ($purchaseLocation === 'hospital_pharmacy' && $nurseId && $db->tableExists('nurse_notifications')) {
                            $db->table('nurse_notifications')->insert([
                                'nurse_id' => $nurseId,
                                'type' => 'new_doctor_order', // Use 'new_doctor_order' as per table enum
                                'title' => 'New Medication Order',
                                'message' => "Dr. " . (session()->get('username') ?? 'Doctor') . " has prescribed {$medicineName} for a patient. Patient will purchase from hospital pharmacy. Please wait for pharmacy to dispense before administering.",
                                'related_id' => $orderId, // Use related_id instead of order_id
                                'related_type' => 'doctor_order', // Use related_type to specify it's a doctor order
                                'is_read' => 0,
                                'created_at' => date('Y-m-d H:i:s'),
                            ]);
                        }
                    }
                }
            }
            
            // Handle lab test request if provided
            $requestLabTest = $this->request->getPost('request_lab_test');
            if ($requestLabTest === 'yes') {
                $labTestName = $this->request->getPost('lab_test_name');
                $labNurseId = $this->request->getPost('lab_nurse_id');
                $labTestRemarks = $this->request->getPost('lab_test_remarks');
                
                if (!empty($labTestName)) {
                    // IMPORTANT: Check test type FIRST before processing (matching admin logic)
                    $requiresSpecimen = true; // Default to true for safety
                    $testPrice = 300.00; // Default price
                    $testType = 'General';
                    
                    // Handle both empty string and null/not set for nurse_id
                    if ($labNurseId === '' || $labNurseId === null) {
                        $labNurseId = null;
                    }
                    
                    // Check test type FIRST before validation (matching admin logic)
                    if ($db->tableExists('lab_tests') && !empty($labTestName)) {
                        $labTest = $db->table('lab_tests')
                            ->where('test_name', $labTestName)
                            ->where('is_active', 1)
                            ->get()
                            ->getRowArray();
                        
                        if ($labTest) {
                            $testType = $labTest['test_type'] ?? 'General';
                            $testPrice = (float)($labTest['price'] ?? 300.00);
                            $specimenCategory = $labTest['specimen_category'] ?? 'with_specimen';
                            $requiresSpecimen = ($specimenCategory === 'with_specimen');
                            log_message('debug', 'Doctor Consultation - Lab Test: ' . $labTestName . ', Specimen Category: ' . $specimenCategory . ', Requires Specimen: ' . ($requiresSpecimen ? 'yes' : 'no'));
                        } else {
                            log_message('warning', 'Doctor Consultation - Lab test not found in database: ' . $labTestName);
                            // If test not found, default to requiring specimen for safety
                            $requiresSpecimen = true;
                        }
                    } else {
                        log_message('warning', 'Doctor Consultation - lab_tests table does not exist or test_name is empty. Test name: ' . ($labTestName ?? 'NULL'));
                        // If we can't check, default to requiring specimen for safety
                        $requiresSpecimen = true;
                    }
                    
                    log_message('debug', 'Doctor Consultation - Requires Specimen: ' . ($requiresSpecimen ? 'yes' : 'no') . ', Nurse ID provided: ' . (!empty($labNurseId) ? 'yes (' . $labNurseId . ')' : 'no'));
                    
                    // For without_specimen tests, explicitly unset nurse_id to avoid validation issues
                    if (!$requiresSpecimen && empty($labNurseId)) {
                        $labNurseId = null; // Explicitly set to null
                        // Also unset from POST data to prevent any validation issues
                        $postData = $this->request->getPost();
                        unset($postData['lab_nurse_id']);
                    }
                    
                    // Create lab service (matching admin logic)
                    $labServiceModel = new \App\Models\LabServiceModel();
                    $labServiceData = [
                        'patient_id' => $adminPatientId,
                        'doctor_id' => $doctorId,
                        'test_type' => $labTestName,
                        'result' => null,
                        'remarks' => $labTestRemarks ?? null,
                    ];
                    
                    // Only add nurse_id if provided (required for with_specimen, optional for without_specimen)
                    // Matching admin logic: only add if not empty
                    if (!empty($labNurseId)) {
                        $labServiceData['nurse_id'] = $labNurseId;
                    }
                    
                    log_message('debug', 'Doctor Consultation - Lab Service Data: ' . json_encode($labServiceData));
                    
                    // Skip validation to avoid nurse_id requirement issues (matching admin approach)
                    $labServiceModel->skipValidation(true);
                    $labServiceInserted = $labServiceModel->insert($labServiceData);
                    $labServiceModel->skipValidation(false);
                    
                    if ($labServiceInserted) {
                        $labServiceId = $labServiceModel->getInsertID();
                        
                        // Create lab request (matching admin logic)
                        $labRequestModel = new \App\Models\LabRequestModel();
                        $labRequestData = [
                            'patient_id' => $adminPatientId,
                            'doctor_id' => $doctorId,
                            'test_type' => $testType,
                            'test_name' => $labTestName,
                            'requested_by' => 'doctor',
                            'priority' => 'routine',
                            'instructions' => ($labTestRemarks ?? '') . ' | SPECIMEN_CATEGORY:' . ($requiresSpecimen ? 'with_specimen' : 'without_specimen'),
                            'status' => 'pending',
                            'requested_date' => date('Y-m-d'),
                            'payment_status' => 'pending', // Pending accountant approval - will be 'approved' then 'paid' by accountant
                        ];
                        
                        // Only add nurse_id if test requires specimen (matching admin logic)
                        if ($requiresSpecimen) {
                            if (!empty($labNurseId)) {
                                $labRequestData['nurse_id'] = $labNurseId;
                            }
                        }
                        // For without_specimen, don't add nurse_id at all
                        
                        log_message('debug', 'Doctor Consultation - Lab Request Data: ' . json_encode($labRequestData));
                        
                        // Skip validation to avoid nurse_id requirement issues for without_specimen tests
                        $labRequestModel->skipValidation(true);
                        $labRequestInserted = $labRequestModel->insert($labRequestData);
                        $labRequestModel->skipValidation(false);
                        
                        if ($labRequestInserted) {
                            $labRequestId = $labRequestModel->getInsertID();
                            
                            // Link lab service to lab request
                            $labServiceModel->update($labServiceId, [
                                'lab_request_id' => $labRequestId
                            ]);
                            
                            // Create charge for lab test
                            $chargeModel = new ChargeModel();
                            $billingItemModel = new BillingItemModel();
                            
                            if ($db->tableExists('charges') && $db->tableExists('billing_items')) {
                                $chargeNumber = $chargeModel->generateChargeNumber();
                                
                                // Create charge record (pending - waiting for accountant approval) - matching admin logic
                                $chargeNotes = $requiresSpecimen 
                                    ? 'Lab test payment: ' . $labTestName . ' - Requires accountant approval before proceeding to nurse for specimen collection'
                                    : 'Lab test payment: ' . $labTestName . ' - Requires accountant approval before proceeding to laboratory for testing (no specimen required)';
                                
                                $chargeData = [
                                    'patient_id' => $adminPatientId,
                                    'charge_number' => $chargeNumber,
                                    'total_amount' => $testPrice,
                                    'status' => 'pending', // Pending until accountant approves
                                    'notes' => $chargeNotes,
                                ];
                                
                                log_message('debug', 'Doctor Consultation - Creating charge for lab service: ' . json_encode($chargeData));
                                
                                if ($chargeModel->insert($chargeData)) {
                                    $chargeId = $chargeModel->getInsertID();
                                    
                                    // Create billing item
                                    $billingItemData = [
                                        'charge_id' => $chargeId,
                                        'item_type' => 'lab_test',
                                        'item_name' => $labTestName,
                                        'description' => 'Lab Test: ' . $testType . ' - ' . $labTestName,
                                        'quantity' => 1.00,
                                        'unit_price' => $testPrice,
                                        'total_price' => $testPrice,
                                        'related_id' => $labRequestId,
                                        'related_type' => 'lab_request',
                                    ];
                                    
                                    $billingItemModel->insert($billingItemData);
                                    
                                    // Update lab request with charge_id and store specimen_category for later reference (matching admin logic)
                                    $updateData = [
                                        'charge_id' => $chargeId,
                                        'payment_status' => 'pending', // Pending accountant approval
                                        'updated_at' => date('Y-m-d H:i:s')
                                    ];
                                    
                                    // Store specimen category in instructions for later reference (already done above, but ensure it's there)
                                    $existingInstructions = $labRequestData['instructions'] ?? '';
                                    if (strpos($existingInstructions, 'SPECIMEN_CATEGORY:') === false) {
                                        $specimenCategory = $requiresSpecimen ? 'with_specimen' : 'without_specimen';
                                        $updateData['instructions'] = $existingInstructions . ' | SPECIMEN_CATEGORY:' . $specimenCategory;
                                    }
                                    
                                    $labRequestModel->update($labRequestId, $updateData);
                                    
                                    // Notify Accountant about new payment request (needs approval) - matching admin logic
                                    if ($db->tableExists('accountant_notifications')) {
                                        $patient = $db->table('admin_patients')
                                            ->where('id', $adminPatientId)
                                            ->get()
                                            ->getRowArray();
                                        $patientName = ($patient ? ($patient['firstname'] ?? '') . ' ' . ($patient['lastname'] ?? '') : 'Patient');
                                        
                                        $notificationMessage = $requiresSpecimen
                                            ? 'Payment request for lab test: ' . $labTestName . ' - Patient: ' . $patientName . ' - Amount: ₱' . number_format($testPrice, 2) . ' - Please approve to proceed to nurse for specimen collection.'
                                            : 'Payment request for lab test: ' . $labTestName . ' - Patient: ' . $patientName . ' - Amount: ₱' . number_format($testPrice, 2) . ' - Please approve to proceed directly to laboratory for testing (no specimen required).';
                                        
                                        $db->table('accountant_notifications')->insert([
                                            'type' => 'lab_payment',
                                            'title' => 'Lab Test Payment Pending Approval',
                                            'message' => $notificationMessage,
                                            'related_id' => $chargeId,
                                            'related_type' => 'charge',
                                            'is_read' => 0,
                                            'created_at' => date('Y-m-d H:i:s'),
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            // Redirect based on admission status
            $forAdmission = $this->request->getPost('for_admission') ? 1 : 0;
            $medicationMessage = '';
            if ($prescribeMedication === 'yes') {
                $purchaseLocation = $this->request->getPost('purchase_location');
                if ($purchaseLocation === 'hospital_pharmacy') {
                    $medicationMessage = ' Medication prescription created and sent to hospital pharmacy.';
                } else {
                    $medicationMessage = ' Medication prescription created. Patient will purchase from outside pharmacy.';
                }
            }
            
            if ($forAdmission) {
                return redirect()->to('/doctor/orders?patient_id=' . $adminPatientId . '&consultation_id=' . $consultationId)->with('success', 'Consultation completed and saved successfully. Patient is marked for admission. A Nurse or Receptionist will process the admission and assign a room/bed.' . $medicationMessage);
            } else {
                return redirect()->to('/doctor/orders?patient_id=' . $adminPatientId)->with('success', 'Consultation completed and saved successfully. Charges generated.' . $medicationMessage);
            }
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to save consultation.');
        }
    }

    public function pediatricsList()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $doctorId = session()->get('user_id');
        $db = \Config\Database::connect();
        
        // Verify doctor is a pediatrician
        $isPediatricsDoctor = false;
        if ($db->tableExists('doctors')) {
            $doctor = $db->table('doctors')
                ->where('user_id', $doctorId)
                ->get()
                ->getRowArray();
            if ($doctor && strtolower(trim($doctor['specialization'] ?? '')) === 'pediatrics') {
                $isPediatricsDoctor = true;
            }
        }
        
        if (!$isPediatricsDoctor) {
            return redirect()->to('/doctor/dashboard')->with('error', 'This page is only available for pediatricians.');
        }

        $hmsPatientModel = new HMSPatientModel();
        $adminPatientModel = new AdminPatientModel();
        
        // Get all pediatric patients (age 0-17) assigned to this doctor from patients table
        $pediatricPatients = [];
        if ($db->tableExists('patients')) {
            $allPatients = $db->table('patients')
                ->where('doctor_id', $doctorId)
                ->where('doctor_id IS NOT NULL')
                ->where('doctor_id !=', 0)
                ->get()
                ->getResultArray();
            
            foreach ($allPatients as $patient) {
                // Calculate age from date_of_birth
                $age = null;
                if (!empty($patient['date_of_birth'])) {
                    $birthDate = new \DateTime($patient['date_of_birth']);
                    $today = new \DateTime();
                    $age = $today->diff($birthDate)->y;
                } elseif (!empty($patient['age'])) {
                    $age = (int)$patient['age'];
                }
                
                // Include patients aged 0-17
                if ($age !== null && $age >= 0 && $age <= 17) {
                    $patient['calculated_age'] = $age;
                    $patient['source'] = 'patients';
                    $pediatricPatients[] = $patient;
                }
            }
        }

        // Also get pediatric patients from admin_patients table
        if ($db->tableExists('admin_patients')) {
            $adminPatients = $adminPatientModel
                ->where('doctor_id', $doctorId)
                ->findAll();
            
            foreach ($adminPatients as $patient) {
                // Calculate age from birthdate
                $age = null;
                if (!empty($patient['birthdate'])) {
                    $birthDate = new \DateTime($patient['birthdate']);
                    $today = new \DateTime();
                    $age = $today->diff($birthDate)->y;
                }
                
                // Include patients aged 0-17
                if ($age !== null && $age >= 0 && $age <= 17) {
                    // Format to match patients table structure
                    $formattedPatient = [
                        'patient_id' => $patient['id'],
                        'id' => $patient['id'],
                        'full_name' => ($patient['firstname'] ?? '') . ' ' . ($patient['lastname'] ?? ''),
                        'first_name' => $patient['firstname'] ?? '',
                        'last_name' => $patient['lastname'] ?? '',
                        'date_of_birth' => $patient['birthdate'] ?? null,
                        'gender' => $patient['gender'] ?? null,
                        'contact' => $patient['contact'] ?? null,
                        'address' => $patient['address'] ?? null,
                        'calculated_age' => $age,
                        'source' => 'admin_patients',
                    ];
                    $pediatricPatients[] = $formattedPatient;
                }
            }
        }

        // Sort by patient ID
        usort($pediatricPatients, function($a, $b) {
            $idA = $a['patient_id'] ?? $a['id'] ?? 0;
            $idB = $b['patient_id'] ?? $b['id'] ?? 0;
            return $idA <=> $idB;
        });

        $data = [
            'title' => 'Pediatrics Consultations',
            'patients' => $pediatricPatients,
        ];

        return view('doctor/consultations/pediatrics_list', $data);
    }

    public function pediatricsConsult($patientId)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $doctorId = session()->get('user_id');
        $db = \Config\Database::connect();
        
        // Verify doctor is a pediatrician
        $isPediatricsDoctor = false;
        if ($db->tableExists('doctors')) {
            $doctor = $db->table('doctors')
                ->where('user_id', $doctorId)
                ->get()
                ->getRowArray();
            if ($doctor && strtolower(trim($doctor['specialization'] ?? '')) === 'pediatrics') {
                $isPediatricsDoctor = true;
            }
        }
        
        if (!$isPediatricsDoctor) {
            return redirect()->to('/doctor/dashboard')->with('error', 'This page is only available for pediatricians.');
        }

        $hmsPatientModel = new HMSPatientModel();
        $adminPatientModel = new AdminPatientModel();
        
        // Try to get patient from patients table first
        $patient = $hmsPatientModel->find($patientId);
        $patientSource = 'patients';
        
        // If not found, try admin_patients table
        if (!$patient && $db->tableExists('admin_patients')) {
            $adminPatient = $adminPatientModel->find($patientId);
            if ($adminPatient) {
                // Format to match patients table structure
                $patient = [
                    'patient_id' => $adminPatient['id'],
                    'id' => $adminPatient['id'],
                    'full_name' => ($adminPatient['firstname'] ?? '') . ' ' . ($adminPatient['lastname'] ?? ''),
                    'first_name' => $adminPatient['firstname'] ?? '',
                    'last_name' => $adminPatient['lastname'] ?? '',
                    'date_of_birth' => $adminPatient['birthdate'] ?? null,
                    'gender' => $adminPatient['gender'] ?? null,
                    'contact' => $adminPatient['contact'] ?? null,
                    'address' => $adminPatient['address'] ?? null,
                    'allergies' => null,
                ];
                $patientSource = 'admin_patients';
            }
        }
        
        if (!$patient) {
            return redirect()->to('/doctor/consultations/pediatrics')->with('error', 'Patient not found.');
        }

        // Calculate age
        $age = null;
        if (!empty($patient['date_of_birth'])) {
            $birthDate = new \DateTime($patient['date_of_birth']);
            $today = new \DateTime();
            $age = $today->diff($birthDate)->y;
        } elseif (!empty($patient['age'])) {
            $age = (int)$patient['age'];
        } elseif ($patientSource === 'admin_patients' && !empty($patient['birthdate'])) {
            $birthDate = new \DateTime($patient['birthdate']);
            $today = new \DateTime();
            $age = $today->diff($birthDate)->y;
        }

        // Verify patient is pediatric (0-17)
        if ($age === null || $age < 0 || $age > 17) {
            return redirect()->to('/doctor/consultations/pediatrics')->with('error', 'This patient is not a pediatric patient (must be 0-17 years old).');
        }

        // Get all pediatricians for assignment
        $doctorModel = new DoctorModel();
        $pediatricians = $doctorModel->getDoctorsBySpecialization('Pediatrics');

        // Get all available medicines from pharmacy for medication prescription
        $medicines = [];
        if ($db->tableExists('pharmacy')) {
            $medicines = $db->table('pharmacy')
                ->where('quantity >', 0)
                ->orderBy('item_name', 'ASC')
                ->get()
                ->getResultArray();
        }

        // Get all active nurses (for medication orders and lab tests)
        $nurses = [];
        if ($db->tableExists('users') && $db->tableExists('roles')) {
            $nurses = $db->table('users')
                ->select('users.id, users.username')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('LOWER(roles.name)', 'nurse')
                ->where('users.status', 'active')
                ->orderBy('users.username', 'ASC')
                ->get()
                ->getResultArray();
        }

        // Get active lab tests grouped by category (for lab test requests)
        $labTests = [];
        if ($db->tableExists('lab_tests')) {
            $labTestModel = new \App\Models\LabTestModel();
            $labTests = $labTestModel->getActiveTestsGroupedByCategory();
        }

        $data = [
            'title' => 'Pediatric Consultation',
            'patient' => $patient,
            'age' => $age,
            'pediatricians' => $pediatricians,
            'patient_source' => $patientSource,
            'medicines' => $medicines,
            'nurses' => $nurses,
            'labTests' => $labTests,
            'validation' => \Config\Services::validation()
        ];

        return view('doctor/consultations/pediatrics_consult', $data);
    }

    public function savePediatricsConsultation()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $doctorId = session()->get('user_id');
        $db = \Config\Database::connect();
        
        // Verify doctor is a pediatrician
        $isPediatricsDoctor = false;
        if ($db->tableExists('doctors')) {
            $doctor = $db->table('doctors')
                ->where('user_id', $doctorId)
                ->get()
                ->getRowArray();
            if ($doctor && strtolower(trim($doctor['specialization'] ?? '')) === 'pediatrics') {
                $isPediatricsDoctor = true;
            }
        }
        
        if (!$isPediatricsDoctor) {
            return redirect()->to('/doctor/dashboard')->with('error', 'This page is only available for pediatricians.');
        }

        $validation = $this->validate([
            'patient_id' => 'required|integer|greater_than[0]',
            'reason_for_consultation' => 'required|max_length[1000]',
            'symptoms' => 'permit_empty|max_length[2000]',
            'allergies' => 'permit_empty|max_length[500]',
            'temperature' => 'permit_empty|decimal',
            'weight' => 'permit_empty|decimal',
            'pulse_rate' => 'permit_empty|integer',
            'diagnosis_notes' => 'permit_empty|max_length[2000]',
            'follow_up_date' => 'permit_empty|valid_date',
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $patientId = $this->request->getPost('patient_id');
        $hmsPatientModel = new HMSPatientModel();
        $adminPatientModel = new AdminPatientModel();
        
        // Try to get patient from patients table first
        $patient = $hmsPatientModel->find($patientId);
        $patientSource = 'patients';
        $adminPatientId = null;
        
        // If not found, try admin_patients table
        if (!$patient && $db->tableExists('admin_patients')) {
            $adminPatient = $adminPatientModel->find($patientId);
            if ($adminPatient) {
                $patient = $adminPatient;
                $patientSource = 'admin_patients';
                $adminPatientId = $patientId;
            }
        }
        
        if (!$patient) {
            return redirect()->back()->withInput()->with('error', 'Patient not found.');
        }

        // For admin_patients, use adminPatientId; for patients, use patientId
        $effectivePatientId = ($patientSource === 'admin_patients') ? $adminPatientId : $patientId;

        // Save consultation data (use logged-in doctor as the pediatrician)
        $consultationData = [
            'doctor_id' => $doctorId,
            'patient_id' => $effectivePatientId,
            'consultation_date' => date('Y-m-d'),
            'consultation_time' => date('H:i:s'),
            'type' => 'completed',
            'status' => 'completed',
            'notes' => $this->request->getPost('diagnosis_notes'),
            'observations' => $this->request->getPost('symptoms'),
            'diagnosis' => $this->request->getPost('diagnosis_notes'),
        ];

        $consultationModel = new ConsultationModel();
        $consultationId = $consultationModel->insert($consultationData);

        // Save vital signs if provided
        if ($db->tableExists('patient_vitals')) {
            $vitalData = [
                'patient_id' => $effectivePatientId,
                'temperature' => $this->request->getPost('temperature'),
                'weight' => $this->request->getPost('weight'),
                'pulse_rate' => $this->request->getPost('pulse_rate'),
                'recorded_by' => $doctorId,
                'recorded_at' => date('Y-m-d H:i:s'),
            ];
            
            $vitalModel = new PatientVitalModel();
            $vitalModel->insert($vitalData);
        }

        // Update patient allergies if provided
        if ($this->request->getPost('allergies')) {
            if ($patientSource === 'admin_patients') {
                $adminPatientModel->update($patientId, [
                    'allergies' => $this->request->getPost('allergies')
                ]);
            } else {
                $hmsPatientModel->update($patientId, [
                    'allergies' => $this->request->getPost('allergies')
                ]);
            }
        }

        // Handle medication prescription if provided
        $prescribeMedication = $this->request->getPost('prescribe_medication');
        if ($prescribeMedication === 'yes') {
            $medicineName = $this->request->getPost('medicine_name');
            $dosage = $this->request->getPost('dosage');
            $frequency = $this->request->getPost('frequency');
            $duration = $this->request->getPost('duration');
            $purchaseLocation = $this->request->getPost('purchase_location');
            $nurseId = $this->request->getPost('nurse_id'); // Only required if hospital_pharmacy
            
            if ($medicineName && $dosage && $frequency && $duration && $purchaseLocation) {
                // Only require nurse_id if hospital pharmacy
                if ($purchaseLocation === 'hospital_pharmacy' && !$nurseId) {
                    return redirect()->back()->withInput()->with('error', 'Please assign a nurse for hospital pharmacy medication orders.');
                }
                
                // Create medication order
                $orderModel = new \App\Models\DoctorOrderModel();
                $orderData = [
                    'patient_id' => $effectivePatientId,
                    'doctor_id' => $doctorId,
                    'nurse_id' => $nurseId ?: null, // Only set if hospital pharmacy
                    'order_type' => 'medication',
                    'medicine_name' => $medicineName,
                    'dosage' => $dosage,
                    'frequency' => $frequency,
                    'duration' => $duration,
                    'order_description' => "Prescribed during pediatric consultation: {$medicineName} - {$dosage}, {$frequency}, for {$duration}",
                    'status' => $purchaseLocation === 'outside' ? 'completed' : 'pending', // Auto-complete if outside hospital
                    'purchase_location' => $purchaseLocation, // hospital_pharmacy or outside
                    'pharmacy_status' => $purchaseLocation === 'hospital_pharmacy' ? 'pending' : null, // Only set if hospital pharmacy
                    'completed_by' => $purchaseLocation === 'outside' ? $doctorId : null, // Doctor completed the prescription for outside purchase
                    'completed_at' => $purchaseLocation === 'outside' ? date('Y-m-d H:i:s') : null, // Auto-complete timestamp for outside
                ];
                
                if ($orderModel->insert($orderData)) {
                    $orderId = $orderModel->getInsertID();
                    
                    // Create order status log entry
                    if ($db->tableExists('order_status_logs')) {
                        $logModel = new \App\Models\OrderStatusLogModel();
                        $logModel->insert([
                            'order_id' => $orderId,
                            'status' => $orderData['status'],
                            'changed_by' => $doctorId,
                            'notes' => $purchaseLocation === 'outside' 
                                ? 'Prescription completed by doctor. Patient will purchase medication from outside pharmacy.' 
                                : 'Medication order created. Sent to hospital pharmacy.',
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                    
                    // If hospital pharmacy, notify pharmacy
                    if ($purchaseLocation === 'hospital_pharmacy' && $db->tableExists('pharmacy_notifications')) {
                        $db->table('pharmacy_notifications')->insert([
                            'order_id' => $orderId,
                            'patient_id' => $effectivePatientId,
                            'medicine_name' => $medicineName,
                            'message' => "New medication order from Dr. " . (session()->get('username') ?? 'Doctor') . " for pediatric patient consultation.",
                            'is_read' => 0,
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                    
                    // Notify nurse only if hospital pharmacy (nurse will administer)
                    if ($purchaseLocation === 'hospital_pharmacy' && $nurseId && $db->tableExists('nurse_notifications')) {
                        $db->table('nurse_notifications')->insert([
                            'nurse_id' => $nurseId,
                            'type' => 'new_doctor_order',
                            'title' => 'New Medication Order',
                            'message' => "Dr. " . (session()->get('username') ?? 'Doctor') . " has prescribed {$medicineName} for a pediatric patient. Patient will purchase from hospital pharmacy. Please wait for pharmacy to dispense before administering.",
                            'related_id' => $orderId,
                            'related_type' => 'doctor_order',
                            'is_read' => 0,
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }
        }
        
        // Handle lab test request if provided
        $requestLabTest = $this->request->getPost('request_lab_test');
        if ($requestLabTest === 'yes') {
            $labTestName = $this->request->getPost('lab_test_name');
            $labNurseId = $this->request->getPost('lab_nurse_id');
            $labTestRemarks = $this->request->getPost('lab_test_remarks');
            
            if (!empty($labTestName)) {
                // IMPORTANT: Check test type FIRST before processing
                $requiresSpecimen = true; // Default to true for safety
                $testPrice = 300.00; // Default price
                $testType = 'General';
                
                // Handle both empty string and null/not set for nurse_id
                if ($labNurseId === '' || $labNurseId === null) {
                    $labNurseId = null;
                }
                
                // Check test type FIRST before validation
                if ($db->tableExists('lab_tests') && !empty($labTestName)) {
                    $labTest = $db->table('lab_tests')
                        ->where('test_name', $labTestName)
                        ->where('is_active', 1)
                        ->get()
                        ->getRowArray();
                    
                    if ($labTest) {
                        $testType = $labTest['test_type'] ?? 'General';
                        $testPrice = (float)($labTest['price'] ?? 300.00);
                        $specimenCategory = $labTest['specimen_category'] ?? 'with_specimen';
                        $requiresSpecimen = ($specimenCategory === 'with_specimen');
                    } else {
                        // If test not found, default to requiring specimen for safety
                        $requiresSpecimen = true;
                    }
                } else {
                    // If we can't check, default to requiring specimen for safety
                    $requiresSpecimen = true;
                }
                
                // For without_specimen tests, explicitly unset nurse_id to avoid validation issues
                if (!$requiresSpecimen && empty($labNurseId)) {
                    $labNurseId = null; // Explicitly set to null
                }
                
                // Create lab service
                $labServiceModel = new \App\Models\LabServiceModel();
                $labServiceData = [
                    'patient_id' => $effectivePatientId,
                    'doctor_id' => $doctorId,
                    'test_type' => $labTestName,
                    'result' => null,
                    'remarks' => $labTestRemarks ?? null,
                ];
                
                // Only add nurse_id if provided (required for with_specimen, optional for without_specimen)
                if (!empty($labNurseId)) {
                    $labServiceData['nurse_id'] = $labNurseId;
                }
                
                // Skip validation to avoid nurse_id requirement issues
                $labServiceModel->skipValidation(true);
                $labServiceInserted = $labServiceModel->insert($labServiceData);
                $labServiceModel->skipValidation(false);
                
                if ($labServiceInserted) {
                    $labServiceId = $labServiceModel->getInsertID();
                    
                    // Create lab request
                    $labRequestModel = new \App\Models\LabRequestModel();
                    $labRequestData = [
                        'patient_id' => $effectivePatientId,
                        'doctor_id' => $doctorId,
                        'test_type' => $testType,
                        'test_name' => $labTestName,
                        'requested_by' => 'doctor',
                        'priority' => 'routine',
                        'instructions' => ($labTestRemarks ?? '') . ' | SPECIMEN_CATEGORY:' . ($requiresSpecimen ? 'with_specimen' : 'without_specimen'),
                        'status' => 'pending',
                        'requested_date' => date('Y-m-d'),
                        'payment_status' => 'pending', // Pending accountant approval
                    ];
                    
                    // Only add nurse_id if test requires specimen
                    if ($requiresSpecimen) {
                        if (!empty($labNurseId)) {
                            $labRequestData['nurse_id'] = $labNurseId;
                        }
                    }
                    // For without_specimen, don't add nurse_id at all
                    
                    // Skip validation to avoid nurse_id requirement issues for without_specimen tests
                    $labRequestModel->skipValidation(true);
                    $labRequestInserted = $labRequestModel->insert($labRequestData);
                    $labRequestModel->skipValidation(false);
                    
                    if ($labRequestInserted) {
                        $labRequestId = $labRequestModel->getInsertID();
                        
                        // Link lab service to lab request
                        $labServiceModel->update($labServiceId, [
                            'lab_request_id' => $labRequestId
                        ]);
                        
                        // Create charge for lab test
                        $chargeModel = new ChargeModel();
                        $billingItemModel = new BillingItemModel();
                        
                        if ($db->tableExists('charges') && $db->tableExists('billing_items')) {
                            $chargeNumber = $chargeModel->generateChargeNumber();
                            
                            // Create charge record (pending - waiting for accountant approval)
                            $chargeNotes = $requiresSpecimen 
                                ? 'Lab test payment: ' . $labTestName . ' - Requires accountant approval before proceeding to nurse for specimen collection'
                                : 'Lab test payment: ' . $labTestName . ' - Requires accountant approval before proceeding to laboratory for testing (no specimen required)';
                            
                            $chargeData = [
                                'patient_id' => $effectivePatientId,
                                'charge_number' => $chargeNumber,
                                'total_amount' => $testPrice,
                                'status' => 'pending', // Pending until accountant approves
                                'notes' => $chargeNotes,
                            ];
                            
                            if ($chargeModel->insert($chargeData)) {
                                $chargeId = $chargeModel->getInsertID();
                                
                                // Create billing item
                                $billingItemData = [
                                    'charge_id' => $chargeId,
                                    'item_type' => 'lab_test',
                                    'item_name' => $labTestName,
                                    'description' => 'Lab Test: ' . $testType . ' - ' . $labTestName,
                                    'quantity' => 1.00,
                                    'unit_price' => $testPrice,
                                    'total_price' => $testPrice,
                                    'related_id' => $labRequestId,
                                    'related_type' => 'lab_request',
                                ];
                                
                                $billingItemModel->insert($billingItemData);
                                
                                // Update lab request with charge_id
                                $updateData = [
                                    'charge_id' => $chargeId,
                                    'payment_status' => 'pending', // Pending accountant approval
                                    'updated_at' => date('Y-m-d H:i:s')
                                ];
                                
                                $labRequestModel->update($labRequestId, $updateData);
                                
                                // Notify Accountant about new payment request (needs approval)
                                if ($db->tableExists('accountant_notifications')) {
                                    $patientName = ($patientSource === 'admin_patients' && isset($patient['firstname']))
                                        ? ($patient['firstname'] ?? '') . ' ' . ($patient['lastname'] ?? '')
                                        : ($patient['full_name'] ?? ($patient['first_name'] ?? '') . ' ' . ($patient['last_name'] ?? '') ?? 'Patient');
                                    
                                    $notificationMessage = $requiresSpecimen
                                        ? 'Payment request for lab test: ' . $labTestName . ' - Patient: ' . $patientName . ' - Amount: ₱' . number_format($testPrice, 2) . ' - Please approve to proceed to nurse for specimen collection.'
                                        : 'Payment request for lab test: ' . $labTestName . ' - Patient: ' . $patientName . ' - Amount: ₱' . number_format($testPrice, 2) . ' - Please approve to proceed directly to laboratory for testing (no specimen required).';
                                    
                                    $db->table('accountant_notifications')->insert([
                                        'type' => 'lab_payment',
                                        'title' => 'Lab Test Payment Pending Approval',
                                        'message' => $notificationMessage,
                                        'related_id' => $chargeId,
                                        'related_type' => 'charge',
                                        'is_read' => 0,
                                        'created_at' => date('Y-m-d H:i:s'),
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        }

        $medicationMessage = '';
        if ($prescribeMedication === 'yes') {
            $purchaseLocation = $this->request->getPost('purchase_location');
            if ($purchaseLocation === 'hospital_pharmacy') {
                $medicationMessage = ' Medication prescription created and sent to hospital pharmacy.';
            } else {
                $medicationMessage = ' Medication prescription created. Patient will purchase from outside pharmacy.';
            }
        }

        if ($consultationId) {
            return redirect()->to('/doctor/consultations/pediatrics')->with('success', 'Pediatric consultation saved successfully.' . $medicationMessage);
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to save consultation.');
        }
    }
}
