<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateItStaffTable extends Migration
{
    public function up()
    {
        // Check if table already exists
        if ($this->db->tableExists('it_staff')) {
            echo "IT Staff table already exists, skipping creation...\n";
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
            'employee_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'department' => [
                'type'       => 'ENUM',
                'constraint' => ['network', 'security', 'database', 'support', 'development', 'general'],
                'default'    => 'general',
            ],
            'access_level' => [
                'type'       => 'ENUM',
                'constraint' => ['technician', 'analyst', 'administrator', 'manager'],
                'default'    => 'technician',
            ],
            'specializations' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'years_experience' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 0,
            ],
            'system_admin_access' => [
                'type'    => 'BOOLEAN',
                'default' => false,
            ],
            'network_access' => [
                'type'    => 'BOOLEAN',
                'default' => false,
            ],
            'database_access' => [
                'type'    => 'BOOLEAN',
                'default' => false,
            ],
            'security_clearance' => [
                'type'       => 'ENUM',
                'constraint' => ['basic', 'intermediate', 'advanced', 'full'],
                'default'    => 'basic',
            ],
            'on_call_schedule' => [
                'type'    => 'BOOLEAN',
                'default' => false,
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
            $this->forge->addUniqueKey('employee_id');
        } catch (\Exception $e) {
            echo "Employee ID unique key already exists, skipping...\n";
        }
        
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('it_staff');
    }

    public function down()
    {
        $this->forge->dropTable('it_staff');
    }
}
