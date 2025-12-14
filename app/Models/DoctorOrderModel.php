<?php

namespace App\Models;

use CodeIgniter\Model;

class DoctorOrderModel extends Model
{
    protected $table = 'doctor_orders';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'patient_id',
        'admission_id',
        'doctor_id',
        'nurse_id',
        'order_type',
        'order_description',
        'medicine_name',
        'dosage',
        'instructions',
        'frequency',
        'duration',
        'remarks',
        'start_date',
        'end_date',
        'status',
        'pharmacy_status',
        'pharmacy_approved_at',
        'pharmacy_prepared_at',
        'pharmacy_dispensed_at',
        'purchase_location',
        'vital_id',
        'completed_by',
        'completed_at',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'patient_id' => 'required|integer',
        'doctor_id' => 'required|integer',
        'order_type' => 'required|in_list[medication,lab_test,diagnostic_imaging,nursing_order,treatment_order,iv_fluids_order,procedure,reassessment_order,stat_order,diet,activity,other]',
        'order_description' => 'required',
        'status' => 'required|in_list[pending,in_progress,completed,cancelled]',
    ];
}

