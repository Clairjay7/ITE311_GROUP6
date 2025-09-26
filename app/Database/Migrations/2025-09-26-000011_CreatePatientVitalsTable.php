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
            'recorded_at' => [
                'type' => 'DATETIME',
            ],
            'temperature' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
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
            'respiratory_rate' => [
                'type' => 'INT',
                'constraint' => 3,
                'null' => true,
            ],
            'oxygen_saturation' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
            ],
            'weight' => [
                'type' => 'DECIMAL',
                'constraint' => '6,2',
                'null' => true,
            ],
            'height' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
            ],
            'bmi' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
            ],
            'pain_scale' => [
                'type' => 'INT',
                'constraint' => 2,
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
        $this->forge->addKey(['patient_id', 'recorded_at']);
        $this->forge->addForeignKey('patient_id', 'patients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('nurse_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('patient_vitals');
    }

    public function down()
    {
        $this->forge->dropTable('patient_vitals');
    }
}
