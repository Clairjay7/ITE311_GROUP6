<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateLabRequestsRequestedBy extends Migration
{
    public function up()
    {
        // Update requested_by ENUM to include 'admin'
        $this->db->query("ALTER TABLE lab_requests MODIFY COLUMN requested_by ENUM('doctor', 'nurse', 'admin') DEFAULT 'doctor'");
    }

    public function down()
    {
        // Revert back to original ENUM
        $this->db->query("ALTER TABLE lab_requests MODIFY COLUMN requested_by ENUM('doctor', 'nurse') DEFAULT 'doctor'");
    }
}

