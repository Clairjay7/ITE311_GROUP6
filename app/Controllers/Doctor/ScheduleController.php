<?php

namespace App\Controllers\Doctor;

use App\Controllers\BaseController;

class ScheduleController extends BaseController
{
    public function index()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return redirect()->to('/auth')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $doctorId = session()->get('user_id');
        $db = \Config\Database::connect();
        
        // Get view type and selected date
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
        }
        
        // Get selected month from query parameter (format: Y-m)
        $selectedMonth = $this->request->getGet('month');
        
        // Get all schedules (for month grouping)
        $allSchedules = [];
        if ($db->tableExists('doctor_schedules')) {
            $allSchedules = $db->table('doctor_schedules')
                ->where('doctor_id', $doctorId)
                ->where('status !=', 'cancelled')
                ->where('shift_date >=', date('Y-m-d')) // Only future or today's schedules
                ->orderBy('shift_date', 'ASC')
                ->orderBy('start_time', 'ASC')
                ->get()
                ->getResultArray();
        }
        
        // Get all appointments (for month grouping)
        $allAppointments = [];
        if ($db->tableExists('appointments')) {
            $allAppointments = $db->table('appointments')
                ->select('appointments.*, patients.first_name, patients.middle_name, patients.last_name, patients.contact')
                ->join('patients', 'patients.patient_id = appointments.patient_id', 'left')
                ->where('appointments.doctor_id', $doctorId)
                ->whereNotIn('appointments.status', ['cancelled', 'no_show'])
                ->where('appointments.appointment_date >=', date('Y-m-d')) // Only future or today's appointments
                ->orderBy('appointments.appointment_date', 'ASC')
                ->orderBy('appointments.appointment_time', 'ASC')
                ->get()
                ->getResultArray();
        }
        
        // Group schedules by month
        $schedulesByMonth = [];
        $allMonths = [];
        foreach ($allSchedules as $schedule) {
            $month = date('Y-m', strtotime($schedule['shift_date']));
            $date = $schedule['shift_date'];
            
            if (!isset($schedulesByMonth[$month])) {
                $schedulesByMonth[$month] = [];
                $allMonths[] = $month;
            }
            if (!isset($schedulesByMonth[$month][$date])) {
                $schedulesByMonth[$month][$date] = [];
            }
            $schedulesByMonth[$month][$date][] = $schedule;
        }
        sort($allMonths);
        
        // Group appointments by month
        $appointmentsByMonth = [];
        foreach ($allAppointments as $appointment) {
            $month = date('Y-m', strtotime($appointment['appointment_date']));
            $date = $appointment['appointment_date'];
            
            if (!isset($appointmentsByMonth[$month])) {
                $appointmentsByMonth[$month] = [];
            }
            if (!isset($appointmentsByMonth[$month][$date])) {
                $appointmentsByMonth[$month][$date] = [];
            }
            $appointmentsByMonth[$month][$date][] = $appointment;
        }
        
        // Filter schedules and appointments based on selected month or view type
        $schedules = [];
        $appointments = [];
        $schedulesByDate = [];
        $appointmentsByDate = [];
        
        if ($selectedMonth) {
            // Show only selected month
            $schedulesByDate = $schedulesByMonth[$selectedMonth] ?? [];
            $appointmentsByDate = $appointmentsByMonth[$selectedMonth] ?? [];
            
            // Flatten for display
            foreach ($schedulesByDate as $daySchedules) {
                $schedules = array_merge($schedules, $daySchedules);
            }
            foreach ($appointmentsByDate as $dayAppointments) {
                $appointments = array_merge($appointments, $dayAppointments);
            }
        } else {
            // Show all schedules and appointments
            foreach ($schedulesByMonth as $monthSchedules) {
                foreach ($monthSchedules as $daySchedules) {
                    $schedules = array_merge($schedules, $daySchedules);
                }
            }
            foreach ($appointmentsByMonth as $monthAppointments) {
                foreach ($monthAppointments as $dayAppointments) {
                    $appointments = array_merge($appointments, $dayAppointments);
                }
            }
        }
        
        // Get consultations from consultations table
        $consultations = [];
        if ($db->tableExists('consultations')) {
            $consultationQuery = $db->table('consultations')
                ->select('consultations.*, 
                         COALESCE(admin_patients.firstname, patients.first_name) as patient_first_name,
                         COALESCE(admin_patients.lastname, patients.last_name) as patient_last_name,
                         COALESCE(admin_patients.contact, patients.contact) as patient_contact')
                ->join('admin_patients', 'admin_patients.id = consultations.patient_id', 'left')
                ->join('patients', 'patients.patient_id = consultations.patient_id', 'left')
                ->where('consultations.doctor_id', $doctorId)
                ->whereIn('consultations.status', ['approved', 'pending', 'completed']);
            
            if ($selectedMonth) {
                $consultationQuery->where('DATE_FORMAT(consultations.consultation_date, "%Y-%m")', $selectedMonth);
            } else {
                $consultationQuery->where('consultations.consultation_date >=', date('Y-m-d'));
            }
            
            $consultations = $consultationQuery
                ->orderBy('consultations.consultation_date', 'ASC')
                ->orderBy('consultations.consultation_time', 'ASC')
                ->get()
                ->getResultArray();
        }
        
        // Get In-Patient registrations assigned to this doctor
        $inPatients = [];
        if ($db->tableExists('patients')) {
            $inPatientQuery = $db->table('patients')
                ->select('patients.*, 
                         patients.first_name as patient_first_name,
                         patients.middle_name as patient_middle_name,
                         patients.last_name as patient_last_name,
                         patients.contact as patient_contact,
                         patients.admission_date,
                         patients.visit_type,
                         patients.purpose')
                ->where('patients.doctor_id', $doctorId)
                ->where('patients.type', 'In-Patient')
                ->where('patients.admission_date >=', date('Y-m-d')); // Only today and future admissions
            
            if ($selectedMonth) {
                $inPatientQuery->where('DATE_FORMAT(patients.admission_date, "%Y-%m")', $selectedMonth);
            }
            
            $inPatients = $inPatientQuery
                ->orderBy('patients.admission_date', 'ASC')
                ->get()
                ->getResultArray();
        }
        
        // Get doctor info
        $doctor = null;
        if ($db->tableExists('doctors')) {
            $doctor = $db->table('doctors')
                ->where('user_id', $doctorId)
                ->get()
                ->getRowArray();
        }
        
        // Group In-Patients by month and date
        $inPatientsByMonth = [];
        $inPatientsByDate = [];
        foreach ($inPatients as $inPatient) {
            $month = date('Y-m', strtotime($inPatient['admission_date']));
            $date = $inPatient['admission_date'];
            
            if (!isset($inPatientsByMonth[$month])) {
                $inPatientsByMonth[$month] = [];
            }
            if (!isset($inPatientsByMonth[$month][$date])) {
                $inPatientsByMonth[$month][$date] = [];
            }
            $inPatientsByMonth[$month][$date][] = $inPatient;
        }
        
        if ($selectedMonth) {
            $inPatientsByDate = $inPatientsByMonth[$selectedMonth] ?? [];
        }
        
        $data = [
            'title' => 'My Schedule',
            'doctor' => $doctor,
            'schedules' => $schedules,
            'appointments' => $appointments,
            'consultations' => $consultations,
            'inPatients' => $inPatients,
            'inPatientsByMonth' => $inPatientsByMonth,
            'inPatientsByDate' => $inPatientsByDate,
            'schedulesByMonth' => $schedulesByMonth,
            'schedulesByDate' => $schedulesByDate,
            'appointmentsByMonth' => $appointmentsByMonth,
            'appointmentsByDate' => $appointmentsByDate,
            'allMonths' => $allMonths,
            'selectedMonth' => $selectedMonth,
            'viewType' => $viewType,
            'selectedDate' => $selectedDate,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];

        return view('doctor/schedule/index', $data);
    }
}

