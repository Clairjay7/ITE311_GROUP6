<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSystemLogsTable extends Migration
{
    public function up()
    {
        // Check if table already exists
        if ($this->db->tableExists('system_logs')) {
            echo "System logs table already exists, skipping creation...\n";
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
                'null'       => true,
            ],
            'action' => [
                'type'       => 'VARCHAR',
                'constraint' => '200',
            ],
            'module' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'level' => [
                'type'       => 'ENUM',
                'constraint' => ['info', 'warning', 'error', 'critical'],
                'default'    => 'info',
            ],
            'message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => '45',
                'null'       => true,
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id', 'created_at']);
        $this->forge->addKey('level');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('system_logs');
    }

    public function down()
    {
        $this->forge->dropTable('system_logs');
    }
}
