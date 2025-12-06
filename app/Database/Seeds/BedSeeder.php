<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BedSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        if (!$db->tableExists('rooms') || !$db->tableExists('beds')) {
            log_message('error', 'Rooms or Beds table does not exist. Please run migrations first.');
            return;
        }
        
        $now = date('Y-m-d H:i:s');
        $bedsToCreate = [];
        
        // Get all rooms from database
        $rooms = $db->table('rooms')
            ->get()
            ->getResultArray();
        
        foreach ($rooms as $room) {
            $roomId = $room['id'];
            $bedCount = $room['bed_count'] ?? 1;
            
            // Check if beds already exist for this room
            $existingBeds = $db->table('beds')
                ->where('room_id', $roomId)
                ->countAllResults();
            
            // Only create beds if they don't exist or if we need more
            if ($existingBeds < $bedCount) {
                // Get existing bed numbers to avoid duplicates
                $existingBedNumbers = $db->table('beds')
                    ->where('room_id', $roomId)
                    ->select('bed_number')
                    ->get()
                    ->getResultArray();
                
                $existingNumbers = array_column($existingBedNumbers, 'bed_number');
                
                // Create beds for this room
                for ($i = 1; $i <= $bedCount; $i++) {
                    $bedNumber = (string)$i;
                    
                    // Skip if bed number already exists
                    if (in_array($bedNumber, $existingNumbers)) {
                        continue;
                    }
                    
                    $bedsToCreate[] = [
                        'room_id' => $roomId,
                        'bed_number' => $bedNumber,
                        'status' => 'available',
                        'current_patient_id' => null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }
        
        // Insert all beds
        if (!empty($bedsToCreate)) {
            $db->table('beds')->insertBatch($bedsToCreate);
            log_message('info', 'Successfully created ' . count($bedsToCreate) . ' beds for rooms.');
        } else {
            log_message('info', 'All rooms already have beds. No new beds created.');
        }
    }
}

