<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMedicalServicesTable extends Migration
{
    public function up()
    {
        // Check if table already exists
        if ($this->db->tableExists('medical_services')) {
            echo "Medical services table already exists, skipping creation...\n";
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'service_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'category' => [
                'type'       => 'ENUM',
                'constraint' => ['consultation', 'laboratory', 'imaging', 'surgery', 'therapy', 'emergency', 'other'],
                'default'    => 'consultation',
            ],
            'price' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive', 'discontinued'],
                'default'    => 'active',
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
        $this->forge->addKey('category');
        $this->forge->addKey('status');
        $this->forge->createTable('medical_services');

        // Insert sample data
        $data = [
            [
                'service_name' => 'General Consultation',
                'description' => 'Basic medical consultation with a general practitioner',
                'category' => 'consultation',
                'price' => 500.00,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'service_name' => 'Complete Blood Count (CBC)',
                'description' => 'Comprehensive blood test to evaluate overall health',
                'category' => 'laboratory',
                'price' => 350.00,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'service_name' => 'X-Ray Chest',
                'description' => 'Chest X-ray imaging for respiratory evaluation',
                'category' => 'imaging',
                'price' => 800.00,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'service_name' => 'ECG (Electrocardiogram)',
                'description' => 'Heart rhythm and electrical activity test',
                'category' => 'laboratory',
                'price' => 450.00,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'service_name' => 'Physical Therapy Session',
                'description' => 'Individual physical therapy treatment session',
                'category' => 'therapy',
                'price' => 600.00,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'service_name' => 'Emergency Room Visit',
                'description' => 'Emergency medical care and evaluation',
                'category' => 'emergency',
                'price' => 1500.00,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('medical_services')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('medical_services');
    }
}
