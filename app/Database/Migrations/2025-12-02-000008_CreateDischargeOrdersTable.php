<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDischargeOrdersTable extends Migration
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
            'admission_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'patient_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'doctor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
                'comment' => 'Doctor who issued the discharge order',
            ],
            'final_diagnosis' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'treatment_summary' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'recommendations' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'follow_up_instructions' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'medications_prescribed' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON or text list of medications',
            ],
            'discharge_date' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Planned discharge date/time',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'approved', 'completed', 'cancelled'],
                'default' => 'pending',
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
        $this->forge->addKey('admission_id');
        $this->forge->addKey('patient_id');
        $this->forge->addKey('doctor_id');
        $this->forge->addForeignKey('admission_id', 'admissions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('patient_id', 'admin_patients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('doctor_id', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('discharge_orders');
    }

    public function down()
    {
        $this->forge->dropTable('discharge_orders');
    }
}

