<?php

namespace App\Models;

use CodeIgniter\Model;

class PatientCheckInModel extends Model
{
    protected $table = 'patient_check_ins';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'appointment_id', 'patient_id', 'check_in_time', 'check_out_time', 'status', 'notes'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function activeQueue()
    {
        return $this->whereIn('status', ['waiting','in_consultation'])
                    ->orderBy('check_in_time','ASC')
                    ->findAll();
    }
}
