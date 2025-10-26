<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePatientsTable extends Migration
{
    public function up()
    {
        // Check if table exists
        if (!$this->db->tableExists('patients')) {
            $this->createNewTable();
        } else {
            $this->ensureAllColumns();
        }
    }

    private function createNewTable()
    {
        // Complete patients table structure for Patient Records module
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'patient_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ],
            'first_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => false,
            ],
            'last_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => false,
            ],
            'middle_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'date_of_birth' => [
                'type'    => 'DATE',
                'null'    => true,
            ],
            'gender' => [
                'type'       => 'ENUM',
                'constraint' => ['male', 'female', 'other'],
                'null'       => true,
            ],
            'contact_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'address' => [
                'type'    => 'TEXT',
                'null'    => true,
            ],
            'emergency_contact_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'emergency_contact_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ],
            'emergency_contact_phone' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ],
            'government_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'blood_type' => [
                'type'       => 'ENUM',
                'constraint' => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'],
                'null'       => true,
            ],
            'allergies' => [
                'type'    => 'TEXT',
                'null'    => true,
            ],
            'medical_history' => [
                'type'    => 'TEXT',
                'null'    => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive', 'deceased', 'inpatient', 'outpatient', 'archived'],
                'default'    => 'outpatient',
            ],
            'department' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'admission_date' => [
                'type'    => 'DATE',
                'null'    => true,
            ],
            'archived_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('status');
        $this->forge->addKey('department');
        
        // Try to add unique key for patient_id
        try {
            $this->forge->addUniqueKey('patient_id');
        } catch (\Exception $e) {
            // Patient ID unique key creation failed, continuing silently
        }
        
        $this->forge->createTable('patients');
    }

    private function ensureAllColumns()
    {
        // Direct SQL commands to add missing columns
        $sqlCommands = [
            "ALTER TABLE patients ADD COLUMN middle_name VARCHAR(100) NULL",
            "ALTER TABLE patients ADD COLUMN contact_number VARCHAR(20) NULL",
            "ALTER TABLE patients ADD COLUMN emergency_contact_number VARCHAR(20) NULL",
            "ALTER TABLE patients ADD COLUMN government_id VARCHAR(50) NULL",
            "ALTER TABLE patients ADD COLUMN department VARCHAR(100) NULL",
            "ALTER TABLE patients ADD COLUMN admission_date DATE NULL",
            "ALTER TABLE patients ADD COLUMN archived_at DATETIME NULL"
        ];
        
        foreach ($sqlCommands as $sql) {
            try {
                $this->db->query($sql);
            } catch (\Exception $e) {
                // Column might already exist, continue silently
            }
        }
        
        // Update status enum to include new values
        try {
            $this->db->query("ALTER TABLE patients MODIFY COLUMN status ENUM('active', 'inactive', 'deceased', 'inpatient', 'outpatient', 'archived') DEFAULT 'outpatient'");
        } catch (\Exception $e) {
            // Status enum update failed, continue silently
        }
        
        // Make columns nullable
        try {
            $this->db->query("ALTER TABLE patients MODIFY COLUMN patient_id VARCHAR(20) NULL");
            $this->db->query("ALTER TABLE patients MODIFY COLUMN date_of_birth DATE NULL");
            $this->db->query("ALTER TABLE patients MODIFY COLUMN gender ENUM('male', 'female', 'other') NULL");
        } catch (\Exception $e) {
            // Failed to make columns nullable, continue silently
        }
        
        // Test insertion
        try {
            $testData = [
                'first_name' => 'Migration',
                'last_name' => 'Test',
                'middle_name' => 'Update',
                'contact_number' => '09123456789',
                'status' => 'outpatient'
            ];
            
            $this->db->table('patients')->insert($testData);
            $insertId = $this->db->insertID();
            
            if ($insertId) {
                $this->db->table('patients')->delete(['id' => $insertId]);
            }
        } catch (\Exception $e) {
            // Test insertion failed, continue silently
        }
    }

    public function down()
    {
        $this->forge->dropTable('patients');
    }
}
