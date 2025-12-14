<?php

namespace App\Models;

use CodeIgniter\Model;

class HMSPatientModel extends Model
{
    protected $table = 'patients';
    protected $primaryKey = 'patient_id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        // Existing
        'full_name', 'gender', 'age', 'contact', 'address', 'type', 'doctor_id', 'department_id', 'purpose', 'admission_date', 'room_number', 'room_id',
        'created_at', 'updated_at',
        // New personal info
        'patient_reg_no', 'first_name', 'middle_name', 'last_name', 'extension_name', 'date_of_birth', 'civil_status',
        'address_street', 'address_barangay', 'address_city', 'address_province', 'email', 'nationality', 'religion',
        // Emergency contact
        'emergency_name', 'emergency_relationship', 'emergency_contact', 'emergency_address',
        // Medical
        'blood_type', 'allergies', 'existing_conditions', 'current_medications', 'past_surgeries', 'family_history',
        // Insurance/Billing
        'insurance_provider', 'insurance_number', 'philhealth_number', 'billing_address', 'payment_type',
        // Registration details & signatures
        'registration_date', 'registered_by', 'signature_patient', 'signature_staff', 'date_signed',
        // Visit type and triage
        'visit_type', 'triage_status',
        // Doctor check flag
        'is_doctor_checked',
        // Check status fields
        'doctor_check_status',
        'nurse_vital_status',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'first_name' => 'permit_empty|min_length[2]|max_length[60]',
        'middle_name' => 'permit_empty|max_length[60]',
        'last_name' => 'permit_empty|min_length[2]|max_length[60]',
        'extension_name' => 'permit_empty|max_length[20]',
        'email' => 'permit_empty|valid_email|max_length[120]',
        'gender' => 'permit_empty|in_list[male,female,other,Male,Female,Other]',
        'civil_status' => 'permit_empty|in_list[Single,Married,Widowed,Divorced,Separated,Annulled,Other]',
        'date_of_birth' => 'permit_empty|valid_date',
        'age' => 'permit_empty|integer|greater_than_equal_to[0]',
        'type' => 'permit_empty|in_list[In-Patient,Out-Patient]',
        'payment_type' => 'permit_empty|in_list[Cash,Insurance,Credit]',
        'blood_type' => 'permit_empty|in_list[A+,A-,B+,B-,AB+,AB-,O+,O-]',
    ];
}
