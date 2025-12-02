<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLabTestsTable extends Migration
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
            'test_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'test_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'e.g., Hematology, Chemistry, Microbiology, etc.',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'normal_range' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Normal reference range',
            ],
            'price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('test_type');
        $this->forge->addKey('is_active');
        $this->forge->createTable('lab_tests');
    }

    public function down()
    {
        $this->forge->dropTable('lab_tests');
    }
}


