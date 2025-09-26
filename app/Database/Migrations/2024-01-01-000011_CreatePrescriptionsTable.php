<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePrescriptionsTable extends Migration
{
    public function up()
    {
        // Check if table already exists
        if ($this->db->tableExists('prescriptions')) {
            echo "Prescriptions table already exists, skipping creation...\n";
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'prescription_id' => [
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
            'medication_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '200',
            ],
            'dosage' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'frequency' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'duration' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'instructions' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'quantity' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 1,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'dispensed', 'cancelled'],
                'default'    => 'pending',
            ],
            'dispensed_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'dispensed_at' => [
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
            $this->forge->addUniqueKey('prescription_id');
        } catch (\Exception $e) {
            echo "Prescription ID unique key already exists, skipping...\n";
        }
        
        $this->forge->addKey(['patient_id', 'doctor_id']);
        $this->forge->addKey('status');
        $this->forge->addForeignKey('patient_id', 'patients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('doctor_id', 'doctors', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('dispensed_by', 'pharmacists', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('prescriptions');
    }

    public function down()
    {
        $this->forge->dropTable('prescriptions');
    }
}
