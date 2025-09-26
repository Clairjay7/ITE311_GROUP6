<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLabSamplesTable extends Migration
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
            'sample_number' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'unique' => true,
            ],
            'sample_type' => [
                'type' => 'ENUM',
                'constraint' => ['blood', 'urine', 'stool', 'sputum', 'tissue', 'swab', 'other'],
                'default' => 'other',
            ],
            'collection_date' => [
                'type' => 'DATETIME',
            ],
            'collected_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'collection_method' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'volume_amount' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'storage_location' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'storage_temperature' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'expiry_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['collected', 'stored', 'processing', 'tested', 'disposed'],
                'default' => 'collected',
            ],
            'quality_check' => [
                'type' => 'ENUM',
                'constraint' => ['acceptable', 'rejected', 'contaminated'],
                'default' => 'acceptable',
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
        $this->forge->addKey(['lab_request_id', 'status']);
        $this->forge->addForeignKey('lab_request_id', 'lab_requests', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('collected_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('lab_samples');
    }

    public function down()
    {
        $this->forge->dropTable('lab_samples');
    }
}
