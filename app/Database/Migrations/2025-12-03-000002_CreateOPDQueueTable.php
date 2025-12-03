<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOPDQueueTable extends Migration
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
                'null' => false,
            ],
            'triage_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Reference to triage record'
            ],
            'queue_number' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
                'comment' => 'OPD queue number'
            ],
            'doctor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Assigned doctor for consultation'
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['waiting', 'in_consultation', 'completed', 'cancelled'],
                'default' => 'waiting',
                'null' => false,
            ],
            'estimated_wait_time' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Estimated wait time in minutes'
            ],
            'called_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'When patient was called for consultation'
            ],
            'consultation_started_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'When consultation started'
            ],
            'consultation_completed_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'When consultation completed'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('queue_number');
        $this->forge->addKey('status');
        $this->forge->addKey('patient_id');
        $this->forge->addKey('triage_id');
        $this->forge->createTable('opd_queue');
    }

    public function down()
    {
        $this->forge->dropTable('opd_queue');
    }
}

