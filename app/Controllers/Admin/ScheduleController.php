<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ScheduleModel;
use App\Models\AdminPatientModel;

class ScheduleController extends BaseController
{
    protected $scheduleModel;
    protected $patientModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->scheduleModel = new ScheduleModel();
        $this->patientModel = new AdminPatientModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();
        $viewType = $this->request->getGet('view') ?: 'date';
        $selectedDate = $this->request->getGet('date') ?: date('Y-m-d');
        
        // Calculate date range based on view type
        $startDate = $selectedDate;
        $endDate = $selectedDate;
        
        if ($viewType === 'week') {
            // Get week range (Monday to Sunday)
            $dateObj = new \DateTime($selectedDate);
            $dayOfWeek = (int)$dateObj->format('w'); // 0 = Sunday, 1 = Monday, etc.
            $daysToMonday = $dayOfWeek == 0 ? 6 : $dayOfWeek - 1; // Days to go back to Monday
            $dateObj->modify('-' . $daysToMonday . ' days');
            $startDate = $dateObj->format('Y-m-d');
            $dateObj->modify('+6 days');
            $endDate = $dateObj->format('Y-m-d');
        } elseif ($viewType === 'month') {
            // Get month range
            $dateObj = new \DateTime($selectedDate);
            $startDate = $dateObj->format('Y-m-01'); // First day of month
            $endDate = $dateObj->format('Y-m-t'); // Last day of month
        } elseif ($viewType === 'year') {
            // Get year range from today to 1 year from today (based on schedule creation period)
            $startDate = date('Y-m-d'); // Today's date
            $endDate = date('Y-m-d', strtotime('+1 year')); // 1 year from today
        }
        
        // Get all users (doctors and nurses) with their schedules
        $usersWithSchedules = [];
        
        // Get all doctors
        if ($db->tableExists('users') && $db->tableExists('roles')) {
            $doctors = $db->table('users')
                ->select('users.id, users.username, users.email, roles.name as role_name, doctors.specialization')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->join('doctors', 'doctors.user_id = users.id', 'left')
                ->where('LOWER(roles.name)', 'doctor')
                ->where('users.status', 'active')
                ->orderBy('users.username', 'ASC')
                ->get()
                ->getResultArray();
            
            foreach ($doctors as $doctor) {
                // Get doctor schedules from doctor_schedules table
                $doctorSchedules = [];
                if ($db->tableExists('doctor_schedules')) {
                    $scheduleQuery = $db->table('doctor_schedules')
                        ->where('doctor_id', $doctor['id']);
                    
                    // Only exclude cancelled schedules, show all others (active, inactive, null)
                    $scheduleQuery->groupStart()
                        ->where('status !=', 'cancelled')
                        ->orWhere('status', null)
                    ->groupEnd();
                    
                    if ($viewType === 'date') {
                        $scheduleQuery->where('shift_date', $selectedDate);
                    } else {
                        $scheduleQuery->where('shift_date >=', $startDate)
                                      ->where('shift_date <=', $endDate);
                    }
                    
                    $doctorSchedules = $scheduleQuery
                        ->orderBy('shift_date', 'ASC')
                        ->orderBy('start_time', 'ASC')
                        ->get()
                        ->getResultArray();
                }
                
                // Also get patient appointments from schedules table
                $patientAppointments = [];
                if ($db->tableExists('schedules')) {
                    $appointmentQuery = $db->table('schedules')
                        ->select('schedules.*, admin_patients.firstname, admin_patients.lastname')
                        ->join('admin_patients', 'admin_patients.id = schedules.patient_id', 'left')
                        ->where('schedules.doctor', $doctor['username']);
                    
                    if ($viewType === 'date') {
                        $appointmentQuery->where('schedules.date', $selectedDate);
                    } else {
                        $appointmentQuery->where('schedules.date >=', $startDate)
                                         ->where('schedules.date <=', $endDate);
                    }
                    
                    $patientAppointments = $appointmentQuery
                        ->orderBy('schedules.date', 'ASC')
                        ->orderBy('schedules.time', 'ASC')
                        ->get()
                        ->getResultArray();
                }
                
                // Only add doctor to list if they have schedules or appointments
                // This ensures only doctors with manually created schedules are shown
                if (!empty($doctorSchedules) || !empty($patientAppointments)) {
                    $usersWithSchedules[] = [
                        'user_id' => $doctor['id'],
                        'username' => $doctor['username'],
                        'email' => $doctor['email'],
                        'role' => 'Doctor',
                        'role_name' => $doctor['role_name'],
                        'specialization' => $doctor['specialization'] ?? null,
                        'schedules' => $doctorSchedules,
                        'appointments' => $patientAppointments,
                    ];
                }
            }
            
            // Get all nurses
            $nurses = $db->table('users')
                ->select('users.id, users.username, users.email, roles.name as role_name')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('LOWER(roles.name)', 'nurse')
                ->where('users.status', 'active')
                ->orderBy('users.username', 'ASC')
                ->get()
                ->getResultArray();
            
            foreach ($nurses as $nurse) {
                // Get nurse schedules from nurse_schedules table
                $nurseSchedules = [];
                if ($db->tableExists('nurse_schedules')) {
                    $scheduleQuery = $db->table('nurse_schedules')
                        ->where('nurse_id', $nurse['id'])
                        ->where('status !=', 'cancelled');
                    
                    if ($viewType === 'date') {
                        $scheduleQuery->where('shift_date', $selectedDate);
                    } else {
                        $scheduleQuery->where('shift_date >=', $startDate)
                                      ->where('shift_date <=', $endDate);
                    }
                    
                    $nurseSchedules = $scheduleQuery
                        ->orderBy('shift_date', 'ASC')
                        ->orderBy('start_time', 'ASC')
                        ->get()
                        ->getResultArray();
                }
                
                // Only add nurse to list if they have schedules
                // This ensures only nurses with manually created schedules are shown
                if (!empty($nurseSchedules)) {
                    $usersWithSchedules[] = [
                        'user_id' => $nurse['id'],
                        'username' => $nurse['username'],
                        'email' => $nurse['email'],
                        'role' => 'Nurse',
                        'role_name' => $nurse['role_name'],
                        'schedules' => $nurseSchedules,
                        'appointments' => [], // Nurses don't have patient appointments in schedules table
                    ];
                }
            }
        }
        
        $data = [
            'title' => 'Scheduling',
            'usersWithSchedules' => $usersWithSchedules,
            'selectedDate' => $selectedDate,
            'viewType' => $viewType,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];

        return view('admin/schedule/index', $data);
    }

    public function view($userId)
    {
        $db = \Config\Database::connect();
        $role = $this->request->getGet('role') ?: 'doctor';
        $selectedMonth = $this->request->getGet('month'); // Format: Y-m (e.g., 2025-01)
        
        // Get user information
        $user = null;
        $schedules = [];
        $appointments = [];
        
        if ($role === 'doctor') {
            $user = $db->table('users')
                ->select('users.id, users.username, users.email, doctors.doctor_name, doctors.specialization')
                ->join('doctors', 'doctors.user_id = users.id', 'left')
                ->where('users.id', $userId)
                ->where('users.status', 'active')
                ->get()
                ->getRowArray();
            
            if ($user && $db->tableExists('doctor_schedules')) {
                $scheduleQuery = $db->table('doctor_schedules')
                    ->where('doctor_id', $userId);
                
                // Only exclude cancelled schedules, show all others (active, inactive, null)
                $scheduleQuery->groupStart()
                    ->where('status !=', 'cancelled')
                    ->orWhere('status', null)
                ->groupEnd();
                
                // Filter by month if selected
                if ($selectedMonth) {
                    $scheduleQuery->where('DATE_FORMAT(shift_date, "%Y-%m")', $selectedMonth);
                }
                
                $schedules = $scheduleQuery
                    ->orderBy('shift_date', 'ASC')
                    ->orderBy('start_time', 'ASC')
                    ->get()
                    ->getResultArray();
            }
            
            // Get patient appointments
            if ($db->tableExists('schedules')) {
                $appointmentQuery = $db->table('schedules')
                    ->select('schedules.*, admin_patients.firstname, admin_patients.lastname')
                    ->join('admin_patients', 'admin_patients.id = schedules.patient_id', 'left')
                    ->where('schedules.doctor', $user['username'] ?? '');
                
                // Filter by month if selected
                if ($selectedMonth) {
                    $appointmentQuery->where('DATE_FORMAT(schedules.date, "%Y-%m")', $selectedMonth);
                }
                
                $appointments = $appointmentQuery
                    ->orderBy('schedules.date', 'ASC')
                    ->orderBy('schedules.time', 'ASC')
                    ->get()
                    ->getResultArray();
            }
        } else {
            $user = $db->table('users')
                ->select('users.id, users.username, users.email')
                ->where('users.id', $userId)
                ->where('users.status', 'active')
                ->get()
                ->getRowArray();
            
            if ($user && $db->tableExists('nurse_schedules')) {
                $scheduleQuery = $db->table('nurse_schedules')
                    ->where('nurse_id', $userId)
                    ->where('status !=', 'cancelled');
                
                // Filter by month if selected
                if ($selectedMonth) {
                    $scheduleQuery->where('DATE_FORMAT(shift_date, "%Y-%m")', $selectedMonth);
                }
                
                $schedules = $scheduleQuery
                    ->orderBy('shift_date', 'ASC')
                    ->orderBy('start_time', 'ASC')
                    ->get()
                    ->getResultArray();
            }
        }
        
        if (!$user) {
            return redirect()->to('/admin/schedule')->with('error', 'User not found.');
        }
        
        // Get ALL schedules (without month filter) to build month list
        $allSchedulesForMonths = [];
        if ($role === 'doctor' && $db->tableExists('doctor_schedules')) {
            $allSchedulesQuery = $db->table('doctor_schedules')
                ->where('doctor_id', $userId);
            
            $allSchedulesQuery->groupStart()
                ->where('status !=', 'cancelled')
                ->orWhere('status', null)
            ->groupEnd();
            
            $allSchedulesForMonths = $allSchedulesQuery
                ->orderBy('shift_date', 'ASC')
                ->get()
                ->getResultArray();
        } elseif ($role === 'nurse' && $db->tableExists('nurse_schedules')) {
            $allSchedulesForMonths = $db->table('nurse_schedules')
                ->where('nurse_id', $userId)
                ->where('status !=', 'cancelled')
                ->orderBy('shift_date', 'ASC')
                ->get()
                ->getResultArray();
        }
        
        // Get all unique months from ALL schedules (not filtered)
        $allMonths = [];
        foreach ($allSchedulesForMonths as $schedule) {
            $month = date('Y-m', strtotime($schedule['shift_date']));
            if (!in_array($month, $allMonths)) {
                $allMonths[] = $month;
            }
        }
        sort($allMonths);
        
        // Group schedules by month and date
        $schedulesByMonth = [];
        foreach ($schedules as $schedule) {
            $month = date('Y-m', strtotime($schedule['shift_date']));
            $date = $schedule['shift_date'];
            
            if (!isset($schedulesByMonth[$month])) {
                $schedulesByMonth[$month] = [];
            }
            if (!isset($schedulesByMonth[$month][$date])) {
                $schedulesByMonth[$month][$date] = [];
            }
            $schedulesByMonth[$month][$date][] = $schedule;
        }
        
        // Group appointments by month and date
        $appointmentsByMonth = [];
        foreach ($appointments as $appointment) {
            $month = date('Y-m', strtotime($appointment['date']));
            $date = $appointment['date'];
            
            if (!isset($appointmentsByMonth[$month])) {
                $appointmentsByMonth[$month] = [];
            }
            if (!isset($appointmentsByMonth[$month][$date])) {
                $appointmentsByMonth[$month][$date] = [];
            }
            $appointmentsByMonth[$month][$date][] = $appointment;
        }
        
        // If no month selected, show all schedules grouped by month
        // If month selected, show only that month's schedules grouped by date
        $schedulesByDate = [];
        $appointmentsByDate = [];
        if ($selectedMonth) {
            $schedulesByDate = $schedulesByMonth[$selectedMonth] ?? [];
            $appointmentsByDate = $appointmentsByMonth[$selectedMonth] ?? [];
        }
        
        $data = [
            'title' => 'Schedule - ' . ($user['doctor_name'] ?? $user['username']),
            'user' => $user,
            'role' => $role,
            'schedules' => $schedules,
            'schedulesByDate' => $schedulesByDate,
            'schedulesByMonth' => $schedulesByMonth,
            'appointments' => $appointments,
            'appointmentsByDate' => $appointmentsByDate,
            'appointmentsByMonth' => $appointmentsByMonth,
            'allMonths' => $allMonths,
            'selectedMonth' => $selectedMonth,
        ];
        
        return view('admin/schedule/view_individual', $data);
    }

    public function edit($id)
    {
        $db = \Config\Database::connect();
        $role = $this->request->getGet('role') ?: 'doctor';
        
        if ($role === 'doctor') {
            if (!$db->tableExists('doctor_schedules')) {
                return redirect()->to('/admin/schedule')->with('error', 'Doctor schedules table does not exist.');
            }
            
            $schedule = $db->table('doctor_schedules')
                ->where('id', $id)
                ->get()
                ->getRowArray();
            
            if (!$schedule) {
                return redirect()->to('/admin/schedule')->with('error', 'Schedule not found.');
            }
            
            // Get doctor information
            $doctor = $db->table('doctors')
                ->select('doctors.*, users.username, users.email, users.id as user_id')
                ->join('users', 'users.id = doctors.user_id', 'inner')
                ->where('doctors.user_id', $schedule['doctor_id'])
                ->get()
                ->getRowArray();
            
            $data = [
                'title' => 'Edit Doctor Schedule',
                'schedule' => $schedule,
                'doctor' => $doctor,
                'role' => 'doctor',
                'validation' => \Config\Services::validation(),
            ];
            
            return view('admin/schedule/edit_doctor', $data);
        } else {
            if (!$db->tableExists('nurse_schedules')) {
                return redirect()->to('/admin/schedule')->with('error', 'Nurse schedules table does not exist.');
            }
            
            $schedule = $db->table('nurse_schedules')
                ->where('id', $id)
                ->get()
                ->getRowArray();
            
            if (!$schedule) {
                return redirect()->to('/admin/schedule')->with('error', 'Schedule not found.');
            }
            
            // Get nurse information
            $nurse = $db->table('users')
                ->where('id', $schedule['nurse_id'])
                ->get()
                ->getRowArray();
            
            $data = [
                'title' => 'Edit Nurse Schedule',
                'schedule' => $schedule,
                'nurse' => $nurse,
                'role' => 'nurse',
                'validation' => \Config\Services::validation(),
            ];
            
            return view('admin/schedule/edit_nurse', $data);
        }
    }

    public function update($id)
    {
        $db = \Config\Database::connect();
        $role = $this->request->getGet('role') ?: 'doctor';
        
        if ($role === 'doctor') {
            $rules = [
                'shift_date' => 'required|valid_date',
                'start_time' => 'required',
                'end_time' => 'required',
                'status' => 'required|in_list[active,cancelled,on_leave]',
            ];
            
            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
            
            $data = [
                'shift_date' => $this->request->getPost('shift_date'),
                'start_time' => $this->request->getPost('start_time'),
                'end_time' => $this->request->getPost('end_time'),
                'status' => $this->request->getPost('status'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            
            $db->table('doctor_schedules')
                ->where('id', $id)
                ->update($data);
            
            $schedule = $db->table('doctor_schedules')->where('id', $id)->get()->getRowArray();
            return redirect()->to('/admin/schedule/view/' . $schedule['doctor_id'] . '?role=doctor')->with('success', 'Doctor schedule updated successfully.');
        } else {
            $rules = [
                'shift_date' => 'required|valid_date',
                'shift_type' => 'required|in_list[morning,night]',
                'start_time' => 'required',
                'end_time' => 'required',
                'status' => 'required|in_list[active,cancelled,on_leave]',
            ];
            
            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
            
            $data = [
                'shift_date' => $this->request->getPost('shift_date'),
                'shift_type' => $this->request->getPost('shift_type'),
                'start_time' => $this->request->getPost('start_time'),
                'end_time' => $this->request->getPost('end_time'),
                'status' => $this->request->getPost('status'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            
            $db->table('nurse_schedules')
                ->where('id', $id)
                ->update($data);
            
            $schedule = $db->table('nurse_schedules')->where('id', $id)->get()->getRowArray();
            return redirect()->to('/admin/schedule/view/' . $schedule['nurse_id'] . '?role=nurse')->with('success', 'Nurse schedule updated successfully.');
        }
    }

    public function create()
    {
        // Show selection page for Doctor or Nurse schedule
        $data = [
            'title' => 'Create Schedule',
        ];

        return view('admin/schedule/create', $data);
    }

    public function createDoctor()
    {
        $db = \Config\Database::connect();
        
        // Get all active doctors with their specializations
        $doctors = [];
        if ($db->tableExists('doctors') && $db->tableExists('users')) {
            $doctors = $db->table('doctors')
                ->select('doctors.*, users.username, users.email, users.id as user_id')
                ->join('users', 'users.id = doctors.user_id', 'inner')
                ->where('users.status', 'active')
                ->orderBy('doctors.doctor_name', 'ASC')
                ->get()
                ->getResultArray();
        }
        
        // Get departments
        $departments = [];
        if ($db->tableExists('departments')) {
            $departments = $db->table('departments')
                ->orderBy('department_name', 'ASC')
                ->get()
                ->getResultArray();
        }
        
        $data = [
            'title' => 'Create Doctor Schedule',
            'doctors' => $doctors,
            'departments' => $departments,
            'validation' => \Config\Services::validation(),
        ];

        return view('admin/schedule/create_doctor', $data);
    }

    public function createNurse()
    {
        $db = \Config\Database::connect();
        
        // Get all active nurses
        $nurses = [];
        if ($db->tableExists('users') && $db->tableExists('roles')) {
            $nurses = $db->table('users')
                ->select('users.id, users.username, users.email')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('LOWER(roles.name)', 'nurse')
                ->where('users.status', 'active')
                ->orderBy('users.username', 'ASC')
                ->get()
                ->getResultArray();
        }
        
        $data = [
            'title' => 'Create Nurse Schedule',
            'nurses' => $nurses,
            'validation' => \Config\Services::validation(),
        ];

        return view('admin/schedule/create_nurse', $data);
    }

    public function storeDoctor()
    {
        $rules = [
            'doctor_id' => 'required|integer',
            'working_days' => 'required',
            'time_in' => 'required',
            'time_out' => 'required',
            'shift_type' => 'required|in_list[morning,afternoon,evening,whole_day]',
            'on_call' => 'required|in_list[yes,no]',
            'status' => 'required|in_list[active,inactive]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $doctorScheduleModel = new \App\Models\DoctorScheduleModel();
        
        $doctorId = $this->request->getPost('doctor_id');
        $workingDays = $this->request->getPost('working_days');
        $timeIn = $this->request->getPost('time_in');
        $timeOut = $this->request->getPost('time_out');
        $shiftType = $this->request->getPost('shift_type');
        $onCall = $this->request->getPost('on_call');
        $onCallNotes = $this->request->getPost('on_call_notes');
        $maxPatients = $this->request->getPost('max_patients');
        $status = $this->request->getPost('status');
        
        // Day name to day number mapping
        $dayMap = [
            'Monday' => 1,
            'Tuesday' => 2,
            'Wednesday' => 3,
            'Thursday' => 4,
            'Friday' => 5,
            'Saturday' => 6,
            'Sunday' => 0,
        ];
        
        // Generate schedules for 1 year starting from today's date
        $startDate = new \DateTime(); // Today's date
        $endDate = new \DateTime(); // Today's date
        $endDate->modify('+1 year'); // 1 year from today
        
        $schedulesToInsert = [];
        $currentDate = clone $startDate;
        
        while ($currentDate <= $endDate) {
            $dayOfWeek = (int)$currentDate->format('w'); // 0 = Sunday, 1 = Monday, etc.
            $dayName = $currentDate->format('l'); // Full day name
            
            // Check if this day is in working days
            if (in_array($dayName, $workingDays)) {
                $schedulesToInsert[] = [
                    'doctor_id' => $doctorId,
                    'shift_date' => $currentDate->format('Y-m-d'),
                    'start_time' => $timeIn,
                    'end_time' => $timeOut,
                    'status' => $status,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
            
            $currentDate->modify('+1 day');
        }
        
        // Batch insert schedules
        if (!empty($schedulesToInsert)) {
            $chunks = array_chunk($schedulesToInsert, 100);
            foreach ($chunks as $chunk) {
                $db->table('doctor_schedules')->insertBatch($chunk);
            }
        }
        
        return redirect()->to('/admin/schedule')->with('success', 'Doctor schedule created successfully for ' . count($schedulesToInsert) . ' days.');
    }

    public function storeNurse()
    {
        $rules = [
            'nurse_id' => 'required|integer',
            'working_days' => 'required',
            'shift_type' => 'required|in_list[morning,night,whole_day]',
            'time_in' => 'required',
            'time_out' => 'required',
            'duty_type' => 'required|in_list[regular,float]',
            'standby' => 'required|in_list[yes,no]',
            'status' => 'required|in_list[active,inactive]',
        ];
        
        // Station assignment is only required if duty_type is not 'float'
        $dutyType = $this->request->getPost('duty_type');
        if ($dutyType !== 'float') {
            $rules['station_assignment'] = 'required|max_length[255]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $nurseScheduleModel = new \App\Models\NurseScheduleModel();
        
        $nurseId = $this->request->getPost('nurse_id');
        $workingDays = $this->request->getPost('working_days');
        $shiftType = $this->request->getPost('shift_type');
        $timeIn = $this->request->getPost('time_in');
        $timeOut = $this->request->getPost('time_out');
        $dutyType = $this->request->getPost('duty_type');
        $standby = $this->request->getPost('standby');
        $stationAssignment = $this->request->getPost('station_assignment');
        $status = $this->request->getPost('status');
        
        // Generate schedules for 1 year starting from today's date
        $startDate = new \DateTime(); // Today's date
        $endDate = new \DateTime(); // Today's date
        $endDate->modify('+1 year'); // 1 year from today
        
        $schedulesToInsert = [];
        $currentDate = clone $startDate;
        
        while ($currentDate <= $endDate) {
            $dayName = $currentDate->format('l'); // Full day name
            
            // Check if this day is in working days
            if (in_array($dayName, $workingDays)) {
                $schedulesToInsert[] = [
                    'nurse_id' => $nurseId,
                    'shift_date' => $currentDate->format('Y-m-d'),
                    'shift_type' => $shiftType,
                    'start_time' => $timeIn,
                    'end_time' => $timeOut,
                    'status' => $status,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
            
            $currentDate->modify('+1 day');
        }
        
        // Batch insert schedules
        if (!empty($schedulesToInsert)) {
            $chunks = array_chunk($schedulesToInsert, 100);
            foreach ($chunks as $chunk) {
                $db->table('nurse_schedules')->insertBatch($chunk);
            }
        }
        
        return redirect()->to('/admin/schedule')->with('success', 'Nurse schedule created successfully for ' . count($schedulesToInsert) . ' days.');
    }

    public function store()
    {
        $rules = [
            'patient_id' => 'required|integer',
            'date' => 'required|valid_date',
            'time' => 'required',
            'doctor' => 'required|max_length[255]',
            'status' => 'required|in_list[pending,confirmed,completed,cancelled]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'patient_id' => $this->request->getPost('patient_id'),
            'date' => $this->request->getPost('date'),
            'time' => $this->request->getPost('time'),
            'doctor' => $this->request->getPost('doctor'),
            'status' => $this->request->getPost('status'),
        ];

        $this->scheduleModel->insert($data);

        return redirect()->to('/admin/schedule')->with('success', 'Schedule created successfully.');
    }

    public function delete($id)
    {
        $db = \Config\Database::connect();
        $role = $this->request->getGet('role') ?: 'doctor';
        
        if ($role === 'doctor') {
            if ($db->tableExists('doctor_schedules')) {
                $schedule = $db->table('doctor_schedules')->where('id', $id)->get()->getRowArray();
                if ($schedule) {
                    $db->table('doctor_schedules')->where('id', $id)->delete();
                    return redirect()->to('/admin/schedule')->with('success', 'Doctor schedule deleted successfully.');
                }
            }
        } elseif ($role === 'nurse') {
            if ($db->tableExists('nurse_schedules')) {
                $schedule = $db->table('nurse_schedules')->where('id', $id)->get()->getRowArray();
                if ($schedule) {
                    $db->table('nurse_schedules')->where('id', $id)->delete();
                    return redirect()->to('/admin/schedule')->with('success', 'Nurse schedule deleted successfully.');
                }
            }
        }
        
        return redirect()->to('/admin/schedule')->with('error', 'Schedule not found.');
    }

    public function cleanupOldSchedules()
    {
        $db = \Config\Database::connect();
        $currentYear = date('Y'); // Current year (2025)
        $deletedCount = 0;
        
        // Delete doctor schedules from previous years (before current year)
        if ($db->tableExists('doctor_schedules')) {
            $doctorSchedules = $db->table('doctor_schedules')
                ->where('YEAR(shift_date) <', $currentYear)
                ->delete();
            $deletedCount += $db->affectedRows();
        }
        
        // Delete nurse schedules from previous years (before current year)
        if ($db->tableExists('nurse_schedules')) {
            $nurseSchedules = $db->table('nurse_schedules')
                ->where('YEAR(shift_date) <', $currentYear)
                ->delete();
            $deletedCount += $db->affectedRows();
        }
        
        return redirect()->to('/admin/schedule')->with('success', 'Successfully deleted ' . $deletedCount . ' old schedule(s) from previous years.');
    }

    public function clearAllDoctorSchedules()
    {
        $db = \Config\Database::connect();
        $deletedCount = 0;
        
        // Delete ALL doctor schedules
        if ($db->tableExists('doctor_schedules')) {
            $db->table('doctor_schedules')->delete();
            $deletedCount = $db->affectedRows();
        }
        
        return redirect()->to('/admin/schedule')->with('success', 'Successfully deleted all ' . $deletedCount . ' doctor schedule(s). You can now create new schedules using the Create Schedule form.');
    }
}

