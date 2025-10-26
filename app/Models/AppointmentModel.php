<?php

namespace App\Models;

use CodeIgniter\Model;

class AppointmentModel extends Model
{
    protected $table = 'appointments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'appointment_id', 'patient_id', 'doctor_id', 'appointment_date', 'appointment_time', 
        'type', 'appointment_type', 'status', 'notes', 'patient_name', 'patient_phone', 
        'doctor_name', 'department', 'created_by'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'appointment_date' => 'required|valid_date',
        'appointment_time' => 'required',
        'patient_name' => 'permit_empty|max_length[255]',
        'doctor_name' => 'permit_empty|max_length[255]',
        'appointment_type' => 'permit_empty|in_list[consultation,follow-up,emergency,surgery,therapy]',
        'status' => 'permit_empty|in_list[pending,scheduled,confirmed,in_progress,completed,cancelled,no_show]'
    ];

    protected $validationMessages = [
        'patient_name' => [
            'required' => 'Patient name is required'
        ],
        'doctor_name' => [
            'required' => 'Doctor name is required'
        ],
        'appointment_date' => [
            'required' => 'Appointment date is required',
            'valid_date' => 'Please enter a valid date'
        ],
        'appointment_time' => [
            'required' => 'Appointment time is required'
        ],
        'appointment_type' => [
            'required' => 'Appointment type is required'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    public function upcomingForDateRange($start, $end)
    {
        return $this->where('appointment_date >=', $start)
                    ->where('appointment_date <=', $end)
                    ->orderBy('appointment_date', 'ASC')
                    ->findAll();
    }

    public function withPatientDoctor()
    {
        return $this->select('appointments.*, patients.first_name as patient_first_name, patients.last_name as patient_last_name, doctors.name as doctor_name')
                    ->join('patients', 'patients.id = appointments.patient_id')
                    ->join('doctors', 'doctors.id = appointments.doctor_id');
    }

    public function getByDoctor($doctorId)
    {
        return $this->select('appointments.*, 
                             patients.first_name as patient_first_name,
                             patients.last_name as patient_last_name,
                             patients.patient_id as patient_number')
                    ->join('patients', 'patients.id = appointments.patient_id')
                    ->where('appointments.doctor_id', $doctorId)
                    ->orderBy('appointments.appointment_date', 'ASC')
                    ->findAll();
    }

    public function getByPatient($patientId)
    {
        return $this->select('appointments.*, 
                             users.first_name as doctor_first_name,
                             users.last_name as doctor_last_name')
                    ->join('users', 'users.id = appointments.doctor_id')
                    ->where('appointments.patient_id', $patientId)
                    ->orderBy('appointments.appointment_date', 'DESC')
                    ->findAll();
    }

    /**
     * Get all appointments with complete information
     */
    public function getAllAppointments()
    {
        return $this->orderBy('appointment_date', 'DESC')
                    ->orderBy('appointment_time', 'ASC')
                    ->findAll();
    }

    /**
     * Get appointments by status
     */
    public function getByStatus($status)
    {
        return $this->where('status', $status)
                    ->orderBy('appointment_date', 'ASC')
                    ->findAll();
    }

    /**
     * Get today's appointments
     */
    public function getTodaysAppointments()
    {
        return $this->where('appointment_date', date('Y-m-d'))
                    ->orderBy('appointment_time', 'ASC')
                    ->findAll();
    }

    /**
     * Get appointments by date range
     */
    public function getByDateRange($startDate, $endDate)
    {
        return $this->where('appointment_date >=', $startDate)
                    ->where('appointment_date <=', $endDate)
                    ->orderBy('appointment_date', 'ASC')
                    ->orderBy('appointment_time', 'ASC')
                    ->findAll();
    }

    /**
     * Search appointments
     */
    public function searchAppointments($searchTerm)
    {
        return $this->like('patient_name', $searchTerm)
                    ->orLike('doctor_name', $searchTerm)
                    ->orLike('department', $searchTerm)
                    ->orderBy('appointment_date', 'DESC')
                    ->findAll();
    }

    /**
     * Cancel appointment
     */
    public function cancelAppointment($id)
    {
        return $this->update($id, ['status' => 'cancelled']);
    }

    /**
     * Reschedule appointment
     */
    public function rescheduleAppointment($id, $newDate, $newTime, $reason = null)
    {
        $data = [
            'appointment_date' => $newDate,
            'appointment_time' => $newTime
        ];
        
        if ($reason) {
            $data['notes'] = $reason;
        }
        
        return $this->update($id, $data);
    }

    /**
     * Get appointment statistics
     */
    public function getStatistics()
    {
        $total = $this->countAll();
        $today = $this->where('appointment_date', date('Y-m-d'))->countAllResults();
        $pending = $this->where('status', 'pending')->countAllResults();
        $completed = $this->where('status', 'completed')->countAllResults();
        
        return [
            'total' => $total,
            'today' => $today,
            'pending' => $pending,
            'completed' => $completed
        ];
    }
}
