<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ClearAllDoctorSchedules extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'schedules:clear-all-doctors';
    protected $description = 'Delete all doctor schedules from the database';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        if (!$db->tableExists('doctor_schedules')) {
            CLI::write('doctor_schedules table does not exist.', 'red');
            return;
        }
        
        CLI::write('Deleting all doctor schedules...', 'yellow');
        
        // Get count before deletion
        $countBefore = $db->table('doctor_schedules')->countAllResults();
        
        // Delete all records (using where clause to satisfy CodeIgniter's safety requirement)
        $db->table('doctor_schedules')->where('id >', 0)->delete();
        $deletedCount = $db->affectedRows();
        
        // If the above didn't work (empty table), use truncate
        if ($deletedCount == 0 && $countBefore > 0) {
            $db->query('TRUNCATE TABLE doctor_schedules');
            $deletedCount = $countBefore;
        }
        
        CLI::write("Successfully deleted {$deletedCount} doctor schedule(s).", 'green');
        CLI::write('You can now create new schedules manually through the admin interface.', 'green');
    }
}

