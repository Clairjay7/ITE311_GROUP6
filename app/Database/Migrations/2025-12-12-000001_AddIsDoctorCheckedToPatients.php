<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsDoctorCheckedToPatients extends Migration
{
    public function up()
    {
        // Add is_doctor_checked to admin_patients table
        if ($this->db->tableExists('admin_patients')) {
            $this->forge->addColumn('admin_patients', [
                'is_doctor_checked' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0,
                    'null' => false,
                    'comment' => 'Flag to indicate if doctor has checked the patient (required before nurse can check vitals)',
                ],
            ]);
        }

        // Add is_doctor_checked to patients table
        if ($this->db->tableExists('patients')) {
            $this->forge->addColumn('patients', [
                'is_doctor_checked' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0,
                    'null' => false,
                    'comment' => 'Flag to indicate if doctor has checked the patient (required before nurse can check vitals)',
                ],
            ]);
        }
    }

    public function down()
    {
        // Remove is_doctor_checked from admin_patients table
        if ($this->db->tableExists('admin_patients')) {
            if ($this->db->fieldExists('is_doctor_checked', 'admin_patients')) {
                $this->forge->dropColumn('admin_patients', 'is_doctor_checked');
            }
        }

        // Remove is_doctor_checked from patients table
        if ($this->db->tableExists('patients')) {
            if ($this->db->fieldExists('is_doctor_checked', 'patients')) {
                $this->forge->dropColumn('patients', 'is_doctor_checked');
            }
        }
    }
}

