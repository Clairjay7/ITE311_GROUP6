<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDoctorSchedulesTable extends Migration
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
            'doctor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'shift_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'start_time' => [
                'type' => 'TIME',
                'null' => false,
            ],
            'end_time' => [
                'type' => 'TIME',
                'null' => false,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'active',
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
        $this->forge->addKey('doctor_id');
        $this->forge->addKey('shift_date');
        $this->forge->addKey(['doctor_id', 'shift_date']);
        
        // Add foreign key to users table (doctors)
        $this->forge->addForeignKey('doctor_id', 'users', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('doctor_schedules');
    }

    public function down()
    {
        $this->forge->dropTable('doctor_schedules');
    }
}

