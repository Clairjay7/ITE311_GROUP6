<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDoctorIdToLabServices extends Migration
{
    public function up()
    {
        $this->forge->addColumn('lab_services', [
            'doctor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'patient_id',
                'comment' => 'Doctor who requested the lab service',
            ],
            'lab_request_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'doctor_id',
                'comment' => 'Link to lab_requests table',
            ],
        ]);

        // Add indexes
        $this->forge->addKey('doctor_id');
        $this->forge->addKey('lab_request_id');
        
        // Add foreign key if tables exist
        if ($this->db->tableExists('users') && $this->db->tableExists('lab_requests')) {
            try {
                $this->forge->addForeignKey('doctor_id', 'users', 'id', 'SET NULL', 'CASCADE', 'fk_lab_services_doctor');
            } catch (\Exception $e) {
                // Ignore if foreign key already exists
            }
            try {
                $this->forge->addForeignKey('lab_request_id', 'lab_requests', 'id', 'SET NULL', 'CASCADE', 'fk_lab_services_lab_request');
            } catch (\Exception $e) {
                // Ignore if foreign key already exists
            }
        }
    }

    public function down()
    {
        // Drop foreign keys first
        if ($this->db->tableExists('lab_services')) {
            try {
                $this->forge->dropForeignKey('lab_services', 'fk_lab_services_doctor');
            } catch (\Exception $e) {
                // Ignore if foreign keys don't exist
            }
            try {
                $this->forge->dropForeignKey('lab_services', 'fk_lab_services_lab_request');
            } catch (\Exception $e) {
                // Ignore if foreign keys don't exist
            }
        }
        
        $this->forge->dropColumn('lab_services', ['doctor_id', 'lab_request_id']);
    }
}

