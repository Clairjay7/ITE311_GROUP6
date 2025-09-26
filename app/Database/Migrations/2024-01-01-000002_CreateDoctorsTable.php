<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDoctorsTable extends Migration
{
    public function up()
    {
        // Check if table already exists
        if ($this->db->tableExists('doctors')) {
            echo "Doctors table already exists, skipping creation...\n";
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'license_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'specialization' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'department' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'consultation_fee' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'schedule_start' => [
                'type'    => 'TIME',
                'default' => '08:00:00',
            ],
            'schedule_end' => [
                'type'    => 'TIME',
                'default' => '17:00:00',
            ],
            'available_days' => [
                'type'    => 'VARCHAR',
                'constraint' => '50',
                'default' => 'Monday,Tuesday,Wednesday,Thursday,Friday',
            ],
            'room_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ],
            'years_experience' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 0,
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
        $this->forge->addKey('user_id');
        
        // Try to add unique key, but don't fail if it exists
        try {
            $this->forge->addUniqueKey('license_number');
        } catch (\Exception $e) {
            echo "License number unique key already exists, skipping...\n";
        }
        
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('doctors');
    }

    public function down()
    {
        $this->forge->dropTable('doctors');
    }
}
