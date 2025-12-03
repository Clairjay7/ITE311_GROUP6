<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveTriagePatientForeignKey extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Drop the foreign key constraint on patient_id
        // This allows triage to support both patients and admin_patients tables
        if ($db->tableExists('triage')) {
            try {
                // Get the foreign key constraint name
                $fkQuery = $db->query("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'triage' 
                    AND COLUMN_NAME = 'patient_id' 
                    AND REFERENCED_TABLE_NAME = 'patients'
                    AND REFERENCED_COLUMN_NAME = 'patient_id'
                ");
                
                $fkResult = $fkQuery->getRowArray();
                if ($fkResult) {
                    $fkName = $fkResult['CONSTRAINT_NAME'];
                    // Drop the foreign key constraint
                    $db->query("ALTER TABLE triage DROP FOREIGN KEY `{$fkName}`");
                }
            } catch (\Exception $e) {
                // Foreign key might not exist or already dropped
                log_message('debug', 'Foreign key drop attempt: ' . $e->getMessage());
            }
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        
        // Restore foreign key constraint (optional - usually not needed)
        if ($db->tableExists('triage')) {
            try {
                $db->query("ALTER TABLE triage 
                    ADD CONSTRAINT triage_patient_id_foreign 
                    FOREIGN KEY (patient_id) REFERENCES patients(patient_id) 
                    ON DELETE CASCADE ON UPDATE CASCADE");
            } catch (\Exception $e) {
                log_message('debug', 'Foreign key restore attempt: ' . $e->getMessage());
            }
        }
    }
}

