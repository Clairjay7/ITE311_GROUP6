<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PopulateRoomsByType extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Check if rooms table exists
        if (!$db->tableExists('rooms')) {
            log_message('error', 'Rooms table does not exist. Please run CreateRoomsTable migration first.');
            return;
        }
        
        // Check if room_type column exists, if not add it
        $fields = $db->getFieldData('rooms');
        $hasRoomType = false;
        $hasBedCount = false;
        
        foreach ($fields as $field) {
            if ($field->name === 'room_type') {
                $hasRoomType = true;
            }
            if ($field->name === 'bed_count') {
                $hasBedCount = true;
            }
        }
        
        if (!$hasRoomType) {
            $this->forge->addColumn('rooms', [
                'room_type' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => true,
                    'default' => 'Ward',
                    'comment' => 'Private, Semi-Private, Ward, ICU, Isolation',
                    'after' => 'ward',
                ],
            ]);
        }
        
        if (!$hasBedCount) {
            $this->forge->addColumn('rooms', [
                'bed_count' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'default' => 1,
                    'null' => false,
                    'after' => 'room_type',
                ],
            ]);
        }
        
        // Define rooms to create for each room type
        $roomsToCreate = [
            // Private Rooms - 1 patient, own CR, higher rate
            [
                'room_type' => 'Private',
                'ward' => 'Private Ward',
                'count' => 5,
                'bed_count' => 1,
                'prefix' => 'PRV',
            ],
            // Semi-Private Rooms - 2 patients, shared CR, mid-range rate
            [
                'room_type' => 'Semi-Private',
                'ward' => 'Semi-Private Ward',
                'count' => 5,
                'bed_count' => 2,
                'prefix' => 'SEM',
            ],
            // Ward (General Ward) - 4-10+ patients, shared facilities, cheapest
            [
                'room_type' => 'Ward',
                'ward' => 'General Ward',
                'count' => 10,
                'bed_count' => 6,
                'prefix' => 'WRD',
            ],
            // ICU (Intensive Care Unit) - Critical care, special equipment
            [
                'room_type' => 'ICU',
                'ward' => 'Intensive Care Unit',
                'count' => 3,
                'bed_count' => 1,
                'prefix' => 'ICU',
            ],
            // Isolation Room - For infectious diseases
            [
                'room_type' => 'Isolation',
                'ward' => 'Isolation Ward',
                'count' => 2,
                'bed_count' => 1,
                'prefix' => 'ISO',
            ],
        ];
        
        $rooms = [];
        $now = date('Y-m-d H:i:s');
        
        foreach ($roomsToCreate as $roomConfig) {
            // Check if rooms of this type already exist with this prefix
            $existingCount = $db->table('rooms')
                ->where('room_type', $roomConfig['room_type'])
                ->like('room_number', $roomConfig['prefix'], 'after')
                ->countAllResults();
            
            if ($existingCount >= $roomConfig['count']) {
                log_message('info', "Rooms of type '{$roomConfig['room_type']}' already exist. Skipping...");
                continue;
            }
            
            // Get existing room numbers for this type to avoid duplicates
            $existingRooms = $db->table('rooms')
                ->where('room_type', $roomConfig['room_type'])
                ->like('room_number', $roomConfig['prefix'], 'after')
                ->get()
                ->getResultArray();
            
            $existingNumbers = array_column($existingRooms, 'room_number');
            
            // Create rooms for this type
            for ($i = 1; $i <= $roomConfig['count']; $i++) {
                $roomNumber = $roomConfig['prefix'] . sprintf('%02d', $i);
                
                // Skip if room number already exists
                if (in_array($roomNumber, $existingNumbers)) {
                    continue;
                }
                
                $rooms[] = [
                    'ward' => $roomConfig['ward'],
                    'room_number' => $roomNumber,
                    'room_type' => $roomConfig['room_type'],
                    'bed_count' => $roomConfig['bed_count'],
                    'status' => 'Available',
                    'current_patient_id' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
        
        // Insert all rooms
        if (!empty($rooms)) {
            $db->table('rooms')->insertBatch($rooms);
            log_message('info', 'Successfully created ' . count($rooms) . ' rooms by type.');
        } else {
            log_message('info', 'No new rooms to create. All room types already have rooms.');
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        
        if ($db->tableExists('rooms')) {
            // Delete rooms by type (keep ER rooms if they exist)
            $roomTypes = ['Private', 'Semi-Private', 'Ward', 'ICU', 'Isolation'];
            
            foreach ($roomTypes as $type) {
                $db->table('rooms')
                    ->where('room_type', $type)
                    ->delete();
            }
        }
    }
}

