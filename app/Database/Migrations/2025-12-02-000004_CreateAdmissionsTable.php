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
            'consultation_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Links to consultations table - the visit that led to admission',
            ],
            'patient_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'room_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'bed_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'room_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'e.g., Private, Semi-Private, Ward',
            ],
            'admission_reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'attending_physician_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
                'comment' => 'Doctor ID who is attending',
            ],
            'initial_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'admission_date' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'admitted', 'discharged', 'cancelled'],
                'default' => 'pending',
                'null' => false,
            ],
            'requested_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'User ID who requested admission (usually doctor)',
            ],
            'processed_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'User ID who processed admission (nurse or receptionist)',
            ],
            'discharge_date' => [
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('consultation_id');
        $this->forge->addKey('patient_id');
        $this->forge->addKey('room_id');
        $this->forge->addKey('attending_physician_id');
        $this->forge->addForeignKey('consultation_id', 'consultations', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('patient_id', 'admin_patients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('room_id', 'rooms', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('attending_physician_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('admissions');
    }

    public function down()
    {
        $this->forge->dropTable('admissions');
    }
}

