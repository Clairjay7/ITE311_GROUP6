<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSystemBackupsTable extends Migration
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
            'backup_name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'backup_type' => [
                'type' => 'ENUM',
                'constraint' => ['full', 'incremental', 'differential', 'database_only', 'files_only'],
                'default' => 'full',
            ],
            'backup_date' => [
                'type' => 'DATETIME',
            ],
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
            ],
            'file_size' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => true,
            ],
            'compression_type' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['in_progress', 'completed', 'failed', 'corrupted', 'deleted'],
                'default' => 'in_progress',
            ],
            'start_time' => [
                'type' => 'DATETIME',
            ],
            'end_time' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'duration_seconds' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
            ],
            'tables_included' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'error_message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'checksum' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'retention_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'notes' => [
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
        $this->forge->addKey(['backup_date', 'status']);
        $this->forge->addKey(['backup_type', 'status']);
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('system_backups');
    }

    public function down()
    {
        $this->forge->dropTable('system_backups');
    }
}
