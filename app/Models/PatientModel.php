<?php

namespace App\Models;

use CodeIgniter\Model;

class PatientModel extends Model
{
    protected $table = 'patients';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'patient_id', 'first_name', 'last_name', 'middle_name', 'date_of_birth', 'gender',
        'contact_number', 'phone', 'email', 'address', 'emergency_contact_name', 'emergency_contact_number', 'emergency_contact_phone',
        'government_id', 'blood_type', 'allergies', 'medical_history', 'status', 'department', 'admission_date', 'archived_at'
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
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'first_name' => 'required|max_length[100]',
        'last_name' => 'required|max_length[100]',
        'middle_name' => 'permit_empty|max_length[100]',
        'contact_number' => 'permit_empty|max_length[20]',
        'phone' => 'permit_empty|max_length[20]',
        'email' => 'permit_empty|valid_email|max_length[255]',
        'date_of_birth' => 'permit_empty|valid_date',
        'gender' => 'permit_empty|in_list[male,female,other]',
        'address' => 'permit_empty',
        'emergency_contact_name' => 'permit_empty|max_length[100]',
        'emergency_contact_number' => 'permit_empty|max_length[20]',
        'emergency_contact_phone' => 'permit_empty|max_length[20]',
        'blood_type' => 'permit_empty|in_list[A+,A-,B+,B-,AB+,AB-,O+,O-]',
        'allergies' => 'permit_empty',
        'medical_history' => 'permit_empty',
        'status' => 'permit_empty|in_list[active,inactive,deceased,inpatient,outpatient,archived]',
        'department' => 'permit_empty|max_length[100]',
        'admission_date' => 'permit_empty|valid_date'
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generatePatientId'];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    protected function generatePatientId(array $data)
    {
        if (!isset($data['data']['patient_id']) || empty($data['data']['patient_id'])) {
            try {
                $count = $this->countAll();
                $year = date('Y');
                $nextNumber = $count + 1;
                $data['data']['patient_id'] = 'PAT-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            } catch (\Exception $e) {
                // Fallback if count fails
                $data['data']['patient_id'] = 'PAT-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            }
        }
        return $data;
    }

    public function searchPatients($searchTerm)
    {
        return $this->like('first_name', $searchTerm)
                    ->orLike('last_name', $searchTerm)
                    ->orLike('patient_id', $searchTerm)
                    ->orLike('phone', $searchTerm)
                    ->orLike('email', $searchTerm)
                    ->where('status', 'active')
                    ->findAll();
    }

    public function getPatientWithAppointments($patientId)
    {
        return $this->select('patients.*, COUNT(appointments.id) as appointment_count')
                    ->join('appointments', 'appointments.patient_id = patients.id', 'left')
                    ->where('patients.id', $patientId)
                    ->groupBy('patients.id')
                    ->first();
    }

    public function getActivePatients()
    {
        return $this->where('status', 'active')->findAll();
    }

    public function getFilteredPatients($search = null, $status = null, $department = null)
    {
        $builder = $this->builder();
        
        // Exclude archived patients by default
        $builder->where('status !=', 'archived');
        
        if ($search) {
            $builder->groupStart()
                   ->like('first_name', $search)
                   ->orLike('last_name', $search)
                   ->orLike('patient_id', $search)
                   ->orLike('contact_number', $search)
                   ->orLike('phone', $search)
                   ->orLike('email', $search)
                   ->groupEnd();
        }
        
        if ($status) {
            $builder->where('status', $status);
        }
        
        if ($department) {
            $builder->where('department', $department);
        }
        
        return $builder->orderBy('created_at', 'DESC')->get()->getResultArray();
    }

    public function getUniqueDepartments()
    {
        return $this->distinct()
                   ->select('department')
                   ->where('department IS NOT NULL')
                   ->where('department !=', '')
                   ->where('status !=', 'archived')
                   ->findColumn('department');
    }

    public function getPatientAge($dateOfBirth)
    {
        if (!$dateOfBirth) return 'N/A';
        
        $dob = new \DateTime($dateOfBirth);
        $now = new \DateTime();
        $age = $now->diff($dob);
        
        return $age->y;
    }

    public function getPatientFullName($patient)
    {
        $name = trim($patient['first_name'] . ' ');
        if (!empty($patient['middle_name'])) {
            $name .= trim($patient['middle_name']) . ' ';
        }
        $name .= $patient['last_name'];
        
        return $name;
    }

    public function getPatientsByStatus($status)
    {
        return $this->where('status', $status)->findAll();
    }
}
