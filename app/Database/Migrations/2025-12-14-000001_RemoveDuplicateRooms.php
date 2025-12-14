<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveDuplicateRooms extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        if (!$db->tableExists('rooms')) {
            log_message('error', 'Rooms table does not exist.');
            return;
        }
        
        // Find duplicate room numbers
        $duplicates = $db->query("
            SELECT room_number, COUNT(*) as count
            FROM rooms
            GROUP BY room_number
            HAVING count > 1
        ")->getResultArray();
        
        if (empty($duplicates)) {
            log_message('info', 'No duplicate rooms found.');
            return;
        }
        
        log_message('info', 'Found ' . count($duplicates) . ' duplicate room numbers.');
        
        // For each duplicate room number, keep the first one (lowest ID) and delete the rest
        foreach ($duplicates as $dup) {
            $roomNumber = $dup['room_number'];
            
            // Get all rooms with this number, ordered by ID
            $rooms = $db->table('rooms')
                ->where('room_number', $roomNumber)
                ->orderBy('id', 'ASC')
                ->get()
                ->getResultArray();
            
            if (count($rooms) <= 1) {
                continue; // Skip if only one exists now
            }
            
            // Keep the first room (lowest ID)
            $keepRoom = $rooms[0];
            $keepId = $keepRoom['id'];
            
            // Delete all other duplicates
            $deleteIds = [];
            for ($i = 1; $i < count($rooms); $i++) {
                $deleteIds[] = $rooms[$i]['id'];
            }
            
            if (!empty($deleteIds)) {
                // Check if any of the rooms to delete have patients assigned
                $roomsWithPatients = $db->table('rooms')
                    ->whereIn('id', $deleteIds)
                    ->where('current_patient_id IS NOT NULL', null, false)
                    ->get()
                    ->getResultArray();
                
                if (!empty($roomsWithPatients)) {
                    // If duplicate has patient, transfer to the kept room
                    foreach ($roomsWithPatients as $roomWithPatient) {
                        if (empty($keepRoom['current_patient_id'])) {
                            // Transfer patient to kept room
                            $db->table('rooms')
                                ->where('id', $keepId)
                                ->update([
                                    'current_patient_id' => $roomWithPatient['current_patient_id'],
                                    'status' => $roomWithPatient['status'],
                                    'updated_at' => date('Y-m-d H:i:s')
                                ]);
                            
                            // Also update beds if they exist
                            $db->table('beds')
                                ->where('room_id', $roomWithPatient['id'])
                                ->update(['room_id' => $keepId]);
                        }
                    }
                }
                
                // Also update beds to point to the kept room
                $db->table('beds')
                    ->whereIn('room_id', $deleteIds)
                    ->update(['room_id' => $keepId]);
                
                // Delete duplicate rooms
                $db->table('rooms')
                    ->whereIn('id', $deleteIds)
                    ->delete();
                
                log_message('info', "Removed " . count($deleteIds) . " duplicate(s) of room {$roomNumber}. Kept room ID: {$keepId}");
            }
        }
        
        log_message('info', 'Duplicate rooms cleanup completed.');
    }

    public function down()
    {
        // Cannot restore deleted duplicates, so leave empty
    }
}

