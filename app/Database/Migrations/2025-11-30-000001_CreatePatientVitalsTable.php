<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePatientVitalsTable extends Migration
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
            'nurse_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'blood_pressure_systolic' => [
                'type' => 'INT',
                'constraint' => 3,
                'null' => true,
            ],
            'blood_pressure_diastolic' => [
                'type' => 'INT',
                'constraint' => 3,
                'null' => true,
            ],
            'heart_rate' => [
                'type' => 'INT',
                'constraint' => 3,
                'null' => true,
            ],
            'temperature' => [
                'type' => 'DECIMAL',
                'constraint' => '4,1',
                'null' => true,
            ],
            'oxygen_saturation' => [
                'type' => 'INT',
                'constraint' => 3,
                'null' => true,
            ],
            'respiratory_rate' => [
                'type' => 'INT',
                'constraint' => 3,
                'null' => true,
            ],
            'weight' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
            ],
            'height' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'recorded_at' => [
                'type' => 'DATETIME',
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
        $this->forge->addForeignKey('patient_id', 'admin_patients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('nurse_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('patient_vitals');
    }

    public function down()
    {
        $this->forge->dropTable('patient_vitals');
    }
}

