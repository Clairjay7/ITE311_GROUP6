<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $data = [];
        
        // Pedia Ward Rooms (for pediatric patients 0-17 years old)
        for ($i = 1; $i <= 5; $i++) {
            $data[] = [
                'ward' => 'Pedia Ward',
                'room_number' => 'P' . sprintf('%02d', $i),
                'room_type' => 'Ward',
                'bed_count' => 6,
                'price' => 1000.00,
                'status' => 'Available',
                'current_patient_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        
        // Male Ward Rooms (for male patients)
        for ($i = 1; $i <= 5; $i++) {
            $data[] = [
                'ward' => 'Male Ward',
                'room_number' => 'M' . sprintf('%02d', $i),
                'room_type' => 'Ward',
                'bed_count' => 6,
                'price' => 1000.00,
                'status' => 'Available',
                'current_patient_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        
        // Female Ward Rooms (for female patients)
        for ($i = 1; $i <= 5; $i++) {
            $data[] = [
                'ward' => 'Female Ward',
                'room_number' => 'F' . sprintf('%02d', $i),
                'room_type' => 'Ward',
                'bed_count' => 6,
                'price' => 1000.00,
                'status' => 'Available',
                'current_patient_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        
        // General Ward Rooms (for Ward room type - general admission)
        for ($i = 1; $i <= 10; $i++) {
            $data[] = [
                'ward' => 'General Ward',
                'room_number' => 'WRD' . sprintf('%02d', $i),
                'room_type' => 'Ward',
                'bed_count' => 6,
                'price' => 1000.00,
                'status' => 'Available',
                'current_patient_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        
        // Private Rooms (₱5,000/day - 1 patient, own CR)
        for ($i = 1; $i <= 5; $i++) {
            $roomNumber = 'PRV' . sprintf('%02d', $i);
            
            // Check if room already exists
            $exists = $this->db->table('rooms')
                ->where('room_number', $roomNumber)
                ->countAllResults();
            
            if ($exists == 0) {
                $data[] = [
                    'ward' => 'Private Ward',
                    'room_number' => $roomNumber,
                    'room_type' => 'Private',
                    'bed_count' => 1,
                    'price' => 5000.00,
                    'status' => 'Available',
                    'current_patient_id' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
        
        // Semi-Private Rooms (₱3,000/day - 2 patients, shared CR)
        for ($i = 1; $i <= 5; $i++) {
            $roomNumber = 'SEM' . sprintf('%02d', $i);
            
            // Check if room already exists
            $exists = $this->db->table('rooms')
                ->where('room_number', $roomNumber)
                ->countAllResults();
            
            if ($exists == 0) {
                $data[] = [
                    'ward' => 'Semi-Private Ward',
                    'room_number' => $roomNumber,
                    'room_type' => 'Semi-Private',
                    'bed_count' => 2,
                    'price' => 3000.00,
                    'status' => 'Available',
                    'current_patient_id' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
        
        // ICU Rooms (₱8,000/day - Critical care, special equipment)
        for ($i = 1; $i <= 3; $i++) {
            $data[] = [
                'ward' => 'Intensive Care Unit',
                'room_number' => 'ICU' . sprintf('%02d', $i),
                'room_type' => 'ICU',
                'bed_count' => 1,
                'price' => 8000.00,
                'status' => 'Available',
                'current_patient_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        
        // Isolation Rooms (₱6,000/day - For infectious diseases)
        for ($i = 1; $i <= 2; $i++) {
            $data[] = [
                'ward' => 'Isolation Ward',
                'room_number' => 'ISO' . sprintf('%02d', $i),
                'room_type' => 'Isolation',
                'bed_count' => 1,
                'price' => 6000.00,
                'status' => 'Available',
                'current_patient_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        
        // NICU Rooms (Neonatal Intensive Care Unit - ₱10,000/day - For newborns 0-28 days old)
        for ($i = 1; $i <= 3; $i++) {
            $data[] = [
                'ward' => 'NICU',
                'room_number' => 'NICU' . sprintf('%02d', $i),
                'room_type' => 'NICU',
                'bed_count' => 1,
                'price' => 10000.00,
                'status' => 'Available',
                'current_patient_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        
        // OR Rooms (Operating Room - ₱15,000/day - For surgeries)
        for ($i = 1; $i <= 5; $i++) {
            $data[] = [
                'ward' => 'Operating Room',
                'room_number' => 'OR' . sprintf('%02d', $i),
                'room_type' => 'OR',
                'bed_count' => 1,
                'price' => 15000.00,
                'status' => 'Available',
                'current_patient_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        
        $this->db->table('rooms')->insertBatch($data);
    }
}

