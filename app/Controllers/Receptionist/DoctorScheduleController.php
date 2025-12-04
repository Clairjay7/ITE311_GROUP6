<?php

namespace App\Controllers\Receptionist;

use App\Controllers\BaseController;
use App\Models\DoctorScheduleModel;

class DoctorScheduleController extends BaseController
{
    protected $scheduleModel;

    public function __construct()
    {
        $this->scheduleModel = new DoctorScheduleModel();
    }

    /**
     * View all doctor schedules - organized by month and day
     */
    public function index()
    {
        if (!session()->get('logged_in') || !in_array(session()->get('role'), ['receptionist', 'admin'])) {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a receptionist to access this page.');
        }

        $db = \Config\Database::connect();
        
        // Check if table exists
        if (!$db->tableExists('doctor_schedules')) {
            return redirect()->back()->with('error', 'Doctor schedules table does not exist. Please contact administrator.');
        }

        // Get selected doctor (optional filter)
        $selectedDoctorId = $this->request->getGet('doctor_id');
        $currentYear = date('Y');
        $startDate = $currentYear . '-01-01';
        $endDate = $currentYear . '-12-31';

        // Get all active doctors
        $doctors = [];
        if ($db->tableExists('users') && $db->tableExists('roles')) {
            $doctorsQuery = $db->table('users')
                ->select('users.id, users.username, users.email')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('LOWER(roles.name)', 'doctor')
                ->where('users.status', 'active')
                ->orderBy('users.username', 'ASC');
            
            $doctors = $doctorsQuery->get()->getResultArray();
        }

        // Get schedules for all doctors or selected doctor
        $schedulesQuery = $db->table('doctor_schedules')
            ->select('doctor_schedules.*, users.username as doctor_name, users.id as doctor_id')
            ->join('users', 'users.id = doctor_schedules.doctor_id', 'left')
            ->where('doctor_schedules.shift_date >=', $startDate)
            ->where('doctor_schedules.shift_date <=', $endDate)
            ->where('doctor_schedules.status !=', 'cancelled')
            ->orderBy('users.username', 'ASC')
            ->orderBy('doctor_schedules.shift_date', 'ASC')
            ->orderBy('doctor_schedules.start_time', 'ASC');

        if ($selectedDoctorId) {
            $schedulesQuery->where('doctor_schedules.doctor_id', $selectedDoctorId);
        }

        $schedules = $schedulesQuery->get()->getResultArray();

        // Organize schedules by doctor, then by month and day
        $scheduleByDoctor = [];
        foreach ($schedules as $schedule) {
            $doctorId = $schedule['doctor_id'];
            $doctorName = $schedule['doctor_name'];
            
            if (!isset($scheduleByDoctor[$doctorId])) {
                $scheduleByDoctor[$doctorId] = [
                    'doctor_id' => $doctorId,
                    'doctor_name' => $doctorName,
                    'months' => []
                ];
            }

            $date = new \DateTime($schedule['shift_date']);
            $month = $date->format('F Y'); // e.g., "January 2025"
            $day = $date->format('d'); // Day number
            $dayName = $date->format('l'); // Day name
            
            if (!isset($scheduleByDoctor[$doctorId]['months'][$month])) {
                $scheduleByDoctor[$doctorId]['months'][$month] = [];
            }
            
            if (!isset($scheduleByDoctor[$doctorId]['months'][$month][$day])) {
                $scheduleByDoctor[$doctorId]['months'][$month][$day] = [
                    'date' => $schedule['shift_date'],
                    'day_name' => $dayName,
                    'time_slots' => []
                ];
            }
            
            // Format time
            $startTime = date('g:i A', strtotime($schedule['start_time']));
            $endTime = date('g:i A', strtotime($schedule['end_time']));
            $timeSlot = $startTime . ' - ' . $endTime;
            
            // Avoid duplicates
            if (!in_array($timeSlot, $scheduleByDoctor[$doctorId]['months'][$month][$day]['time_slots'])) {
                $scheduleByDoctor[$doctorId]['months'][$month][$day]['time_slots'][] = $timeSlot;
            }
        }

        $data = [
            'title' => 'Doctor Schedules',
            'scheduleByDoctor' => $scheduleByDoctor,
            'doctors' => $doctors,
            'selectedDoctorId' => $selectedDoctorId,
            'currentYear' => $currentYear
        ];

        return view('Reception/doctor_schedules/index', $data);
    }
}

