<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAdmissionsTable extends Migration
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
            'admission_number' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'unique' => true,
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
            'department_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'room_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'admitted_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'admission_date' => [
                'type' => 'DATETIME',
            ],
            'admission_type' => [
                'type' => 'ENUM',
                'constraint' => ['emergency', 'planned', 'transfer'],
                'default' => 'planned',
            ],
            'admission_reason' => [
                'type' => 'TEXT',
            ],
            'expected_discharge_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'discharge_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'discharge_reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'discharged_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['admitted', 'discharged', 'transferred'],
                'default' => 'admitted',
            ],
            'insurance_info' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'emergency_contact' => [
                'type' => 'JSON',
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
        $this->forge->addKey(['admission_date', 'status']);
        $this->forge->addForeignKey('patient_id', 'patients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('doctor_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('department_id', 'departments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('room_id', 'rooms', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('admitted_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('discharged_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('admissions');
    }

    public function down()
    {
        $this->forge->dropTable('admissions');
    }
}
