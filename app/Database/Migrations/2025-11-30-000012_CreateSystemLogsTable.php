<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSystemLogsTable extends Migration
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
            'level' => [
                'type' => 'ENUM',
                'constraint' => ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'],
                'default' => 'info',
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'context' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
            ],
            'user_agent' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'module' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'action' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('level');
        $this->forge->addKey('created_at');
        $this->forge->addKey('user_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('system_logs');
    }

    public function down()
    {
        $this->forge->dropTable('system_logs');
    }
}

