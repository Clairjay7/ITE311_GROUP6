<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVitalIdToDoctorOrdersAndUpdateWorkflow extends Migration
{
    public function up()
    {
        // Add vital_id to doctor_orders table to link orders to vital records
        if ($this->db->tableExists('doctor_orders')) {
            if (!$this->db->fieldExists('vital_id', 'doctor_orders')) {
                $this->forge->addColumn('doctor_orders', [
                    'vital_id' => [
                        'type' => 'INT',
                        'constraint' => 11,
                        'unsigned' => true,
                        'null' => true,
                        'comment' => 'Link to patient_vitals.id - order created from this vital record',
                    ],
                ]);
            }
        }

        // Update doctor_check_status comment to include new states
        if ($this->db->tableExists('admin_patients')) {
            // The field already exists, we just need to update the comment via ALTER
            // Note: CodeIgniter doesn't have direct comment update, but we can document it
        }

        if ($this->db->tableExists('patients')) {
            // Same for patients table
        }
    }

    public function down()
    {
        // Remove vital_id from doctor_orders table
        if ($this->db->tableExists('doctor_orders')) {
            if ($this->db->fieldExists('vital_id', 'doctor_orders')) {
                $this->forge->dropColumn('doctor_orders', 'vital_id');
            }
        }
    }
}

