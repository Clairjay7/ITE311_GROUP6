<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTriageTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'patient_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'           => false,
            ],
            'nurse_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'           => false,
            ],
            'triage_level' => [
                'type'           => 'VARCHAR',
                'constraint'     => 50,
                'null'           => false,
                'comment'        => 'Critical, Moderate, Minor'
            ],
            'vital_signs' => [
                'type'           => 'TEXT',
                'null'           => true,
                'comment'        => 'JSON: heart_rate, blood_pressure, temperature, oxygen_saturation, respiratory_rate'
            ],
            'chief_complaint' => [
                'type'           => 'TEXT',
                'null'           => true,
            ],
            'notes' => [
                'type'           => 'TEXT',
                'null'           => true,
            ],
            'status' => [
                'type'           => 'VARCHAR',
                'constraint'     => 50,
                'default'        => 'pending',
                'comment'        => 'pending, completed, sent_to_doctor'
            ],
            'sent_to_doctor' => [
                'type'           => 'TINYINT',
                'constraint'     => 1,
                'default'        => 0,
            ],
            'doctor_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'           => true,
            ],
            'created_at' => [
                'type'           => 'DATETIME',
                'null'           => false,
            ],
            'updated_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('patient_id', 'patients', 'patient_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('nurse_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('doctor_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('triage');
    }

    public function down()
    {
        $this->forge->dropTable('triage');
    }
}

