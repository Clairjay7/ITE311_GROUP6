<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class NurseScheduleSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Check if nurse_schedules table exists
        if (!$db->tableExists('nurse_schedules')) {
            echo "nurse_schedules table does not exist. Please run migrations first.\n";
            return;
        }
        
        // Get all active nurses
        $nurses = [];
        if ($db->tableExists('users') && $db->tableExists('roles')) {
            $nurses = $db->table('users')
                ->select('users.id, users.username')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('LOWER(roles.name)', 'nurse')
                ->where('users.status', 'active')
                ->get()
                ->getResultArray();
        }
        
        if (empty($nurses)) {
            echo "No active nurses found. Please create nurse users first.\n";
            return;
        }
        
        echo "Found " . count($nurses) . " active nurse(s).\n";
        
        // Generate schedules for the next 30 days
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+30 days'));
        
        $schedules = [];
        $currentDate = $startDate;
        $nurseIndex = 0;
        
        while ($currentDate <= $endDate) {
            // Skip weekends (Saturday = 6, Sunday = 0)
            $dayOfWeek = date('w', strtotime($currentDate));
            if ($dayOfWeek == 0 || $dayOfWeek == 6) {
                $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
                continue;
            }
            
            // Assign nurses to morning and night shifts
            // Rotate nurses to ensure fair distribution
            $morningNurse = $nurses[$nurseIndex % count($nurses)];
            $nightNurse = $nurses[($nurseIndex + 1) % count($nurses)];
            
            // Check if schedule already exists for this nurse, date, and shift type
            $existingMorning = $db->table('nurse_schedules')
                ->where('nurse_id', $morningNurse['id'])
                ->where('shift_date', $currentDate)
                ->where('shift_type', 'morning')
                ->get()
                ->getRowArray();
            
            $existingNight = $db->table('nurse_schedules')
                ->where('nurse_id', $nightNurse['id'])
                ->where('shift_date', $currentDate)
                ->where('shift_type', 'night')
                ->get()
                ->getRowArray();
            
            // Add morning shift if it doesn't exist
            if (!$existingMorning) {
                $schedules[] = [
                    'nurse_id' => $morningNurse['id'],
                    'shift_date' => $currentDate,
                    'shift_type' => 'morning',
                    'start_time' => '06:00:00',
                    'end_time' => '12:00:00',
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
            
            // Add night shift if it doesn't exist
            if (!$existingNight) {
                $schedules[] = [
                    'nurse_id' => $nightNurse['id'],
                    'shift_date' => $currentDate,
                    'shift_type' => 'night',
                    'start_time' => '18:00:00',
                    'end_time' => '00:00:00',
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
            
            // Move to next day and rotate nurse index
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
            $nurseIndex++;
        }
        
        if (!empty($schedules)) {
            // Insert in batches of 50
            $batchSize = 50;
            for ($i = 0; $i < count($schedules); $i += $batchSize) {
                $batch = array_slice($schedules, $i, $batchSize);
                $db->table('nurse_schedules')->insertBatch($batch);
            }
            
            echo "Successfully created " . count($schedules) . " nurse schedules (morning and night shifts) for the next 30 days.\n";
            echo "- Morning shifts: " . count(array_filter($schedules, fn($s) => $s['shift_type'] === 'morning')) . "\n";
            echo "- Night shifts: " . count(array_filter($schedules, fn($s) => $s['shift_type'] === 'night')) . "\n";
        } else {
            echo "No new schedules to create. All schedules may already exist.\n";
        }
    }
}

