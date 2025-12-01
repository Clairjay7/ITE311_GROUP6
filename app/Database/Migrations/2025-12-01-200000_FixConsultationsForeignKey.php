<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixConsultationsForeignKey extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Drop the existing foreign key constraint and add new one
        if ($db->tableExists('consultations')) {
            try {
                // Get the current foreign key constraint name
                $fkQuery = $db->query("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'consultations' 
                    AND COLUMN_NAME = 'patient_id' 
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ");
                
                $fkResult = $fkQuery->getRowArray();
                if ($fkResult) {
                    $fkName = $fkResult['CONSTRAINT_NAME'];
                    // Drop the existing foreign key constraint
                    $db->query("ALTER TABLE consultations DROP FOREIGN KEY `{$fkName}`");
                }
            } catch (\Exception $e) {
                // Foreign key might not exist, continue anyway
                log_message('debug', 'Foreign key drop attempt: ' . $e->getMessage());
            }
            
            // Add new foreign key constraint to admin_patients.id
            try {
                $db->query("ALTER TABLE consultations 
                    ADD CONSTRAINT consultations_patient_id_foreign 
                    FOREIGN KEY (patient_id) REFERENCES admin_patients(id) 
                    ON DELETE CASCADE ON UPDATE CASCADE");
            } catch (\Exception $e) {
                // Foreign key might already exist with different name
                log_message('debug', 'Foreign key add attempt: ' . $e->getMessage());
            }
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        
        if ($db->tableExists('consultations')) {
            // Drop the admin_patients foreign key
            $this->db->query("ALTER TABLE consultations DROP FOREIGN KEY IF EXISTS consultations_patient_id_foreign");
            
            // Restore original foreign key to patients.patient_id
            $this->db->query("ALTER TABLE consultations 
                ADD CONSTRAINT consultations_patient_id_foreign 
                FOREIGN KEY (patient_id) REFERENCES patients(patient_id) 
                ON DELETE CASCADE ON UPDATE CASCADE");
        }
    }
}

