<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReceptionistsTable extends Migration
{
    public function up()
    {
        // Check if table already exists
        if ($this->db->tableExists('receptionists')) {
            echo "Receptionists table already exists, skipping creation...\n";
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
            'shift_type' => [
                'type'       => 'ENUM',
                'constraint' => ['morning', 'afternoon', 'night', 'rotating'],
                'default'    => 'morning',
            ],
            'shift_start' => [
                'type'    => 'TIME',
                'default' => '08:00:00',
            ],
            'shift_end' => [
                'type'    => 'TIME',
                'default' => '16:00:00',
            ],
            'desk_location' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'languages_spoken' => [
                'type'    => 'VARCHAR',
                'constraint' => '200',
                'default' => 'English,Filipino',
            ],
            'access_level' => [
                'type'       => 'ENUM',
                'constraint' => ['basic', 'advanced', 'supervisor'],
                'default'    => 'basic',
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
        $this->forge->createTable('receptionists');
    }

    public function down()
    {
        $this->forge->dropTable('receptionists');
    }
}
