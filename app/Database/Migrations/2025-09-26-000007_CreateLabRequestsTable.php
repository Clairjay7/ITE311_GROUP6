<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLabRequestsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'patient_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'doctor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'medical_record_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'request_number' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'unique' => true,
            ],
            'test_type' => [
                'type' => 'VARCHAR',
                'constraint' => 120,
            ],
            'test_category' => [
                'type' => 'ENUM',
                'constraint' => ['blood', 'urine', 'imaging', 'biopsy', 'culture', 'other'],
                'default' => 'other',
            ],
            'clinical_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'priority' => [
                'type' => 'ENUM',
                'constraint' => ['urgent', 'high', 'normal', 'low'],
                'default' => 'normal',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['requested', 'accepted', 'processing', 'completed', 'cancelled'],
                'default' => 'requested',
            ],
            'accepted_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'accepted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'completed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'result_file' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['patient_id', 'status']);
        $this->forge->addKey(['doctor_id', 'created_at']);
        $this->forge->addForeignKey('patient_id', 'patients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('doctor_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('medical_record_id', 'medical_records', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('accepted_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('lab_requests');
    }

    public function down()
    {
        $this->forge->dropTable('lab_requests');
    }
}


