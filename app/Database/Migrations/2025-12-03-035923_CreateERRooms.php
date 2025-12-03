<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateERRooms extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Check if rooms table exists
        if (!$db->tableExists('rooms')) {
            log_message('error', 'Rooms table does not exist. Please run CreateRoomsTable migration first.');
            return;
        }
        
        // Check if ER rooms already exist
        $existingERRooms = $db->table('rooms')
            ->groupStart()
                ->where('room_type', 'ER')
                ->orWhere('ward', 'Emergency')
                ->orWhere('ward', 'ER')
            ->groupEnd()
            ->countAllResults();
        
        if ($existingERRooms > 0) {
            log_message('info', "ER rooms already exist ({$existingERRooms} rooms found). Skipping creation.");
            return;
        }
        
        // Create ER rooms
        $erRooms = [];
        $erRoomNumbers = ['ER-01', 'ER-02', 'ER-03', 'ER-04', 'ER-05', 'ER-06', 'ER-07', 'ER-08'];
        
        foreach ($erRoomNumbers as $roomNumber) {
            $erRooms[] = [
                'ward' => 'Emergency',
                'room_number' => $roomNumber,
                'room_type' => 'ER',
                'bed_count' => 1, // Each ER room has 1 bed
                'status' => 'available',
                'current_patient_id' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }
        
        // Insert ER rooms
        if (!empty($erRooms)) {
            $db->table('rooms')->insertBatch($erRooms);
            log_message('info', 'Created ' . count($erRooms) . ' ER rooms.');
        }
        
        // Create beds for ER rooms if beds table exists
        if ($db->tableExists('beds')) {
            $erBeds = [];
            $insertedRooms = $db->table('rooms')
                ->where('ward', 'Emergency')
                ->where('room_type', 'ER')
                ->get()
                ->getResultArray();
            
            foreach ($insertedRooms as $room) {
                // Create 1 bed per ER room
                $erBeds[] = [
                    'room_id' => $room['id'],
                    'bed_number' => $room['room_number'] . '-BED-01',
                    'status' => 'available',
                    'current_patient_id' => null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
            
            if (!empty($erBeds)) {
                $db->table('beds')->insertBatch($erBeds);
                log_message('info', 'Created ' . count($erBeds) . ' ER beds.');
            }
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        
        // Delete ER beds first (if beds table exists)
        if ($db->tableExists('beds')) {
            $erRoomIds = $db->table('rooms')
                ->select('id')
                ->groupStart()
                    ->where('room_type', 'ER')
                    ->orWhere('ward', 'Emergency')
                    ->orWhere('ward', 'ER')
                ->groupEnd()
                ->get()
                ->getResultArray();
            
            $roomIds = array_column($erRoomIds, 'id');
            
            if (!empty($roomIds)) {
                $db->table('beds')
                    ->whereIn('room_id', $roomIds)
                    ->delete();
            }
        }
        
        // Delete ER rooms
        if ($db->tableExists('rooms')) {
            $db->table('rooms')
                ->groupStart()
                    ->where('room_type', 'ER')
                    ->orWhere('ward', 'Emergency')
                    ->orWhere('ward', 'ER')
                ->groupEnd()
                ->delete();
        }
    }
}
