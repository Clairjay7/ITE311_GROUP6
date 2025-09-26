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
            'lab_sample_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'test_name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'test_parameter' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'result_value' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'unit' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'reference_range' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'flag' => [
                'type' => 'ENUM',
                'constraint' => ['normal', 'high', 'low', 'critical', 'abnormal'],
                'default' => 'normal',
            ],
            'tested_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'tested_at' => [
                'type' => 'DATETIME',
            ],
            'validated_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'validated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'equipment_used' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'method' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'comments' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'completed', 'validated', 'reported'],
                'default' => 'pending',
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
        $this->forge->addKey(['lab_request_id', 'status']);
        $this->forge->addForeignKey('lab_request_id', 'lab_requests', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('lab_sample_id', 'lab_samples', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('tested_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('validated_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('lab_results');
    }

    public function down()
    {
        $this->forge->dropTable('lab_results');
    }
}
