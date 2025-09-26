<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNursesTable extends Migration
{
    public function up()
    {
        // Check if table already exists
        if ($this->db->tableExists('nurses')) {
            echo "Nurses table already exists, skipping creation...\n";
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
            'assigned_ward' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'shift_type' => [
                'type'       => 'ENUM',
                'constraint' => ['morning', 'afternoon', 'night', 'rotating'],
                'default'    => 'morning',
            ],
            'shift_start' => [
                'type'    => 'TIME',
                'default' => '07:00:00',
            ],
            'shift_end' => [
                'type'    => 'TIME',
                'default' => '15:00:00',
            ],
            'supervisor_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'years_experience' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 0,
            ],
            'certifications' => [
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
        $this->forge->addKey('user_id');
        
        // Try to add unique key, but don't fail if it exists
        try {
            $this->forge->addUniqueKey('license_number');
        } catch (\Exception $e) {
            echo "License number unique key already exists, skipping...\n";
        }
        
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('supervisor_id', 'nurses', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('nurses');
    }

    public function down()
    {
        $this->forge->dropTable('nurses');
    }
}
