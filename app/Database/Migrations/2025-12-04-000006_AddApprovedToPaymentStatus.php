<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddApprovedToPaymentStatus extends Migration
{
    public function up()
    {
        // Modify the payment_status ENUM to include 'approved'
        // First check current ENUM values and add 'approved'
        $this->db->query("ALTER TABLE lab_requests MODIFY COLUMN payment_status ENUM('unpaid', 'pending', 'approved', 'paid') DEFAULT 'unpaid'");
    }

    public function down()
    {
        // Revert back to original ENUM values (remove 'approved')
        $this->db->query("ALTER TABLE lab_requests MODIFY COLUMN payment_status ENUM('unpaid', 'pending', 'paid') DEFAULT 'unpaid'");
    }
}

