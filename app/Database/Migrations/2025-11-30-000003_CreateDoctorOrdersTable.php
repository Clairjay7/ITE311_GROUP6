<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDoctorOrdersTable extends Migration
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
            'order_type' => [
                'type' => 'ENUM',
                'constraint' => ['medication', 'lab_test', 'procedure', 'diet', 'activity', 'other'],
                'default' => 'medication',
            ],
            'order_description' => [
                'type' => 'TEXT',
            ],
            'instructions' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'frequency' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'start_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'end_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'in_progress', 'completed', 'cancelled'],
                'default' => 'pending',
            ],
            'completed_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'completed_at' => [
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
        $this->forge->addForeignKey('doctor_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('completed_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('doctor_orders');
    }

    public function down()
    {
        $this->forge->dropTable('doctor_orders');
    }
}

