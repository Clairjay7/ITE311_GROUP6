<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNurseSchedulesTable extends Migration
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
            'nurse_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'shift_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'shift_type' => [
                'type' => 'ENUM',
                'constraint' => ['morning', 'night'],
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
                'type' => 'ENUM',
                'constraint' => ['active', 'cancelled', 'on_leave'],
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
        $this->forge->addKey('nurse_id');
        $this->forge->addKey('shift_date');
        $this->forge->addKey(['nurse_id', 'shift_date']);
        $this->forge->addKey(['nurse_id', 'shift_date', 'shift_type']);
        
        // Add foreign key to users table (nurses)
        $this->forge->addForeignKey('nurse_id', 'users', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('nurse_schedules');
    }

    public function down()
    {
        $this->forge->dropTable('nurse_schedules');
    }
}
