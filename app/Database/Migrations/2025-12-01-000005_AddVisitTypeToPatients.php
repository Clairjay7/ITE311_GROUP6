<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVisitTypeToPatients extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Add visit_type column to patients table if it doesn't exist
        if ($this->db->tableExists('patients')) {
            $fields = $db->getFieldData('patients');
            $hasVisitType = false;
            $hasTriageStatus = false;
            foreach ($fields as $field) {
                if ($field->name === 'visit_type') $hasVisitType = true;
                if ($field->name === 'triage_status') $hasTriageStatus = true;
            }
            
            if (!$hasVisitType) {
                $this->forge->addColumn('patients', [
                    'visit_type' => [
                        'type' => 'VARCHAR',
                        'constraint' => 50,
                        'null' => true,
                        'after' => 'type',
                        'comment' => 'Consultation, Check-up, Follow-up, Emergency'
                    ],
                ]);
            }
            
            if (!$hasTriageStatus) {
                $this->forge->addColumn('patients', [
                    'triage_status' => [
                        'type' => 'VARCHAR',
                        'constraint' => 50,
                        'null' => true,
                        'default' => 'pending',
                        'after' => 'visit_type',
                        'comment' => 'pending, triaged, critical, moderate, minor'
                    ],
                ]);
            }
        }

        // Add visit_type column to admin_patients table if it exists
        if ($this->db->tableExists('admin_patients')) {
            $fields = $db->getFieldData('admin_patients');
            $hasVisitType = false;
            $hasTriageStatus = false;
            foreach ($fields as $field) {
                if ($field->name === 'visit_type') $hasVisitType = true;
                if ($field->name === 'triage_status') $hasTriageStatus = true;
            }
            
            if (!$hasVisitType) {
                $this->forge->addColumn('admin_patients', [
                    'visit_type' => [
                        'type' => 'VARCHAR',
                        'constraint' => 50,
                        'null' => true,
                    ],
                ]);
            }
            
            if (!$hasTriageStatus) {
                $this->forge->addColumn('admin_patients', [
                    'triage_status' => [
                        'type' => 'VARCHAR',
                        'constraint' => 50,
                        'null' => true,
                        'default' => 'pending',
                    ],
                ]);
            }
        }
    }

    public function down()
    {
        $this->forge->dropColumn('patients', ['visit_type', 'triage_status']);
        if ($this->db->tableExists('admin_patients')) {
            $this->forge->dropColumn('admin_patients', ['visit_type', 'triage_status']);
        }
    }
}

