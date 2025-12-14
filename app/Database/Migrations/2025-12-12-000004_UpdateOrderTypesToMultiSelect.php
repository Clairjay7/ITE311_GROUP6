<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateOrderTypesToMultiSelect extends Migration
{
    public function up()
    {
        // Update order_type ENUM to include new order types
        if ($this->db->tableExists('doctor_orders')) {
            // MySQL doesn't support direct ENUM modification, so we need to alter the column
            $this->db->query("ALTER TABLE doctor_orders MODIFY COLUMN order_type ENUM(
                'medication',
                'lab_test',
                'diagnostic_imaging',
                'nursing_order',
                'treatment_order',
                'iv_fluids_order',
                'procedure',
                'reassessment_order',
                'stat_order',
                'diet',
                'activity',
                'other'
            ) DEFAULT 'medication'");
        }
    }

    public function down()
    {
        // Revert to original order types
        if ($this->db->tableExists('doctor_orders')) {
            $this->db->query("ALTER TABLE doctor_orders MODIFY COLUMN order_type ENUM(
                'medication',
                'lab_test',
                'procedure',
                'diet',
                'activity',
                'other'
            ) DEFAULT 'medication'");
        }
    }
}

