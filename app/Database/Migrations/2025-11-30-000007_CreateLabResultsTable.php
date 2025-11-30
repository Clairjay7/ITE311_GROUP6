<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLabResultsTable extends Migration
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
            'lab_request_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'result' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'result_file' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'result_file_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'interpretation' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'completed_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'completed_at' => [
                'type' => 'DATETIME',
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
        $this->forge->addForeignKey('lab_request_id', 'lab_requests', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('completed_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('lab_results');
    }

    public function down()
    {
        $this->forge->dropTable('lab_results');
    }
}

