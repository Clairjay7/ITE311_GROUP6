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
        $roomsUpdated = 0;
        
        // Get all rooms from database
        $rooms = $db->table('rooms')
            ->get()
            ->getResultArray();
        
        // Define default bed counts by room type if bed_count is missing or 0
        $defaultBedCounts = [
            'Private' => 1,
            'Semi-Private' => 2,
            'Ward' => 6,
            'ICU' => 1,
            'Isolation' => 1,
            'NICU' => 1,
        ];
        
        foreach ($rooms as $room) {
            $roomId = $room['id'];
            $bedCount = (int)($room['bed_count'] ?? 0);
            $roomType = $room['room_type'] ?? 'Unknown';
            $roomNumber = $room['room_number'] ?? 'N/A';
            
            // If bed_count is 0, NULL, or missing, set it based on room type
            if ($bedCount < 1) {
                $bedCount = $defaultBedCounts[$roomType] ?? 1;
                
                // Update the room's bed_count in database
                $db->table('rooms')
                    ->where('id', $roomId)
                    ->update(['bed_count' => $bedCount, 'updated_at' => $now]);
                
                $roomsUpdated++;
                log_message('info', "Updated room {$roomNumber} (Type: {$roomType}, ID: {$roomId}) bed_count to {$bedCount}");
            }
            
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
                
                // Calculate how many beds we need to create
                $bedsNeeded = $bedCount - $existingBeds;
                
                // Create beds for this room
                $bedNumberCounter = $existingBeds + 1;
                for ($i = 0; $i < $bedsNeeded; $i++) {
                    $bedNumber = (string)$bedNumberCounter;
                    
                    // Skip if bed number already exists
                    if (in_array($bedNumber, $existingNumbers)) {
                        $bedNumberCounter++;
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
                    
                    $bedNumberCounter++;
                }
                
                // Log for debugging
                log_message('info', "Creating {$bedsNeeded} bed(s) for room {$roomNumber} (Type: {$roomType}, ID: {$roomId}). Total beds needed: {$bedCount}, Existing: {$existingBeds}");
            } else {
                // Log if beds already exist
                log_message('debug', "Room {$roomNumber} (Type: {$roomType}, ID: {$roomId}) already has {$existingBeds} bed(s), skipping.");
            }
        }
        
        // Insert all beds
        if (!empty($bedsToCreate)) {
            $db->table('beds')->insertBatch($bedsToCreate);
            log_message('info', 'Successfully created ' . count($bedsToCreate) . ' beds for rooms.');
        } else {
            log_message('info', 'All rooms already have beds. No new beds created.');
        }
        
        if ($roomsUpdated > 0) {
            log_message('info', "Updated bed_count for {$roomsUpdated} room(s).");
        }
        
        // Summary log
        $totalRooms = count($rooms);
        $totalBedsCreated = count($bedsToCreate);
        log_message('info', "BedSeeder completed: {$totalRooms} rooms processed, {$totalBedsCreated} beds created, {$roomsUpdated} rooms updated.");
    }
}

