<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PopulateBedsForRoomTypes extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        if (!$db->tableExists('beds') || !$db->tableExists('rooms')) {
            log_message('info', 'Beds or Rooms table does not exist. Skipping bed population.');
            return;
        }
        
        // Get all rooms that don't have beds yet
        $rooms = $db->table('rooms')
            ->get()
            ->getResultArray();
        
        $bedsToCreate = [];
        $now = date('Y-m-d H:i:s');
        
        foreach ($rooms as $room) {
            $roomId = $room['id'];
            $bedCount = $room['bed_count'] ?? 1;
            
            // Check if beds already exist for this room
            $existingBeds = $db->table('beds')
                ->where('room_id', $roomId)
                ->countAllResults();
            
            if ($existingBeds >= $bedCount) {
                continue; // Room already has enough beds
            }
            
            // Get existing bed numbers to avoid duplicates
            $existingBedNumbers = $db->table('beds')
                ->where('room_id', $roomId)
                ->select('bed_number')
                ->get()
                ->getResultArray();
            
            $existingNumbers = array_column($existingBedNumbers, 'bed_number');
            
            // Create missing beds
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
        
        // Insert all beds
        if (!empty($bedsToCreate)) {
            $db->table('beds')->insertBatch($bedsToCreate);
            log_message('info', 'Successfully created ' . count($bedsToCreate) . ' beds for rooms.');
        } else {
            log_message('info', 'All rooms already have beds. No new beds created.');
        }
    }

    public function down()
    {
        // Don't delete beds on rollback
    }
}

