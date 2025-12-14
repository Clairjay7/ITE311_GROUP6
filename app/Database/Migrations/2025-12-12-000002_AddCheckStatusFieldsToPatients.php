<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCheckStatusFieldsToPatients extends Migration
{
    public function up()
    {
        // Add doctor_check_status to admin_patients table
        if ($this->db->tableExists('admin_patients')) {
            $this->forge->addColumn('admin_patients', [
                'doctor_check_status' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'default' => 'available',
                    'null' => false,
                    'comment' => 'Status: available (doctor can check), pending_nurse (waiting for nurse to complete vitals)',
                ],
                'nurse_vital_status' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'default' => 'not_required',
                    'null' => false,
                    'comment' => 'Status: not_required, pending, completed',
                ],
            ]);
        }

        // Add doctor_check_status to patients table
        if ($this->db->tableExists('patients')) {
            $this->forge->addColumn('patients', [
                'doctor_check_status' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'default' => 'available',
                    'null' => false,
                    'comment' => 'Status: available (doctor can check), pending_nurse (waiting for nurse to complete vitals)',
                ],
                'nurse_vital_status' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'default' => 'not_required',
                    'null' => false,
                    'comment' => 'Status: not_required, pending, completed',
                ],
            ]);
        }
    }

    public function down()
    {
        // Remove fields from admin_patients table
        if ($this->db->tableExists('admin_patients')) {
            if ($this->db->fieldExists('doctor_check_status', 'admin_patients')) {
                $this->forge->dropColumn('admin_patients', 'doctor_check_status');
            }
            if ($this->db->fieldExists('nurse_vital_status', 'admin_patients')) {
                $this->forge->dropColumn('admin_patients', 'nurse_vital_status');
            }
        }

        // Remove fields from patients table
        if ($this->db->tableExists('patients')) {
            if ($this->db->fieldExists('doctor_check_status', 'patients')) {
                $this->forge->dropColumn('patients', 'doctor_check_status');
            }
            if ($this->db->fieldExists('nurse_vital_status', 'patients')) {
                $this->forge->dropColumn('patients', 'nurse_vital_status');
            }
        }
    }
}

