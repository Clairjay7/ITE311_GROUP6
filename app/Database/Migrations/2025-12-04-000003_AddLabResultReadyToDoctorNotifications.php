<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLabResultReadyToDoctorNotifications extends Migration
{
    public function up()
    {
        // Update type ENUM to include 'lab_result_ready'
        $this->db->query("ALTER TABLE doctor_notifications MODIFY COLUMN type ENUM('order_completed', 'order_updated', 'lab_request_pending', 'lab_result_ready', 'patient_assigned', 'system') DEFAULT 'system'");
    }

    public function down()
    {
        // Revert back to original ENUM
        $this->db->query("ALTER TABLE doctor_notifications MODIFY COLUMN type ENUM('order_completed', 'order_updated', 'lab_request_pending', 'patient_assigned', 'system') DEFAULT 'system'");
    }
}

