<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAppointmentsTable extends Migration
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
                'null' => true,
            ],
            'patient_ref' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
                'comment' => 'fallback: name or ID reference when patient_id not used',
            ],
            'department' => [
                'type' => 'VARCHAR',
                'constraint' => 120,
                'null' => true,
            ],
            'doctor' => [
                'type' => 'VARCHAR',
                'constraint' => 120,
                'null' => true,
            ],
            'appointment_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'appointment_time' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 40,
                'default' => 'Scheduled',
                'comment' => 'Scheduled|Completed|Canceled|No-Show',
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
        // Optional foreign key if patients table exists with id patient_id
        // $this->forge->addForeignKey('patient_id', 'patients', 'patient_id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('appointments');
    }

    public function down()
    {
        $this->forge->dropTable('appointments');
    }
}
