<?php

namespace App\Models;

use CodeIgniter\Model;

class LabRequestModel extends Model
{
    protected $table = 'lab_requests';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'patient_id', 'doctor_id', 'medical_record_id', 'request_number', 'test_type',
        'test_category', 'clinical_notes', 'priority', 'status', 'accepted_by',
        'accepted_at', 'completed_at', 'result_file', 'notes'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'accepted_at' => 'datetime',
        'completed_at' => 'datetime'
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
        'test_type' => 'required|max_length[120]',
        'test_category' => 'required|in_list[blood,urine,imaging,biopsy,culture,other]',
        'priority' => 'permit_empty|in_list[urgent,high,normal,low]',
        'status' => 'permit_empty|in_list[requested,accepted,processing,completed,cancelled]'
    ];

    protected $validationMessages = [
        'patient_id' => [
            'required' => 'Patient is required'
        ],
        'doctor_id' => [
            'required' => 'Doctor is required'
        ],
        'test_type' => [
            'required' => 'Test type is required'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateRequestNumber'];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    protected function generateRequestNumber(array $data)
    {
        if (!isset($data['data']['request_number'])) {
            $data['data']['request_number'] = 'LAB' . date('Y') . str_pad($this->countAll() + 1, 6, '0', STR_PAD_LEFT);
        }
        return $data;
    }

    /**
     * Get pending requests
     */
    public function getPendingRequests()
    {
        return $this->where('status', 'requested')
                    ->orderBy('priority', 'DESC')
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    /**
     * Get requests by status
     */
    public function getRequestsByStatus($status)
    {
        return $this->where('status', $status)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get requests with patient and doctor info
     */
    public function getRequestsWithDetails($status = null)
    {
        $builder = $this->select('lab_requests.*, 
                                 patients.first_name as patient_first_name,
                                 patients.last_name as patient_last_name,
                                 patients.patient_id as patient_number,
                                 doctors.first_name as doctor_first_name,
                                 doctors.last_name as doctor_last_name,
                                 accepter.first_name as accepter_first_name,
                                 accepter.last_name as accepter_last_name')
                        ->join('patients', 'patients.id = lab_requests.patient_id')
                        ->join('users as doctors', 'doctors.id = lab_requests.doctor_id')
                        ->join('users as accepter', 'accepter.id = lab_requests.accepted_by', 'left')
                        ->orderBy('lab_requests.created_at', 'DESC');

        if ($status) {
            $builder->where('lab_requests.status', $status);
        }

        return $builder->findAll();
    }

    /**
     * Get requests by patient
     */
    public function getRequestsByPatient($patientId)
    {
        return $this->where('patient_id', $patientId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get requests by doctor
     */
    public function getRequestsByDoctor($doctorId)
    {
        return $this->where('doctor_id', $doctorId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get urgent requests
     */
    public function getUrgentRequests()
    {
        return $this->select('lab_requests.*, 
                             patients.first_name as patient_first_name,
                             patients.last_name as patient_last_name,
                             patients.patient_id as patient_number')
                    ->join('patients', 'patients.id = lab_requests.patient_id')
                    ->whereIn('priority', ['urgent', 'high'])
                    ->whereIn('status', ['requested', 'accepted', 'processing'])
                    ->orderBy('priority', 'DESC')
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    /**
     * Accept request
     */
    public function acceptRequest($requestId, $acceptedBy)
    {
        return $this->update($requestId, [
            'status' => 'accepted',
            'accepted_by' => $acceptedBy,
            'accepted_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Complete request
     */
    public function completeRequest($requestId, $resultFile = null, $notes = null)
    {
        $updateData = [
            'status' => 'completed',
            'completed_at' => date('Y-m-d H:i:s')
        ];

        if ($resultFile) {
            $updateData['result_file'] = $resultFile;
        }

        if ($notes) {
            $updateData['notes'] = $notes;
        }

        return $this->update($requestId, $updateData);
    }

    /**
     * Get requests by category
     */
    public function getRequestsByCategory($category)
    {
        return $this->where('test_category', $category)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Search requests
     */
    public function searchRequests($searchTerm)
    {
        return $this->select('lab_requests.*, 
                             patients.first_name as patient_first_name,
                             patients.last_name as patient_last_name,
                             patients.patient_id as patient_number')
                    ->join('patients', 'patients.id = lab_requests.patient_id')
                    ->like('patients.first_name', $searchTerm)
                    ->orLike('patients.last_name', $searchTerm)
                    ->orLike('patients.patient_id', $searchTerm)
                    ->orLike('lab_requests.request_number', $searchTerm)
                    ->orLike('lab_requests.test_type', $searchTerm)
                    ->orderBy('lab_requests.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get request statistics
     */
    public function getRequestStats($startDate = null, $endDate = null)
    {
        $builder = $this->select('status, test_category, COUNT(*) as count')
                        ->groupBy(['status', 'test_category']);

        if ($startDate && $endDate) {
            $builder->where('created_at >=', $startDate)
                   ->where('created_at <=', $endDate);
        }

        return $builder->findAll();
    }

    /**
     * Get overdue requests
     */
    public function getOverdueRequests($hours = 24)
    {
        $overdueTime = date('Y-m-d H:i:s', strtotime("-{$hours} hours"));
        
        return $this->select('lab_requests.*, 
                             patients.first_name as patient_first_name,
                             patients.last_name as patient_last_name,
                             patients.patient_id as patient_number')
                    ->join('patients', 'patients.id = lab_requests.patient_id')
                    ->where('lab_requests.created_at <', $overdueTime)
                    ->whereIn('lab_requests.status', ['requested', 'accepted', 'processing'])
                    ->orderBy('lab_requests.created_at', 'ASC')
                    ->findAll();
    }
}
