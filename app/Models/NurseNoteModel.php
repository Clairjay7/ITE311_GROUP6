<?php

namespace App\Models;

use CodeIgniter\Model;

class NurseNoteModel extends Model
{
    protected $table = 'nurse_notes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'patient_id',
        'nurse_id',
        'note_type',
        'note',
        'priority',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'patient_id' => 'required|integer',
        'nurse_id' => 'required|integer',
        'note_type' => 'required|in_list[progress,observation,medication,incident,other]',
        'note' => 'required',
        'priority' => 'required|in_list[low,normal,high,urgent]',
    ];
}

