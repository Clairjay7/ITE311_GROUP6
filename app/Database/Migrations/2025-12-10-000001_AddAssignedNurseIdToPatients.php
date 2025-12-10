<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAssignedNurseIdToPatients extends Migration
{
    public function up()
    {
        // Add assigned_nurse_id to admin_patients table
        if ($this->db->tableExists('admin_patients')) {
            $fields = [
                'assigned_nurse_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'after' => 'doctor_id',
                ],
            ];
            
            // Check if column doesn't already exist
            if (!$this->db->fieldExists('assigned_nurse_id', 'admin_patients')) {
                $this->forge->addColumn('admin_patients', $fields);
                
                // Add foreign key constraint if users table exists
                if ($this->db->tableExists('users')) {
                    $this->forge->addForeignKey('assigned_nurse_id', 'users', 'id', 'SET NULL', 'CASCADE', 'admin_patients_nurse_fk');
                }
            }
        }
        
        // Add assigned_nurse_id to patients table
        if ($this->db->tableExists('patients')) {
            $fields = [
                'assigned_nurse_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'after' => 'doctor_id',
                ],
            ];
            
            // Check if column doesn't already exist
            if (!$this->db->fieldExists('assigned_nurse_id', 'patients')) {
                $this->forge->addColumn('patients', $fields);
                
                // Add foreign key constraint if users table exists
                if ($this->db->tableExists('users')) {
                    $this->forge->addForeignKey('assigned_nurse_id', 'users', 'id', 'SET NULL', 'CASCADE', 'patients_nurse_fk');
                }
            }
        }
        
        // Add assigned_nurse_id to admission_requests table if it exists
        if ($this->db->tableExists('admission_requests')) {
            $fields = [
                'assigned_nurse_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'after' => 'approved_by',
                ],
            ];
            
            // Check if column doesn't already exist
            if (!$this->db->fieldExists('assigned_nurse_id', 'admission_requests')) {
                $this->forge->addColumn('admission_requests', $fields);
                
                // Add foreign key constraint if users table exists
                if ($this->db->tableExists('users')) {
                    $this->forge->addForeignKey('assigned_nurse_id', 'users', 'id', 'SET NULL', 'CASCADE', 'admission_requests_nurse_fk');
                }
            }
        }
    }

    public function down()
    {
        // Drop foreign keys first
        if ($this->db->tableExists('admin_patients')) {
            try {
                $this->forge->dropForeignKey('admin_patients', 'admin_patients_nurse_fk');
            } catch (\Exception $e) {
                // Foreign key might not exist
            }
            if ($this->db->fieldExists('assigned_nurse_id', 'admin_patients')) {
                $this->forge->dropColumn('admin_patients', 'assigned_nurse_id');
            }
        }
        
        if ($this->db->tableExists('patients')) {
            try {
                $this->forge->dropForeignKey('patients', 'patients_nurse_fk');
            } catch (\Exception $e) {
                // Foreign key might not exist
            }
            if ($this->db->fieldExists('assigned_nurse_id', 'patients')) {
                $this->forge->dropColumn('patients', 'assigned_nurse_id');
            }
        }
        
        if ($this->db->tableExists('admission_requests')) {
            try {
                $this->forge->dropForeignKey('admission_requests', 'admission_requests_nurse_fk');
            } catch (\Exception $e) {
                // Foreign key might not exist
            }
            if ($this->db->fieldExists('assigned_nurse_id', 'admission_requests')) {
                $this->forge->dropColumn('admission_requests', 'assigned_nurse_id');
            }
        }
    }
}

