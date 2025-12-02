<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PopulateBedsForExistingRooms extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Get all rooms
        $rooms = $db->table('rooms')->get()->getResultArray();
        
        // For each room, create beds based on bed_count
        foreach ($rooms as $room) {
            $bedCount = $room['bed_count'] ?? 1;
            
            // Check if beds already exist for this room
            $existingBeds = $db->table('beds')
                ->where('room_id', $room['id'])
                ->countAllResults();
            
            if ($existingBeds == 0) {
                // Create beds for this room
                $beds = [];
                for ($i = 1; $i <= $bedCount; $i++) {
                    $beds[] = [
                        'room_id' => $room['id'],
                        'bed_number' => (string)$i,
                        'status' => 'available',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                }
                
                if (!empty($beds)) {
                    $db->table('beds')->insertBatch($beds);
                }
            }
        }
    }

    public function down()
    {
        // Optionally remove beds, but we'll keep them
    }
}

