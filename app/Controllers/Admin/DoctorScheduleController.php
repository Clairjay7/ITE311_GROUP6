<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DoctorScheduleModel;
use App\Models\DoctorModel;

class DoctorScheduleController extends BaseController
{
    protected $scheduleModel;
    protected $doctorModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->scheduleModel = new DoctorScheduleModel();
        $this->doctorModel = new DoctorModel();
    }

    public function index()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as admin to access this page.');
        }

        $db = \Config\Database::connect();
        $selectedDate = $this->request->getGet('date') ?: date('Y-m-d');
        $selectedDoctor = $this->request->getGet('doctor_id');

        // Get all active doctors
        $doctors = [];
        if ($db->tableExists('users') && $db->tableExists('roles')) {
            $doctors = $db->table('users')
                ->select('users.id, users.username, users.email, doctors.specialization, doctors.doctor_name')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->join('doctors', 'doctors.user_id = users.id', 'left')
                ->where('LOWER(roles.name)', 'doctor')
                ->where('users.status', 'active')
                ->orderBy('users.username', 'ASC')
                ->get()
                ->getResultArray();
        }

        // Get schedules for selected date
        $schedules = [];
        if ($db->tableExists('doctor_schedules')) {
            $scheduleQuery = $db->table('doctor_schedules')
                ->select('doctor_schedules.*, users.username as doctor_name, doctors.specialization')
                ->join('users', 'users.id = doctor_schedules.doctor_id', 'left')
                ->join('doctors', 'doctors.user_id = users.id', 'left')
                ->where('doctor_schedules.shift_date', $selectedDate)
                ->where('doctor_schedules.status !=', 'cancelled')
                ->orderBy('doctor_schedules.start_time', 'ASC');
            
            if ($selectedDoctor) {
                $scheduleQuery->where('doctor_schedules.doctor_id', $selectedDoctor);
            }
            
            $schedules = $scheduleQuery->get()->getResultArray();
        }

        $data = [
            'title' => 'Doctor Schedules',
            'doctors' => $doctors,
            'schedules' => $schedules,
            'selectedDate' => $selectedDate,
            'selectedDoctor' => $selectedDoctor,
        ];

        return view('admin/doctor_schedules/index', $data);
    }

    public function create()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as admin to access this page.');
        }

        $db = \Config\Database::connect();
        
        // Get all active doctors with their info
        $doctors = [];
        if ($db->tableExists('users') && $db->tableExists('roles') && $db->tableExists('doctors')) {
            $doctors = $db->table('users')
                ->select('users.id, users.username, users.email, doctors.specialization, doctors.doctor_name, departments.department_name')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->join('doctors', 'doctors.user_id = users.id', 'left')
                ->join('departments', 'departments.id = doctors.department_id', 'left')
                ->where('LOWER(roles.name)', 'doctor')
                ->where('users.status', 'active')
                ->orderBy('users.username', 'ASC')
                ->get()
                ->getResultArray();
        }

        // Get all departments
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

        return view('admin/doctor_schedules/create', $data);
    }

    public function store()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as admin to access this page.');
        }

        $rules = [
            'doctor_id' => 'required|integer|greater_than[0]',
            'working_days' => 'required',
            'time_in' => 'required|regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/]',
            'time_out' => 'required|regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/]',
            'shift_type' => 'required|in_list[morning,afternoon,evening,whole_day]',
            'on_call' => 'permit_empty|in_list[yes,no]',
            'status' => 'required|in_list[active,inactive]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $doctorId = $this->request->getPost('doctor_id');
        $workingDays = $this->request->getPost('working_days');
        $timeIn = $this->request->getPost('time_in');
        $timeOut = $this->request->getPost('time_out');
        $breakTime = $this->request->getPost('break_time');
        $shiftType = $this->request->getPost('shift_type');
        $onCall = $this->request->getPost('on_call') === 'yes' ? 1 : 0;
        $onCallNotes = $this->request->getPost('on_call_notes');
        $maxPatientsPerDay = $this->request->getPost('max_patients_per_day');
        $status = $this->request->getPost('status');

        // Generate schedules for each selected working day for the next 3 months
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+3 months'));
        $currentDate = new \DateTime($startDate);
        $endDateObj = new \DateTime($endDate);
        
        $schedulesToInsert = [];
        $dayMap = [
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6,
            'sunday' => 0,
        ];

        while ($currentDate <= $endDateObj) {
            $dayOfWeek = (int)$currentDate->format('w'); // 0 = Sunday, 1 = Monday, etc.
            $dayName = strtolower($currentDate->format('l')); // Monday, Tuesday, etc.
            
            // Check if this day is in the selected working days
            if (is_array($workingDays) && in_array($dayName, $workingDays)) {
                $dateStr = $currentDate->format('Y-m-d');
                
                // Check if schedule already exists
                $existing = $db->table('doctor_schedules')
                    ->where('doctor_id', $doctorId)
                    ->where('shift_date', $dateStr)
                    ->where('start_time', $timeIn)
                    ->get()
                    ->getRowArray();
                
                if (!$existing) {
                    $schedulesToInsert[] = [
                        'doctor_id' => $doctorId,
                        'shift_date' => $dateStr,
                        'start_time' => $timeIn . ':00',
                        'end_time' => $timeOut . ':00',
                        'status' => $status,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                }
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

        return redirect()->to('/admin/doctor-schedules')->with('success', 'Doctor schedule created successfully.');
    }
}

