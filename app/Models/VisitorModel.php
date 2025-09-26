<?php

namespace App\Models;

use CodeIgniter\Model;

class VisitorModel extends Model
{
    protected $table = 'visitors';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'patient_id', 'visitor_name', 'visitor_phone', 'relation_to_patient',
        'visit_date', 'time_in', 'time_out', 'purpose', 'status'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function activeVisitors()
    {
        return $this->where('status', 'in')->orderBy('visit_date', 'DESC')->findAll();
    }
}
