<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSpecimenCollectedToLabRequests extends Migration
{
    public function up()
    {
        // Modify the status ENUM to include 'specimen_collected'
        $this->db->query("ALTER TABLE lab_requests MODIFY COLUMN status ENUM('pending', 'specimen_collected', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending'");
    }

    public function down()
    {
        // Revert back to original ENUM values
        $this->db->query("ALTER TABLE lab_requests MODIFY COLUMN status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending'");
    }
}

