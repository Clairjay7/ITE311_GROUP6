<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ForcePopulateRoomsByType extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        if (!$db->tableExists('rooms')) {
            log_message('error', 'Rooms table does not exist.');
            return;
        }
        
        // Check if price column exists
        $fields = $db->getFieldData('rooms');
        $hasPrice = false;
        foreach ($fields as $field) {
            if ($field->name === 'price') {
                $hasPrice = true;
                break;
            }
        }
        
        // Define rooms to create for each room type
        $roomsToCreate = [
            // Private Rooms
            [
                'room_type' => 'Private',
                'ward' => 'Private Ward',
                'count' => 5,
                'bed_count' => 1,
                'prefix' => 'PRV',
                'price' => 5000.00, // Highest rate
            ],
            // Semi-Private Rooms
            [
                'room_type' => 'Semi-Private',
                'ward' => 'Semi-Private Ward',
                'count' => 5,
                'bed_count' => 2,
                'prefix' => 'SEM',
                'price' => 3000.00, // Mid-range rate
            ],
            // Ward (General Ward)
            [
                'room_type' => 'Ward',
                'ward' => 'General Ward',
                'count' => 10,
                'bed_count' => 6,
                'prefix' => 'WRD',
                'price' => 1000.00, // Cheapest
            ],
            // ICU
            [
                'room_type' => 'ICU',
                'ward' => 'Intensive Care Unit',
                'count' => 3,
                'bed_count' => 1,
                'prefix' => 'ICU',
                'price' => 8000.00, // Specialized rate (highest)
            ],
            // Isolation Room
            [
                'room_type' => 'Isolation',
                'ward' => 'Isolation Ward',
                'count' => 2,
                'bed_count' => 1,
                'prefix' => 'ISO',
                'price' => 6000.00, // Special rate
            ],
        ];
        
        $rooms = [];
        $now = date('Y-m-d H:i:s');
        
        foreach ($roomsToCreate as $roomConfig) {
            // Create rooms for this type
            for ($i = 1; $i <= $roomConfig['count']; $i++) {
                $roomNumber = $roomConfig['prefix'] . sprintf('%02d', $i);
                
                // Check if this exact room number already exists
                $exists = $db->table('rooms')
                    ->where('room_number', $roomNumber)
                    ->countAllResults();
                
                if ($exists > 0) {
                    // Update existing room with correct room_type and price
                    $updateData = [
                        'room_type' => $roomConfig['room_type'],
                        'ward' => $roomConfig['ward'],
                        'bed_count' => $roomConfig['bed_count'],
                        'updated_at' => $now,
                    ];
                    
                    if ($hasPrice && isset($roomConfig['price'])) {
                        $updateData['price'] = $roomConfig['price'];
                    }
                    
                    $db->table('rooms')
                        ->where('room_number', $roomNumber)
                        ->update($updateData);
                    continue;
                }
                
                // Create new room
                $roomData = [
                    'ward' => $roomConfig['ward'],
                    'room_number' => $roomNumber,
                    'room_type' => $roomConfig['room_type'],
                    'bed_count' => $roomConfig['bed_count'],
                    'status' => 'Available',
                    'current_patient_id' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                
                if ($hasPrice && isset($roomConfig['price'])) {
                    $roomData['price'] = $roomConfig['price'];
                }
                
                $rooms[] = $roomData;
            }
        }
        
        // Insert all new rooms
        if (!empty($rooms)) {
            $db->table('rooms')->insertBatch($rooms);
            log_message('info', 'Successfully created ' . count($rooms) . ' new rooms by type.');
        }
        
        // Also update any existing rooms with NULL room_type to 'Ward'
        $wardUpdateData = [
            'room_type' => 'Ward',
            'bed_count' => 6,
            'ward' => 'General Ward',
            'updated_at' => $now,
        ];
        
        if ($hasPrice) {
            $wardUpdateData['price'] = 1000.00;
        }
        
        $db->table('rooms')
            ->where('room_type IS NULL', null, false)
            ->orWhere('room_type', '')
            ->update($wardUpdateData);
    }

    public function down()
    {
        // Don't delete rooms on rollback, just leave them
    }
}

