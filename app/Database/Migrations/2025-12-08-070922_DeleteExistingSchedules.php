<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DeleteExistingSchedules extends Migration
{
    public function up()
    {
        // Delete all existing schedules from doctor_schedules table
        if ($this->db->tableExists('doctor_schedules')) {
            $this->db->table('doctor_schedules')->truncate();
            log_message('info', 'Deleted all existing doctor schedules');
        }
        
        // Delete all existing schedules from nurse_schedules table
        if ($this->db->tableExists('nurse_schedules')) {
            $this->db->table('nurse_schedules')->truncate();
            log_message('info', 'Deleted all existing nurse schedules');
        }
    }

    public function down()
    {
        // Cannot restore deleted schedules, so this is a one-way operation
        // If you need to restore, you would need a backup
    }
}
