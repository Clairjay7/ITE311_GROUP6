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
                'constraint' => 255,
            ],
            'backup_type' => [
                'type' => 'ENUM',
                'constraint' => ['database', 'files', 'full'],
                'default' => 'database',
            ],
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
            ],
            'file_size' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'in_progress', 'completed', 'failed'],
                'default' => 'pending',
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
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
        $this->forge->addKey('status');
        $this->forge->addKey('created_at');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('system_backups');
    }

    public function down()
    {
        $this->forge->dropTable('system_backups');
    }
}

