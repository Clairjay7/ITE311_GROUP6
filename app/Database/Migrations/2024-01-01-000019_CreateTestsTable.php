<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTestsTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('tests')) {
            echo "Tests table already exists, skipping creation..." . PHP_EOL;
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'patient_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'test_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'test_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'sample_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'requested_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'in_progress', 'completed'],
                'default'    => 'pending',
            ],
            'result' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'quality_check' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
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
        $this->forge->addKey('patient_id');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('patient_id', 'patients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tests');
    }

    public function down()
    {
        if ($this->db->fieldExists('patient_id', 'tests')) {
            $this->forge->dropForeignKey('tests', 'tests_patient_id_foreign');
        }

        $this->forge->dropTable('tests', true);
    }
}
