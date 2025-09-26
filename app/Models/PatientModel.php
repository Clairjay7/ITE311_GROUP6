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
        'patient_id', 'first_name', 'last_name', 'date_of_birth', 'gender',
        'phone', 'email', 'address', 'emergency_contact_name', 'emergency_contact_phone',
        'government_id', 'blood_type', 'allergies', 'status'
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
        'phone' => 'permit_empty|max_length[30]',
        'email' => 'permit_empty|valid_email|max_length[120]',
        'gender' => 'permit_empty|in_list[male,female,other]',
        'status' => 'permit_empty|in_list[active,inactive,deceased]'
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
        if (!isset($data['data']['patient_id'])) {
            $data['data']['patient_id'] = 'P' . date('Y') . str_pad($this->countAll() + 1, 6, '0', STR_PAD_LEFT);
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
}
