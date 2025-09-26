<?php

namespace App\Models;

use CodeIgniter\Model;

class PatientVitalModel extends Model
{
    protected $table = 'patient_vitals';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'patient_id', 'nurse_id', 'recorded_at', 'temperature', 'blood_pressure_systolic',
        'blood_pressure_diastolic', 'heart_rate', 'respiratory_rate', 'oxygen_saturation',
        'weight', 'height', 'bmi', 'pain_scale', 'notes'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'recorded_at' => 'datetime',
        'temperature' => 'float',
        'blood_pressure_systolic' => 'int',
        'blood_pressure_diastolic' => 'int',
        'heart_rate' => 'int',
        'respiratory_rate' => 'int',
        'oxygen_saturation' => 'float',
        'weight' => 'float',
        'height' => 'float',
        'bmi' => 'float',
        'pain_scale' => 'int'
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
        'nurse_id' => 'required|is_natural_no_zero',
        'recorded_at' => 'required|valid_date',
        'temperature' => 'permit_empty|decimal',
        'blood_pressure_systolic' => 'permit_empty|is_natural',
        'blood_pressure_diastolic' => 'permit_empty|is_natural',
        'heart_rate' => 'permit_empty|is_natural',
        'respiratory_rate' => 'permit_empty|is_natural',
        'oxygen_saturation' => 'permit_empty|decimal',
        'weight' => 'permit_empty|decimal',
        'height' => 'permit_empty|decimal',
        'pain_scale' => 'permit_empty|is_natural|less_than_equal_to[10]'
    ];

    protected $validationMessages = [
        'patient_id' => [
            'required' => 'Patient is required'
        ],
        'nurse_id' => [
            'required' => 'Nurse is required'
        ],
        'recorded_at' => [
            'required' => 'Recording time is required'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['calculateBMI'];
    protected $afterInsert = [];
    protected $beforeUpdate = ['calculateBMI'];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    protected function calculateBMI(array $data)
    {
        if (isset($data['data']['weight']) && isset($data['data']['height']) && 
            $data['data']['weight'] > 0 && $data['data']['height'] > 0) {
            $heightInMeters = $data['data']['height'] / 100;
            $data['data']['bmi'] = round($data['data']['weight'] / ($heightInMeters * $heightInMeters), 2);
        }
        return $data;
    }

    /**
     * Get vitals by patient
     */
    public function getVitalsByPatient($patientId, $limit = null)
    {
        $builder = $this->where('patient_id', $patientId)
                        ->orderBy('recorded_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->findAll();
    }

    /**
     * Get latest vitals for patient
     */
    public function getLatestVitals($patientId)
    {
        return $this->where('patient_id', $patientId)
                    ->orderBy('recorded_at', 'DESC')
                    ->first();
    }

    /**
     * Get vitals with patient and nurse info
     */
    public function getVitalsWithDetails($limit = null)
    {
        $builder = $this->select('patient_vitals.*, 
                                 patients.first_name as patient_first_name,
                                 patients.last_name as patient_last_name,
                                 patients.patient_id as patient_number,
                                 nurses.first_name as nurse_first_name,
                                 nurses.last_name as nurse_last_name')
                        ->join('patients', 'patients.id = patient_vitals.patient_id')
                        ->join('users as nurses', 'nurses.id = patient_vitals.nurse_id')
                        ->orderBy('patient_vitals.recorded_at', 'DESC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Get vitals by date range
     */
    public function getVitalsByDateRange($startDate, $endDate, $patientId = null)
    {
        $builder = $this->where('recorded_at >=', $startDate)
                        ->where('recorded_at <=', $endDate);

        if ($patientId) {
            $builder->where('patient_id', $patientId);
        }

        return $builder->orderBy('recorded_at', 'DESC')->findAll();
    }

    /**
     * Get abnormal vitals
     */
    public function getAbnormalVitals()
    {
        return $this->select('patient_vitals.*, 
                             patients.first_name as patient_first_name,
                             patients.last_name as patient_last_name,
                             patients.patient_id as patient_number')
                    ->join('patients', 'patients.id = patient_vitals.patient_id')
                    ->where('(temperature > 38.5 OR temperature < 35.0)')
                    ->orWhere('(blood_pressure_systolic > 140 OR blood_pressure_systolic < 90)')
                    ->orWhere('(heart_rate > 100 OR heart_rate < 60)')
                    ->orWhere('(oxygen_saturation < 95)')
                    ->orWhere('pain_scale >= 7')
                    ->orderBy('recorded_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get vitals trends for patient
     */
    public function getVitalsTrends($patientId, $days = 7)
    {
        $startDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        return $this->where('patient_id', $patientId)
                    ->where('recorded_at >=', $startDate)
                    ->orderBy('recorded_at', 'ASC')
                    ->findAll();
    }

    /**
     * Get vitals summary for patient
     */
    public function getVitalsSummary($patientId, $days = 30)
    {
        $startDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        return $this->select('
                        AVG(temperature) as avg_temperature,
                        AVG(blood_pressure_systolic) as avg_bp_systolic,
                        AVG(blood_pressure_diastolic) as avg_bp_diastolic,
                        AVG(heart_rate) as avg_heart_rate,
                        AVG(respiratory_rate) as avg_respiratory_rate,
                        AVG(oxygen_saturation) as avg_oxygen_saturation,
                        MAX(recorded_at) as last_recorded,
                        COUNT(*) as total_records
                    ')
                    ->where('patient_id', $patientId)
                    ->where('recorded_at >=', $startDate)
                    ->first();
    }
}
