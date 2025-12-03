<?php

namespace App\Controllers\Nurse;

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
     * View all doctor schedules with availability status
     */
    public function index()
    {
        if (!session()->get('logged_in') || !in_array(session()->get('role'), ['nurse', 'receptionist', 'admin'])) {
            return redirect()->to('/auth')->with('error', 'You must be logged in to access this page.');
        }

        $db = \Config\Database::connect();
        $date = $this->request->getGet('date') ?: date('Y-m-d');

        // Get all active doctors
        $doctors = [];
        if ($db->tableExists('users') && $db->tableExists('roles')) {
            $doctors = $db->table('users')
                ->select('users.id, users.username, users.email')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('LOWER(roles.name)', 'doctor')
                ->where('users.status', 'active')
                ->orderBy('users.username', 'ASC')
                ->get()
                ->getResultArray();
        }

        // Get schedules for the selected date
        $schedules = [];
        if ($db->tableExists('doctor_schedules')) {
            $schedules = $db->table('doctor_schedules')
                ->select('doctor_schedules.*, users.username as doctor_name')
                ->join('users', 'users.id = doctor_schedules.doctor_id', 'left')
                ->where('shift_date', $date)
                ->where('status !=', 'cancelled')
                ->orderBy('start_time', 'ASC')
                ->get()
                ->getResultArray();
        }

        // Get appointments/consultations for the date
        $appointments = [];
        if ($db->tableExists('consultations')) {
            $consultations = $db->table('consultations')
                ->select('doctor_id, consultation_date, consultation_time, type')
                ->where('consultation_date', $date)
                ->where('type !=', 'cancelled')
                ->where('deleted_at', null)
                ->get()
                ->getResultArray();
            foreach ($consultations as $consultation) {
                if (!isset($appointments[$consultation['doctor_id']])) {
                    $appointments[$consultation['doctor_id']] = [];
                }
                $appointments[$consultation['doctor_id']][] = $consultation;
            }
        }

        if ($db->tableExists('appointments')) {
            $apts = $db->table('appointments')
                ->select('doctor_id, appointment_date, appointment_time, status')
                ->where('appointment_date', $date)
                ->whereNotIn('status', ['cancelled', 'no_show'])
                ->get()
                ->getResultArray();
            foreach ($apts as $apt) {
                if (!isset($appointments[$apt['doctor_id']])) {
                    $appointments[$apt['doctor_id']] = [];
                }
                $appointments[$apt['doctor_id']][] = $apt;
            }
        }

        // Calculate availability for each doctor
        $doctorAvailability = [];
        foreach ($doctors as $doctor) {
            $doctorId = $doctor['id'];
            $doctorSchedule = array_filter($schedules, fn($s) => $s['doctor_id'] == $doctorId);
            
            $availability = [
                'doctor' => $doctor,
                'schedules' => $doctorSchedule,
                'appointments' => $appointments[$doctorId] ?? [],
                'status' => 'off_duty',
                'available_slots' => 0,
                'used_slots' => count($appointments[$doctorId] ?? []),
            ];

            if (!empty($doctorSchedule)) {
                $schedule = reset($doctorSchedule);
                $start = strtotime($schedule['start_time']);
                $end = strtotime($schedule['end_time']);
                $duration = ($end - $start) / 3600; // hours
                $maxSlots = (int)($duration * 2); // 2 patients per hour
                $usedSlots = count($appointments[$doctorId] ?? []);
                
                $availability['available_slots'] = max(0, $maxSlots - $usedSlots);
                $availability['max_slots'] = $maxSlots;
                
                if ($usedSlots >= $maxSlots) {
                    $availability['status'] = 'full';
                } elseif ($usedSlots >= ($maxSlots * 0.8)) {
                    $availability['status'] = 'busy';
                } else {
                    $availability['status'] = 'available';
                }
            } else {
                $availability['status'] = 'no_schedule';
            }

            $doctorAvailability[] = $availability;
        }

        $data = [
            'title' => 'Doctor Schedules',
            'doctors' => $doctorAvailability,
            'selected_date' => $date,
        ];

        return view('nurse/doctor_schedules/index', $data);
    }
}


