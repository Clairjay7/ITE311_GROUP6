<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\AppointmentModel;
use App\Models\HMSPatientModel;
use App\Models\UserModel;
use App\Models\DoctorScheduleModel;
use App\Models\DepartmentModel;
use App\Models\DoctorModel;

class Appointment extends BaseController
{
    protected $appointmentModel;
    protected $patientModel;
    protected $userModel;
    protected $doctorScheduleModel;
    protected $departmentModel;
    protected $doctorModel;

    public function __construct()
    {
        $this->appointmentModel = new AppointmentModel();
        $this->patientModel = new HMSPatientModel();
        $this->userModel = new UserModel();
        $this->doctorScheduleModel = new DoctorScheduleModel();
        $this->departmentModel = new DepartmentModel();
        $this->doctorModel = new DoctorModel();
    }

    /**
     * Display appointment list
     */
    public function index()
    {
        // Check if user is logged in and has appropriate role
        $allowedRoles = ['admin', 'doctor', 'nurse', 'receptionist'];
        if (!in_array(session('role'), $allowedRoles)) {
            return redirect()->to('login');
        }

        $selectedMonth = $this->request->getGet('month'); // Format: Y-m
        $today = date('Y-m-d');
        
        // Get all appointments (default: show all - past and future)
        $db = \Config\Database::connect();
        $allAppointments = $db->table('appointments')
            ->select('appointments.*, 
                     p.first_name as patient_first_name,
                     p.middle_name as patient_middle_name,
                     p.last_name as patient_last_name,
                     p.full_name as patient_name,
                     p.contact as patient_contact,
                     u.username AS doctor_name,
                     d.specialization')
            ->join('patients p', 'p.patient_id = appointments.patient_id', 'left')
            ->join('users u', 'u.id = appointments.doctor_id', 'left')
            ->join('doctors d', 'd.user_id = appointments.doctor_id', 'left')
            ->whereNotIn('appointments.status', ['cancelled']) // Exclude cancelled
            ->orderBy('appointments.appointment_date', 'ASC')
            ->orderBy('appointments.appointment_time', 'ASC')
            ->get()
            ->getResultArray();
        
        // Group appointments by month
        $appointmentsByMonth = [];
        $allMonths = [];
        foreach ($allAppointments as $apt) {
            $month = date('Y-m', strtotime($apt['appointment_date']));
            $date = $apt['appointment_date'];
            
            if (!isset($appointmentsByMonth[$month])) {
                $appointmentsByMonth[$month] = [];
                $allMonths[] = $month;
            }
            if (!isset($appointmentsByMonth[$month][$date])) {
                $appointmentsByMonth[$month][$date] = [];
            }
            $appointmentsByMonth[$month][$date][] = $apt;
        }
        sort($allMonths);
        
        // Filter by selected month if provided
        $appointmentsByDate = [];
        $appointments = [];
        if ($selectedMonth && isset($appointmentsByMonth[$selectedMonth])) {
            $appointmentsByDate = $appointmentsByMonth[$selectedMonth];
            foreach ($appointmentsByDate as $dayAppointments) {
                $appointments = array_merge($appointments, $dayAppointments);
            }
        } else {
            // Show all appointments
            foreach ($appointmentsByMonth as $monthAppointments) {
                foreach ($monthAppointments as $dayAppointments) {
                    $appointments = array_merge($appointments, $dayAppointments);
                }
            }
        }
        
        // Calculate available slots per date
        // Get all doctor schedules to determine which dates have available slots
        $availableSlotsByDate = [];
        $today = date('Y-m-d');
        
        // Get all dates with doctor schedules (today and future)
        $futureSchedules = $db->table('doctor_schedules')
            ->select('shift_date, COUNT(DISTINCT doctor_id) as doctor_count')
            ->where('shift_date >=', $today)
            ->where('status', 'active')
            ->groupBy('shift_date')
            ->get()
            ->getResultArray();
        
        // Calculate available slots for each date with schedules
        foreach ($futureSchedules as $schedule) {
            $date = $schedule['shift_date'];
            
            // Count booked appointments for all doctors on this date
            $bookedSlots = $db->table('appointments')
                ->where('appointment_date', $date)
                ->whereNotIn('status', ['cancelled', 'no_show'])
                ->countAllResults();
            
            $totalSlots = $schedule['doctor_count'] * 10; // 10 slots per doctor (8am-5pm)
            $availableSlots = $totalSlots - $bookedSlots;
            
            if ($availableSlots > 0) {
                $availableSlotsByDate[$date] = $availableSlots;
            }
        }

        $data = [
            'title' => "Appointment Tracker",
            'active_menu' => 'appointments',
            'appointments' => $appointments,
            'appointmentsByMonth' => $appointmentsByMonth,
            'appointmentsByDate' => $appointmentsByDate,
            'allMonths' => $allMonths,
            'selectedMonth' => $selectedMonth,
            'availableSlotsByDate' => $availableSlotsByDate,
        ];

        return view('Reception/appointments/list', $data);
    }

    /**
     * Doctor-facing appointment list (defaults to today's appointments for logged-in doctor).
     */
    public function doctorToday()
    {
        // Only doctors can access this view
        if (session('role') !== 'doctor') {
            return redirect()->to('login');
        }

        $doctorId = (string) session('user_id');
        if ($doctorId === '') {
            return redirect()->to('/dashboard')->with('error', 'Unable to resolve doctor account.');
        }

        $filter = $this->request->getGet('filter') ?? 'today';
        $date   = $this->request->getGet('date');
        $today  = date('Y-m-d');

        if ($filter === 'all') {
            $appointments = $this->appointmentModel->getUnifiedList($doctorId, null);
        } elseif ($filter === 'date' && $date && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $appointments = $this->appointmentModel->getUnifiedList($doctorId, $date);
        } else {
            // Default: today's appointments for this doctor
            $appointments = $this->appointmentModel->getUnifiedList($doctorId, $today);
            $filter = 'today';
            $date = $today;
        }

        $data = [
            'title'          => "My Appointments",
            'active_menu'    => 'appointments',
            'appointments'   => $appointments,
            'currentFilter'  => $filter,
            'currentDate'    => $date ?? $today,
        ];

        return view('Roles/doctor/appointments/Appointmentlist', $data);
    }

    /**
     * Show book appointment form
     */
    public function book()
    {
        // Check if user is logged in and has appropriate role
        $allowedRoles = ['admin', 'nurse', 'receptionist'];
        if (!in_array(session('role'), $allowedRoles)) {
            return redirect()->to('login');
        }

        // Get only doctors who have schedules (active schedules for today or future dates)
        $allDoctors = $this->doctorModel->getDoctorsWithSchedules();
        
        // Check doctor availability for today (default) - will be updated via AJAX when date is selected
        $selectedDate = date('Y-m-d');
        $doctors = [];
        $db = \Config\Database::connect();
        foreach ($allDoctors as $doctor) {
            $doctorId = $doctor['id'];
            $isAvailable = false;
            
            // Check if doctor has an active schedule for the selected date
            if ($db->tableExists('doctor_schedules')) {
                $schedule = $db->table('doctor_schedules')
                    ->where('doctor_id', $doctorId)
                    ->where('shift_date', $selectedDate)
                    ->where('status', 'active')
                    ->get()
                    ->getRowArray();
                
                if ($schedule) {
                    $isAvailable = true;
                }
            }
            
            // Add availability flag
            $doctor['is_available'] = $isAvailable;
            $doctors[] = $doctor;
        }

        $departments = $this->departmentModel->findAll();

        $data = [
            'title' => 'Book Appointment',
            'active_menu' => 'appointments',
            'patients' => $this->patientModel->findAll(),
            'doctors' => $doctors,
            'departments' => $departments
        ];
        
        return view('Reception/appointments/book', $data);
    }

    /**
     * JSON: Get available schedule dates (distinct shift_date >= today)
     */
    public function getAvailableDates()
    {
        $allowedRoles = ['admin', 'nurse', 'receptionist'];
        if (!in_array(session('role'), $allowedRoles)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $doctorId = $this->request->getGet('doctor_id');
        if (!$doctorId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Doctor ID required'])->setStatusCode(400);
        }

        $today = date('Y-m-d');
        $db = \Config\Database::connect();
        
        // Get all available schedule dates
        $rows = $db->table('doctor_schedules')
            ->select('shift_date')
            ->where('doctor_id', $doctorId)
            ->where('shift_date >=', $today)
            ->where('status', 'active')
            ->distinct()
            ->orderBy('shift_date', 'ASC')
            ->get()->getResultArray();

        $allDates = array_values(array_unique(array_map(function($r){ return $r['shift_date']; }, $rows)));
        
        // Filter out dates that are fully booked (no available time slots)
        $availableDates = [];
        foreach ($allDates as $date) {
            // Get doctor's schedule for this date
            $schedules = $db->table('doctor_schedules')
                ->select('start_time, end_time')
                ->where('doctor_id', $doctorId)
                ->where('shift_date', $date)
                ->where('status', 'active')
                ->orderBy('start_time', 'ASC')
                ->get()->getResultArray();
            
            if (empty($schedules)) {
                continue; // Skip dates without schedule
            }
            
            // Calculate total available time slots for this date
            // Fixed slots from 8am to 5pm = 10 slots (8:00, 9:00, 10:00, 11:00, 12:00, 13:00, 14:00, 15:00, 16:00, 17:00)
            $totalSlots = 10; // Fixed 8am-5pm slots
            
            // Get booked appointments for this date
            $bookedCount = 0;
            if ($db->tableExists('appointments')) {
                $booked = $db->table('appointments')
                    ->where('doctor_id', $doctorId)
                    ->where('appointment_date', $date)
                    ->whereNotIn('status', ['cancelled', 'no_show', 'completed'])
                    ->countAllResults();
                $bookedCount = $booked;
            }
            
            // Only include dates that have available slots (less than 10 bookings)
            if ($bookedCount < $totalSlots) {
                $availableDates[] = $date;
            }
        }
        
        return $this->response->setJSON(['success' => true, 'dates' => $availableDates]);
    }

    /**
     * JSON: Get doctors by selected date
     */
    public function getDoctorsByDate()
    {
        $allowedRoles = ['admin', 'nurse', 'receptionist'];
        if (!in_array(session('role'), $allowedRoles)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $date = $this->request->getGet('date');
        if (!$date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid date'])->setStatusCode(400);
        }

        $db = \Config\Database::connect();
        $rows = $db->table('doctor_schedules ds')
            ->select("ds.doctor_id, COALESCE(u.username, ds.doctor_name) AS name, u.email")
            ->join('users u', 'u.id = ds.doctor_id', 'left')
            ->where('ds.shift_date', $date)
            ->where('ds.status !=', 'cancelled')
            ->groupBy('ds.doctor_id, name, u.email')
            ->orderBy('name', 'ASC')
            ->get()->getResultArray();

        return $this->response->setJSON(['success' => true, 'doctors' => $rows]);
    }

    /**
     * JSON: Get hourly time slots by doctor and date (expands shift into 1-hour slots)
     */
    public function getTimesByDoctorAndDate()
    {
        $allowedRoles = ['admin', 'nurse', 'receptionist'];
        if (!in_array(session('role'), $allowedRoles)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $date = $this->request->getGet('date');
        $doctorId = $this->request->getGet('doctor_id');
        if (!$date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || !$doctorId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid parameters'])->setStatusCode(400);
        }

        $db = \Config\Database::connect();

        // 1) Load doctor's shift blocks for the selected date
        $rows = $db->table('doctor_schedules')
            ->select('start_time, end_time')
            ->where('doctor_id', $doctorId)
            ->where('shift_date', $date)
            ->where('status !=', 'cancelled')
            ->orderBy('start_time', 'ASC')
            ->get()->getResultArray();

        // 2) Load already-booked appointment times for that doctor/date (exclude cancelled/no_show)
        $booked = $db->table('appointments')
            ->select('appointment_time')
            ->where('doctor_id', $doctorId)
            ->where('appointment_date', $date)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->get()->getResultArray();
        $bookedSet = [];
        foreach ($booked as $b) {
            // normalize to HH:MM:00
            $t = substr($b['appointment_time'], 0, 5) . ':00';
            $bookedSet[$t] = true;
        }

        // 3) Generate time slots from 8am to 5pm (8:00 AM to 5:00 PM)
        $slots = [];
        $tz = new \DateTimeZone('Asia/Manila');
        
        // Only generate slots if doctor has a schedule for this date
        if (!empty($rows)) {
            // Generate slots from 8:00 AM to 5:00 PM (17:00)
            $startTime = new \DateTime($date . ' 08:00:00', $tz);
            $endTime = new \DateTime($date . ' 17:00:00', $tz);
            $cursor = clone $startTime;
            
            while ($cursor <= $endTime) {
                $value = $cursor->format('H:i:00'); // submit value (24-hour format)
                if (!isset($bookedSet[$value])) {   // exclude already booked times
                    $slots[$value] = $cursor->format('g:i A'); // display label (12-hour format)
                }
                $cursor->modify('+1 hour');
            }
        }

        // 4) Build response array
        ksort($slots);
        $times = [];
        foreach ($slots as $value => $label) {
            $times[] = [ 'value' => $value, 'label' => $label ];
        }

        return $this->response->setJSON(['success' => true, 'times' => $times]);
    }
    
    /**
     * JSON: Get doctor's schedule (all available dates and times)
     */
    public function getDoctorSchedule()
    {
        $allowedRoles = ['admin', 'nurse', 'receptionist'];
        if (!in_array(session('role'), $allowedRoles)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $doctorId = $this->request->getGet('doctor_id');
        if (!$doctorId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Doctor ID required'])->setStatusCode(400);
        }

        $db = \Config\Database::connect();
        $today = date('Y-m-d');

        // Get all active schedules for this doctor (today and future)
        $schedules = [];
        if ($db->tableExists('doctor_schedules')) {
            $schedules = $db->table('doctor_schedules')
                ->select('shift_date, start_time, end_time, status')
                ->where('doctor_id', $doctorId)
                ->where('shift_date >=', $today)
                ->where('status', 'active')
                ->orderBy('shift_date', 'ASC')
                ->orderBy('start_time', 'ASC')
                ->get()
                ->getResultArray();
        }

        return $this->response->setJSON([
            'success' => true,
            'schedule' => $schedules
        ]);
    }
    
    /**
     * Display staff schedule - redirect to doctor schedule
     */
    public function schedule()
    {
        // Redirect to the proper doctor scheduling page
        return redirect()->to('/doctor/schedule');
    }

    /**
     * Create new appointment
     */
    public function create()
    {
        // Check if user is logged in and has appropriate role
        $allowedRoles = ['admin', 'nurse', 'receptionist'];
        if (!in_array(session('role'), $allowedRoles)) {
            // For web requests, redirect to login
            if (!$this->request->isAJAX()) {
                return redirect()->to('login')->with('error', 'Please login to access this feature');
            }
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized access']);
        }

        // Handle patient - prefer provided patient_id from autocomplete; fallback to name lookup/create
        $firstName = trim((string)$this->request->getPost('first_name') ?? '');
        $middleName = trim((string)$this->request->getPost('middle_name') ?? '');
        $surname = trim((string)$this->request->getPost('surname') ?? '');
        $patientId = (string) ($this->request->getPost('patient_id') ?? '');
        
        if ($patientId === '' && ($firstName === '' || $surname === '')) {
            if (!$this->request->isAJAX()) {
                return redirect()->back()->withInput()->with('error', 'Please enter first name and surname');
            }
            return $this->response->setJSON(['success' => false, 'message' => 'Please enter first name and surname']);
        }

        if ($patientId === '') {
            // Try to find existing patient by name first (case-insensitive on first and last)
            $existingPatient = $this->patientModel
                ->groupStart()
                    ->where('first_name', $firstName)
                    ->where('last_name', $surname)
                ->groupEnd()
                ->first();
            
            if ($existingPatient) {
                $patientId = $existingPatient['patient_id'];
                
                // Update existing patient with new information if provided
                $updateData = [];
                $contact = trim($this->request->getPost('contact') ?? '');
                $email = trim($this->request->getPost('email') ?? '');
                $dateOfBirth = $this->request->getPost('date_of_birth') ?? null;
                $gender = $this->request->getPost('gender') ?? null;
                $address = trim($this->request->getPost('address') ?? '');
                $bloodType = $this->request->getPost('blood_type') ?? null;
                $addressBarangay = trim($this->request->getPost('address_barangay') ?? '');
                $addressCity = trim($this->request->getPost('address_city') ?? '');
                $addressProvince = trim($this->request->getPost('address_province') ?? '');
                
                if (!empty($middleName)) {
                    $updateData['middle_name'] = $middleName;
                }
                if (!empty($contact)) {
                    $updateData['contact'] = $contact;
                    $updateData['phone'] = $contact;
                }
                if (!empty($email)) {
                    $updateData['email'] = $email;
                }
                if (!empty($dateOfBirth)) {
                    $updateData['date_of_birth'] = $dateOfBirth;
                }
                if (!empty($gender)) {
                    $updateData['gender'] = $gender;
                }
                if (!empty($address)) {
                    $updateData['address'] = $address;
                }
                if (!empty($bloodType)) {
                    $updateData['blood_type'] = $bloodType;
                }
                if (!empty($addressBarangay)) {
                    $updateData['address_barangay'] = $addressBarangay;
                }
                if (!empty($addressCity)) {
                    $updateData['address_city'] = $addressCity;
                }
                if (!empty($addressProvince)) {
                    $updateData['address_province'] = $addressProvince;
                }
                
                // Update patient if there's new information
                if (!empty($updateData)) {
                    $this->patientModel->skipValidation(true);
                    $this->patientModel->update($patientId, $updateData);
                    $this->patientModel->skipValidation(false);
                }
            } else {
                // Auto-create patient with provided information from appointment form
                $lastName = $surname;

                // Get form data
                $contact = trim($this->request->getPost('contact') ?? '');
                $email = trim($this->request->getPost('email') ?? '');
                $dateOfBirth = $this->request->getPost('date_of_birth') ?? null;
                $gender = $this->request->getPost('gender') ?? 'other';
                $age = $this->request->getPost('age') ?? null;
                $address = trim($this->request->getPost('address') ?? '');

                // Generate default values if not provided
                if (empty($email)) {
                    $randomNum = rand(1000, 9999);
                    $email = strtolower(str_replace(' ', '.', $firstName . '.' . $lastName)) . $randomNum . '@temp.com';
                }
                if (empty($contact)) {
                    $contact = '09' . rand(100000000, 999999999);
                }
                if (empty($dateOfBirth)) {
                    // Calculate date of birth from age if provided
                    if (!empty($age) && is_numeric($age)) {
                        $dateOfBirth = date('Y-m-d', strtotime('-' . $age . ' years'));
                    } else {
                        $dateOfBirth = '1990-01-01';
                    }
                }
                if (empty($address)) {
                    $address = 'Not provided';
                }

                $newPatientData = [
                    'first_name' => $firstName,
                    'middle_name' => $middleName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'phone' => $contact,
                    'contact' => $contact,
                    'date_of_birth' => $dateOfBirth,
                    'gender' => $gender,
                    'address' => $address,
                    'status' => 'active',
                    // Ensure auto-created records from booking are not listed as outpatients
                    'type' => 'walkin'
                ];

                $this->patientModel->skipValidation(true);
                $patientId = $this->patientModel->insert($newPatientData);
                $this->patientModel->skipValidation(false);

                if (empty($patientId)) {
                    $errors = $this->patientModel->errors();
                    $errorMessage = 'Failed to create patient record';
                    if (!empty($errors)) {
                        $errorMessage .= ': ' . implode(', ', $errors);
                    }

                    if (!$this->request->isAJAX()) {
                        return redirect()->back()->withInput()->with('error', $errorMessage);
                    }
                    return $this->response->setJSON(['success' => false, 'message' => $errorMessage]);
                }
            }
        }

        $rules = [
            'first_name' => 'required|min_length[2]',
            'surname' => 'required|min_length[2]',
            'middle_name' => 'permit_empty|min_length[1]',
            'contact' => 'required|min_length[10]',
            'doctor_id' => 'required',
            'appointment_date' => 'required|valid_date',
            'appointment_time' => 'required',
            'appointment_type' => 'permit_empty|in_list[consultation]',
            'reason' => 'required|min_length[5]',
            'email' => 'permit_empty|valid_email',
            'date_of_birth' => 'permit_empty|valid_date',
            'gender' => 'permit_empty|in_list[male,female,other]',
            'age' => 'permit_empty|integer|greater_than[0]|less_than[150]',
            'address' => 'permit_empty|max_length[255]',
            'blood_type' => 'permit_empty|in_list[A+,A-,B+,B-,AB+,AB-,O+,O-]',
            'address_barangay' => 'permit_empty|max_length[100]',
            'address_city' => 'permit_empty|max_length[100]',
            'address_province' => 'permit_empty|max_length[100]'
        ];

        if (!$this->validate($rules)) {
            // For web requests, redirect back with errors
            if (!$this->request->isAJAX()) {
                return redirect()->back()->withInput()->with('error', 'Please fill in all required fields correctly');
            }
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $appointmentType = $this->request->getPost('appointment_type');
        if (empty($appointmentType)) {
            $appointmentType = 'consultation'; // Default to consultation
        }
        
        $data = [
            'patient_id' => $patientId,
            'doctor_id' => (string)$this->request->getPost('doctor_id'),
            'appointment_date' => $this->request->getPost('appointment_date'),
            'appointment_time' => $this->request->getPost('appointment_time'),
            'appointment_type' => $appointmentType,
            'reason' => $this->request->getPost('reason'),
            'status' => 'scheduled'
        ];

        // Check for appointment conflicts
        if ($this->appointmentModel->checkAppointmentConflict($data['doctor_id'], $data['appointment_date'], $data['appointment_time'])) {
            // For web requests, redirect back with error
            if (!$this->request->isAJAX()) {
                return redirect()->back()->withInput()->with('error', 'Doctor already has an appointment at this time. Please choose a different time.');
            }
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Doctor already has an appointment at this time'
            ]);
        }

        $appointmentId = $this->appointmentModel->insert($data);

        if ($appointmentId) {
            // For web requests, redirect to appointment list with success
            if (!$this->request->isAJAX()) {
                return redirect()->to('appointments/list')->with('success', "Appointment booked successfully! Appointment ID: {$appointmentId}");
            }
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Appointment created successfully',
                'appointment_id' => $appointmentId
            ]);
        } else {
            // For web requests, redirect back with error
            if (!$this->request->isAJAX()) {
                return redirect()->back()->withInput()->with('error', 'Failed to create appointment. Please try again.');
            }
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create appointment',
                'errors' => $this->appointmentModel->errors()
            ]);
        }
    }

    /**
     * Get appointment details
     */
    public function show($id)
    {
        // Validate ID (accept alphanumeric IDs like APT-YYYYMMDD-####)
        if (empty($id) || !preg_match('/^[A-Za-z0-9\-]+$/', $id)) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid appointment id']);
        }
        // Check if user is logged in and has appropriate role
        $allowedRoles = ['admin', 'doctor', 'nurse', 'receptionist'];
        if (!in_array(session('role'), $allowedRoles)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized access']);
        }

        // Get appointment with patient and doctor details using correct table structure
        $appointment = $this->appointmentModel
            ->select('appointments.*, 
                     patients.first_name as patient_first_name, 
                     patients.last_name as patient_last_name,
                     patients.contact as patient_phone,
                     users.username as doctor_name, 
                     users.email as doctor_email')
            ->join('patients', 'patients.patient_id = appointments.patient_id', 'left')
            ->join('users', 'users.id = appointments.doctor_id', 'left')
            ->join('roles r', 'users.role_id = r.id', 'left')
            ->where('r.name', 'doctor')
            ->where('appointments.id', $id)
            ->first();

        if (!$appointment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Appointment not found'
            ]);
        }

        // Format the appointment data for the modal
        $appointment['patient_name'] = !empty($appointment['patient_first_name']) && !empty($appointment['patient_last_name']) 
            ? $appointment['patient_first_name'] . ' ' . $appointment['patient_last_name'] 
            : 'N/A';
            
        $appointment['doctor_name'] = !empty($appointment['doctor_name']) 
            ? 'Dr. ' . $appointment['doctor_name'] 
            : 'N/A';

        // Get related consultation with prescriptions and lab results if exists
        $db = \Config\Database::connect();
        $prescriptions = [];
        $labResults = [];
        
        if ($db->tableExists('consultations')) {
            // Try to find consultation by matching patient, doctor
            // First, try to find admin_patients record by patient_id or by name
            $adminPatient = null;
            
            // Try direct ID match first
            if (!empty($appointment['patient_id'])) {
                $adminPatient = $db->table('admin_patients')
                    ->where('id', $appointment['patient_id'])
                    ->get()
                    ->getRowArray();
            }
            
            // If not found, try to find by name and contact
            if (!$adminPatient && !empty($appointment['patient_first_name']) && !empty($appointment['patient_last_name'])) {
                $adminPatient = $db->table('admin_patients')
                    ->where('firstname', $appointment['patient_first_name'])
                    ->where('lastname', $appointment['patient_last_name'])
                    ->get()
                    ->getRowArray();
                
                // If still not found, try with contact
                if (!$adminPatient && !empty($appointment['patient_phone'])) {
                    $adminPatient = $db->table('admin_patients')
                        ->where('firstname', $appointment['patient_first_name'])
                        ->where('lastname', $appointment['patient_last_name'])
                        ->where('contact', $appointment['patient_phone'])
                        ->get()
                        ->getRowArray();
                }
            }
            
            $consultationPatientId = $adminPatient['id'] ?? null;
            
            // Find consultations for this patient and doctor (within last 30 days for follow-ups)
            if ($consultationPatientId && !empty($appointment['doctor_id'])) {
                // For follow-up appointments, search consultations within a date range
                $dateFrom = date('Y-m-d', strtotime('-30 days'));
                $dateTo = date('Y-m-d', strtotime('+1 day'));
                
                $consultations = $db->table('consultations')
                    ->where('patient_id', $consultationPatientId)
                    ->where('doctor_id', $appointment['doctor_id'])
                    ->where('consultation_date >=', $dateFrom)
                    ->where('consultation_date <=', $dateTo)
                    ->where('status', 'completed')
                    ->orderBy('consultation_date', 'DESC')
                    ->orderBy('consultation_time', 'DESC')
                    ->get()
                    ->getResultArray();
                
                // Get the most recent completed consultation
                $consultation = !empty($consultations) ? $consultations[0] : null;
                
                if ($consultation) {
                    // Get prescriptions
                    if (!empty($consultation['prescriptions'])) {
                        // Parse prescription details
                        $prescriptionDetails = [];
                        if (!empty($consultation['prescription_details'])) {
                            $prescriptionDetails = json_decode($consultation['prescription_details'], true) ?? [];
                        }
                        
                        // Get medicine names for prescriptions
                        $prescriptionIds = json_decode($consultation['prescriptions'], true) ?? [];
                        if (!empty($prescriptionIds) && $db->tableExists('pharmacy')) {
                            $medicines = $db->table('pharmacy')
                                ->whereIn('id', $prescriptionIds)
                                ->get()
                                ->getResultArray();
                            
                            foreach ($prescriptionDetails as $detail) {
                                $medicineId = $detail['medicine_id'] ?? null;
                                $medicine = array_filter($medicines, function($m) use ($medicineId) {
                                    return $m['id'] == $medicineId;
                                });
                                $medicine = !empty($medicine) ? reset($medicine) : null;
                                
                                if ($medicine) {
                                    $prescriptions[] = [
                                        'medicine_name' => $medicine['item_name'] ?? 'Unknown Medicine',
                                        'generic_name' => $medicine['generic_name'] ?? null,
                                        'dosage' => $detail['dosage'] ?? 'N/A',
                                        'frequency' => $detail['frequency'] ?? 'N/A',
                                        'duration' => $detail['duration'] ?? 'N/A',
                                        'when_to_take' => $detail['when_to_take'] ?? 'N/A',
                                        'instructions' => $detail['instructions'] ?? 'N/A'
                                    ];
                                }
                            }
                        }
                    }
                    
                    // Get lab results
                    if ($db->tableExists('lab_requests') && $db->tableExists('lab_results')) {
                        $labRequests = $db->table('lab_requests')
                            ->where('patient_id', $consultationPatientId)
                            ->where('doctor_id', $appointment['doctor_id'])
                            ->where('requested_date', $consultation['consultation_date'])
                            ->get()
                            ->getResultArray();
                        
                        foreach ($labRequests as $labRequest) {
                            $labResult = $db->table('lab_results')
                                ->where('lab_request_id', $labRequest['id'])
                                ->get()
                                ->getRowArray();
                            
                            if ($labResult) {
                                $labResults[] = [
                                    'test_name' => $labRequest['test_name'] ?? 'N/A',
                                    'test_type' => $labRequest['test_type'] ?? 'N/A',
                                    'result' => $labResult['result'] ?? 'N/A',
                                    'normal_range' => $labResult['normal_range'] ?? 'N/A',
                                    'status' => $labResult['status'] ?? 'N/A',
                                    'notes' => $labResult['notes'] ?? null,
                                    'result_file' => $labResult['result_file'] ?? null,
                                    'completed_at' => $labResult['created_at'] ?? null
                                ];
                            }
                        }
                    }
                }
            }
        }
        
        $appointment['prescriptions'] = $prescriptions;
        $appointment['lab_results'] = $labResults;

        return $this->response->setJSON([
            'success' => true,
            'appointment' => $appointment
        ]);
    }

    /**
     * Update appointment
     */
    public function update($id)
    {
        // Validate ID (accept alphanumeric IDs like APT-YYYYMMDD-####)
        if (empty($id) || !preg_match('/^[A-Za-z0-9\-]+$/', $id)) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid appointment id']);
        }
        // Check if user is logged in and has appropriate role
        $allowedRoles = ['admin', 'doctor', 'nurse', 'receptionist'];
        if (!in_array(session('role'), $allowedRoles)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized access']);
        }

        $appointment = $this->appointmentModel->find($id);
        if (!$appointment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Appointment not found'
            ]);
        }

        $rules = [
            'patient_id' => 'permit_empty|integer',
            'doctor_id' => 'permit_empty|integer',
            'appointment_date' => 'permit_empty|valid_date',
            'appointment_time' => 'permit_empty',
            'appointment_type' => 'permit_empty|in_list[consultation]',
            'status' => 'permit_empty|in_list[scheduled,confirmed,in_progress,completed,cancelled,no_show]',
            'reason' => 'permit_empty|string',
            'notes' => 'permit_empty|string'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [];
        $fields = ['patient_id', 'doctor_id', 'appointment_date', 'appointment_time', 'appointment_type', 'status', 'reason', 'notes'];
        
        foreach ($fields as $field) {
            $value = $this->request->getPost($field);
            if ($value !== null) {
                $data[$field] = $value;
            }
        }

        // Check for appointment conflicts if date/time/doctor changed
        if (isset($data['doctor_id']) || isset($data['appointment_date']) || isset($data['appointment_time'])) {
            $doctorId = $data['doctor_id'] ?? $appointment['doctor_id'];
            $date = $data['appointment_date'] ?? $appointment['appointment_date'];
            $time = $data['appointment_time'] ?? $appointment['appointment_time'];
            
            if ($this->appointmentModel->checkAppointmentConflict($doctorId, $date, $time, $id)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Doctor already has an appointment at this time'
                ]);
            }
        }

        if ($this->appointmentModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Appointment updated successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update appointment',
                'errors' => $this->appointmentModel->errors()
            ]);
        }
    }

    /**
     * Cancel appointment
     */
    public function cancel($id)
    {
        // Validate ID
        if (!is_numeric($id) || (int) $id <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid appointment id']);
        }
        // Check if user is logged in and has appropriate role
        $allowedRoles = ['admin', 'doctor', 'nurse', 'receptionist'];
        if (!in_array(session('role'), $allowedRoles)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized access']);
        }

        $notes = $this->request->getPost('notes');
        
        if ($this->appointmentModel->updateAppointmentStatus($id, 'cancelled', $notes)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Appointment cancelled successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to cancel appointment'
            ]);
        }
    }

    /**
     * Mark appointment as completed
     */
    public function complete($id)
    {
        // Validate ID
        if (!is_numeric($id) || (int) $id <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid appointment id']);
        }
        // Check if user is logged in and has appropriate role
        $allowedRoles = ['admin', 'doctor', 'nurse'];
        if (!in_array(session('role'), $allowedRoles)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized access']);
        }

        $notes = $this->request->getPost('notes');
        
        if ($this->appointmentModel->updateAppointmentStatus($id, 'completed', $notes)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Appointment marked as completed'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to complete appointment'
            ]);
        }
    }

    /**
     * Mark appointment as no-show
     */
    public function noShow($id)
    {
        // Validate ID
        if (!is_numeric($id) || (int) $id <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid appointment id']);
        }
        // Check if user is logged in and has appropriate role
        $allowedRoles = ['admin', 'nurse', 'receptionist'];
        if (!in_array(session('role'), $allowedRoles)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized access']);
        }

        $notes = $this->request->getPost('notes');
        
        if ($this->appointmentModel->updateAppointmentStatus($id, 'no_show', $notes)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Appointment marked as no-show'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to mark appointment as no-show'
            ]);
        }
    }

    /**
     * Get appointments by doctor
     */
    public function getByDoctor($doctorId)
    {
        // Validate ID
        if (!is_numeric($doctorId) || (int) $doctorId <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid doctor id']);
        }
        // Check if user is logged in and has appropriate role
        $allowedRoles = ['admin', 'doctor', 'nurse', 'receptionist'];
        if (!in_array(session('role'), $allowedRoles)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized access']);
        }

        $date = $this->request->getGet('date');
        $appointments = $this->appointmentModel->getAppointmentsByDoctor($doctorId, $date);

        return $this->response->setJSON([
            'success' => true,
            'appointments' => $appointments
        ]);
    }

    /**
     * Get appointments by patient
     */
    public function getByPatient($patientId)
    {
        // Validate ID
        if (!is_numeric($patientId) || (int) $patientId <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid patient id']);
        }
        // Check if user is logged in and has appropriate role
        $allowedRoles = ['admin', 'doctor', 'nurse', 'receptionist'];
        if (!in_array(session('role'), $allowedRoles)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized access']);
        }

        $appointments = $this->appointmentModel->getAppointmentsByPatient($patientId);

        return $this->response->setJSON([
            'success' => true,
            'appointments' => $appointments
        ]);
    }

    /**
     * Get today's appointments
     */
    public function getTodays()
    {
        // Check if user is logged in and has appropriate role
        $allowedRoles = ['admin', 'doctor', 'nurse', 'receptionist'];
        if (!in_array(session('role'), $allowedRoles)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized access']);
        }

        $appointments = $this->appointmentModel->getTodaysAppointments();

        return $this->response->setJSON([
            'success' => true,
            'appointments' => $appointments
        ]);
    }

    /**
     * Get upcoming appointments
     */
    public function getUpcoming()
    {
        // Check if user is logged in and has appropriate role
        $allowedRoles = ['admin', 'doctor', 'nurse', 'receptionist'];
        if (!in_array(session('role'), $allowedRoles)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized access']);
        }

        $limit = $this->request->getGet('limit') ?? 10;
        $appointments = $this->appointmentModel->getUpcomingAppointments($limit);

        return $this->response->setJSON([
            'success' => true,
            'appointments' => $appointments
        ]);
    }

    /**
     * Get appointments by date range (YYYY-MM-DD)
     */
    public function byDateRange()
    {
        $allowedRoles = ['admin', 'doctor', 'nurse', 'receptionist'];
        if (!in_array(session('role'), $allowedRoles)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized access'])->setStatusCode(401);
        }

        $start = $this->request->getGet('start_date');
        $end = $this->request->getGet('end_date');
        if (!$start || !$end || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $start) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $end)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid or missing date parameters'])->setStatusCode(400);
        }

        // Normalize order if needed
        if ($start > $end) {
            [$start, $end] = [$end, $start];
        }

        $appointments = $this->appointmentModel->getAppointmentsByDateRange($start, $end);
        return $this->response->setJSON(['success' => true, 'appointments' => $appointments]);
    }

    /**
     * Search appointments
     */
    public function search()
    {
        // Check if user is logged in and has appropriate role
        $allowedRoles = ['admin', 'doctor', 'nurse', 'receptionist'];
        if (!in_array(session('role'), $allowedRoles)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized access']);
        }

        $searchTerm = $this->request->getGet('q');
        if (empty($searchTerm)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Search term is required'
            ]);
        }

        $appointments = $this->appointmentModel->searchAppointments($searchTerm);

        return $this->response->setJSON([
            'success' => true,
            'appointments' => $appointments
        ]);
    }

    /**
     * Get appointment statistics
     */
    public function getStats()
    {
        // Check if user is logged in and has appropriate role
        $allowedRoles = ['admin', 'doctor', 'nurse'];
        if (!in_array(session('role'), $allowedRoles)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized access']);
        }

        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        
        $stats = $this->appointmentModel->getAppointmentStats($startDate, $endDate);

        return $this->response->setJSON([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Delete appointment
     */
    public function delete($id)
    {
        // Validate ID
        if (!is_numeric($id) || (int) $id <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid appointment id']);
        }
        // Check if user is logged in and has appropriate role
        $allowedRoles = ['admin', 'doctor', 'nurse', 'receptionist'];
        if (!in_array(session('role'), $allowedRoles)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized access']);
        }

        $appointment = $this->appointmentModel->find($id);
        if (!$appointment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Appointment not found'
            ]);
        }

        if ($this->appointmentModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Appointment deleted successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete appointment'
            ]);
        }
    }
}
