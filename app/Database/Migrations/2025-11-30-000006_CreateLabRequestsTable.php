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
                'null' => true,
            ],
            'nurse_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'test_type' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'test_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'requested_by' => [
                'type' => 'ENUM',
                'constraint' => ['doctor', 'nurse'],
                'default' => 'doctor',
            ],
            'priority' => [
                'type' => 'ENUM',
                'constraint' => ['routine', 'urgent', 'stat'],
                'default' => 'routine',
            ],
            'instructions' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'in_progress', 'completed', 'cancelled'],
                'default' => 'pending',
            ],
            'requested_date' => [
                'type' => 'DATE',
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
        $this->forge->addForeignKey('doctor_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('nurse_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('lab_requests');
    }

    public function down()
    {
        $this->forge->dropTable('lab_requests');
    }
}

