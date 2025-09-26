<?php

namespace App\Models;

use CodeIgniter\Model;

class AdmissionModel extends Model
{
    protected $table = 'admissions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'admission_number', 'patient_id', 'doctor_id', 'department_id', 'room_id',
        'admitted_by', 'admission_date', 'admission_type', 'admission_reason',
        'expected_discharge_date', 'discharge_date', 'discharge_reason',
        'discharged_by', 'status', 'insurance_info', 'emergency_contact', 'notes'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'admission_date' => 'datetime',
        'expected_discharge_date' => 'datetime',
        'discharge_date' => 'datetime',
        'insurance_info' => 'json',
        'emergency_contact' => 'json'
    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'patient_id' => 'required|is_natural_no_zero',
        'doctor_id' => 'required|is_natural_no_zero',
        'department_id' => 'required|is_natural_no_zero',
        'admitted_by' => 'required|is_natural_no_zero',
        'admission_date' => 'required|valid_date',
        'admission_type' => 'required|in_list[emergency,planned,transfer]',
        'admission_reason' => 'required',
        'status' => 'permit_empty|in_list[admitted,discharged,transferred]'
    ];

    protected $validationMessages = [
        'patient_id' => [
            'required' => 'Patient is required'
        ],
        'doctor_id' => [
            'required' => 'Doctor is required'
        ],
        'admission_reason' => [
            'required' => 'Admission reason is required'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateAdmissionNumber'];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    protected function generateAdmissionNumber(array $data)
    {
        if (!isset($data['data']['admission_number'])) {
            $data['data']['admission_number'] = 'ADM' . date('Y') . str_pad($this->countAll() + 1, 6, '0', STR_PAD_LEFT);
        }
        return $data;
    }

    /**
     * Get current admissions
     */
    public function getCurrentAdmissions()
    {
        return $this->where('status', 'admitted')
                    ->orderBy('admission_date', 'DESC')
                    ->findAll();
    }

    /**
     * Get admissions with patient and doctor info
     */
    public function getAdmissionsWithDetails()
    {
        return $this->select('admissions.*, 
                             patients.first_name as patient_first_name,
                             patients.last_name as patient_last_name,
                             patients.patient_id as patient_number,
                             doctors.first_name as doctor_first_name,
                             doctors.last_name as doctor_last_name,
                             departments.department_name,
                             rooms.room_number')
                    ->join('patients', 'patients.id = admissions.patient_id')
                    ->join('users as doctors', 'doctors.id = admissions.doctor_id')
                    ->join('departments', 'departments.id = admissions.department_id')
                    ->join('rooms', 'rooms.id = admissions.room_id', 'left')
                    ->orderBy('admissions.admission_date', 'DESC')
                    ->findAll();
    }

    /**
     * Get admissions by patient
     */
    public function getAdmissionsByPatient($patientId)
    {
        return $this->where('patient_id', $patientId)
                    ->orderBy('admission_date', 'DESC')
                    ->findAll();
    }

    /**
     * Get admissions by department
     */
    public function getAdmissionsByDepartment($departmentId)
    {
        return $this->where('department_id', $departmentId)
                    ->where('status', 'admitted')
                    ->orderBy('admission_date', 'DESC')
                    ->findAll();
    }

    /**
     * Get emergency admissions
     */
    public function getEmergencyAdmissions()
    {
        return $this->where('admission_type', 'emergency')
                    ->where('status', 'admitted')
                    ->orderBy('admission_date', 'DESC')
                    ->findAll();
    }

    /**
     * Get admissions due for discharge
     */
    public function getAdmissionsDueForDischarge($days = 0)
    {
        $checkDate = date('Y-m-d', strtotime("+{$days} days"));
        
        return $this->select('admissions.*, 
                             patients.first_name as patient_first_name,
                             patients.last_name as patient_last_name,
                             patients.patient_id as patient_number')
                    ->join('patients', 'patients.id = admissions.patient_id')
                    ->where('admissions.expected_discharge_date <=', $checkDate)
                    ->where('admissions.status', 'admitted')
                    ->orderBy('admissions.expected_discharge_date', 'ASC')
                    ->findAll();
    }

    /**
     * Discharge patient
     */
    public function dischargePatient($admissionId, $dischargedBy, $dischargeReason = null)
    {
        return $this->update($admissionId, [
            'status' => 'discharged',
            'discharge_date' => date('Y-m-d H:i:s'),
            'discharged_by' => $dischargedBy,
            'discharge_reason' => $dischargeReason
        ]);
    }

    /**
     * Search admissions
     */
    public function searchAdmissions($searchTerm)
    {
        return $this->select('admissions.*, 
                             patients.first_name as patient_first_name,
                             patients.last_name as patient_last_name,
                             patients.patient_id as patient_number')
                    ->join('patients', 'patients.id = admissions.patient_id')
                    ->like('patients.first_name', $searchTerm)
                    ->orLike('patients.last_name', $searchTerm)
                    ->orLike('patients.patient_id', $searchTerm)
                    ->orLike('admissions.admission_number', $searchTerm)
                    ->orderBy('admissions.admission_date', 'DESC')
                    ->findAll();
    }

    /**
     * Get admission statistics
     */
    public function getAdmissionStats($startDate = null, $endDate = null)
    {
        $builder = $this->select('admission_type, status, COUNT(*) as count')
                        ->groupBy(['admission_type', 'status']);

        if ($startDate && $endDate) {
            $builder->where('admission_date >=', $startDate)
                   ->where('admission_date <=', $endDate);
        }

        return $builder->findAll();
    }
}
