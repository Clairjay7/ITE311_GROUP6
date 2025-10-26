<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAppointmentsTable extends Migration
{
    public function up()
    {
        // Check if table already exists
        if ($this->db->tableExists('appointments')) {
            echo "Appointments table already exists, updating structure...\n";
            $this->updateExistingTable();
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'appointment_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ],
            'patient_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'patient_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'patient_phone' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ],
            'doctor_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'doctor_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'department' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'appointment_date' => [
                'type' => 'DATE',
            ],
            'appointment_time' => [
                'type' => 'TIME',
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['consultation', 'follow_up', 'emergency', 'surgery', 'checkup'],
                'default'    => 'consultation',
            ],
            'appointment_type' => [
                'type'       => 'ENUM',
                'constraint' => ['consultation', 'follow-up', 'emergency', 'surgery', 'therapy'],
                'default'    => 'consultation',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'scheduled', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'],
                'default'    => 'pending',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
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
        
        // Try to add unique key, but don't fail if it exists
        try {
            $this->forge->addUniqueKey('appointment_id');
        } catch (\Exception $e) {
            echo "Appointment ID unique key already exists, skipping...\n";
        }
        
        $this->forge->addKey(['patient_id', 'doctor_id']);
        $this->forge->addKey('appointment_date');
        
        // Add foreign keys only if the referenced tables exist
        try {
            if ($this->db->tableExists('patients')) {
                $this->forge->addForeignKey('patient_id', 'patients', 'id', 'SET NULL', 'CASCADE');
            }
            if ($this->db->tableExists('doctors')) {
                $this->forge->addForeignKey('doctor_id', 'doctors', 'id', 'SET NULL', 'CASCADE');
            }
            if ($this->db->tableExists('users')) {
                $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'CASCADE');
            }
        } catch (\Exception $e) {
            echo "Foreign key creation failed (tables may not exist): " . $e->getMessage() . "\n";
        }
        
        $this->forge->createTable('appointments');
    }

    public function down()
    {
        $this->forge->dropTable('appointments');
    }

    private function updateExistingTable()
    {
        echo "Adding missing columns to existing appointments table...\n";

        // Add patient_name column
        if (!$this->db->fieldExists('patient_name', 'appointments')) {
            $this->forge->addColumn('appointments', [
                'patient_name' => [
                    'type' => 'VARCHAR',
                    'constraint' => '255',
                    'null' => true,
                    'after' => 'patient_id'
                ]
            ]);
            echo "Added patient_name column\n";
        }

        // Add patient_phone column
        if (!$this->db->fieldExists('patient_phone', 'appointments')) {
            $this->forge->addColumn('appointments', [
                'patient_phone' => [
                    'type' => 'VARCHAR',
                    'constraint' => '20',
                    'null' => true,
                    'after' => 'patient_name'
                ]
            ]);
            echo "Added patient_phone column\n";
        }

        // Add doctor_name column
        if (!$this->db->fieldExists('doctor_name', 'appointments')) {
            $this->forge->addColumn('appointments', [
                'doctor_name' => [
                    'type' => 'VARCHAR',
                    'constraint' => '255',
                    'null' => true,
                    'after' => 'doctor_id'
                ]
            ]);
            echo "Added doctor_name column\n";
        }

        // Add department column
        if (!$this->db->fieldExists('department', 'appointments')) {
            $this->forge->addColumn('appointments', [
                'department' => [
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                    'after' => 'doctor_name'
                ]
            ]);
            echo "Added department column\n";
        }

        // Add appointment_type column
        if (!$this->db->fieldExists('appointment_type', 'appointments')) {
            $this->forge->addColumn('appointments', [
                'appointment_type' => [
                    'type' => 'ENUM',
                    'constraint' => ['consultation', 'follow-up', 'emergency', 'surgery', 'therapy'],
                    'default' => 'consultation',
                    'after' => 'appointment_time'
                ]
            ]);
            echo "Added appointment_type column\n";
        }

        // Update status enum to include 'pending'
        try {
            $this->db->query("ALTER TABLE appointments MODIFY COLUMN status ENUM('pending','scheduled','confirmed','in_progress','completed','cancelled','no_show') DEFAULT 'pending'");
            echo "Updated status enum to include 'pending'\n";
        } catch (\Exception $e) {
            echo "Status enum update failed (may already be updated): " . $e->getMessage() . "\n";
        }

        // Make patient_id and doctor_id nullable
        try {
            $this->db->query("ALTER TABLE appointments MODIFY COLUMN patient_id INT(11) UNSIGNED NULL");
            $this->db->query("ALTER TABLE appointments MODIFY COLUMN doctor_id INT(11) UNSIGNED NULL");
            echo "Made patient_id and doctor_id nullable\n";
        } catch (\Exception $e) {
            echo "Failed to make ID columns nullable: " . $e->getMessage() . "\n";
        }

        echo "Appointments table structure update completed!\n";
    }
}
