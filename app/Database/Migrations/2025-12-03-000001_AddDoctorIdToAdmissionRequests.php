<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDoctorIdToAdmissionRequests extends Migration
{
    public function up()
    {
        // Check if admission_requests table exists
        if ($this->db->tableExists('admission_requests')) {
            // Check if doctor_id column already exists
            $fields = $this->db->getFieldData('admission_requests');
            $hasDoctorId = false;
            foreach ($fields as $field) {
                if ($field->name === 'doctor_id') {
                    $hasDoctorId = true;
                    break;
                }
            }
            
            if (!$hasDoctorId) {
                $this->forge->addColumn('admission_requests', [
                    'doctor_id' => [
                        'type' => 'INT',
                        'constraint' => 11,
                        'unsigned' => true,
                        'null' => true,
                        'after' => 'consultation_id',
                        'comment' => 'Doctor ID assigned to this admission request (from triage)',
                    ],
                ]);
                
                // Add index for faster queries
                $this->forge->addKey('doctor_id', false, false, 'admission_requests_doctor_id_idx');
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('admission_requests')) {
            // Check if doctor_id column exists before dropping
            $fields = $this->db->getFieldData('admission_requests');
            $hasDoctorId = false;
            foreach ($fields as $field) {
                if ($field->name === 'doctor_id') {
                    $hasDoctorId = true;
                    break;
                }
            }
            
            if ($hasDoctorId) {
                $this->forge->dropColumn('admission_requests', 'doctor_id');
            }
        }
    }
}

