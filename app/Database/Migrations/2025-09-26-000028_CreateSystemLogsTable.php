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
            'log_level' => [
                'type' => 'ENUM',
                'constraint' => ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'],
                'default' => 'info',
            ],
            'category' => [
                'type' => 'ENUM',
                'constraint' => ['authentication', 'database', 'application', 'security', 'performance', 'backup', 'system'],
                'default' => 'application',
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'context' => [
                'type' => 'JSON',
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
                'type' => 'TEXT',
                'null' => true,
            ],
            'request_uri' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'request_method' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'response_code' => [
                'type' => 'INT',
                'constraint' => 3,
                'null' => true,
            ],
            'execution_time' => [
                'type' => 'DECIMAL',
                'constraint' => '8,4',
                'null' => true,
            ],
            'memory_usage' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
            ],
            'session_id' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['log_level', 'created_at']);
        $this->forge->addKey(['category', 'created_at']);
        $this->forge->addKey(['user_id', 'created_at']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('system_logs');
    }

    public function down()
    {
        $this->forge->dropTable('system_logs');
    }
}
