<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLabRequestsTable extends Migration
{
    public function up()
    {
        // Check if table already exists
        if ($this->db->tableExists('lab_requests')) {
            echo "Lab requests table already exists, skipping creation...\n";
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'request_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'patient_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'doctor_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'test_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '200',
            ],
            'test_category' => [
                'type'       => 'ENUM',
                'constraint' => ['blood', 'urine', 'imaging', 'biopsy', 'culture', 'other'],
                'default'    => 'blood',
            ],
            'priority' => [
                'type'       => 'ENUM',
                'constraint' => ['routine', 'urgent', 'stat'],
                'default'    => 'routine',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'in_progress', 'completed', 'cancelled'],
                'default'    => 'pending',
            ],
            'instructions' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'results' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'processed_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
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
        
        // Try to add unique key, but don't fail if it exists
        try {
            $this->forge->addUniqueKey('request_id');
        } catch (\Exception $e) {
            echo "Request ID unique key already exists, skipping...\n";
        }
        
        $this->forge->addKey(['patient_id', 'doctor_id']);
        $this->forge->addKey('status');
        $this->forge->addForeignKey('patient_id', 'patients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('doctor_id', 'doctors', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('processed_by', 'laboratories', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('lab_requests');
    }

    public function down()
    {
        $this->forge->dropTable('lab_requests');
    }
}
