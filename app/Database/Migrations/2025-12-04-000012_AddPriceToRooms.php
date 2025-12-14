<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPriceToRooms extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        if (!$db->tableExists('rooms')) {
            log_message('error', 'Rooms table does not exist.');
            return;
        }
        
        // Check if price column already exists
        $fields = $db->getFieldData('rooms');
        $hasPrice = false;
        
        foreach ($fields as $field) {
            if ($field->name === 'price' || $field->name === 'daily_rate') {
                $hasPrice = true;
                break;
            }
        }
        
        if (!$hasPrice) {
            // Add price column
            $this->forge->addColumn('rooms', [
                'price' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'null' => true,
                    'default' => 0.00,
                    'comment' => 'Daily rate/price for the room',
                    'after' => 'bed_count',
                ],
            ]);
        }
        
        // Set default prices for each room type
        $roomPrices = [
            'Private' => 5000.00,        // Highest rate
            'Semi-Private' => 3000.00,    // Mid-range rate
            'Ward' => 1000.00,            // Cheapest
            'ICU' => 8000.00,             // Specialized rate (highest)
            'Isolation' => 6000.00,       // Special rate
            'OR' => 15000.00,             // Operating Room rate
            'NICU' => 10000.00,           // Neonatal ICU rate
        ];
        
        // Update existing rooms with prices based on room_type
        foreach ($roomPrices as $roomType => $price) {
            $db->table('rooms')
                ->where('room_type', $roomType)
                ->where('(price IS NULL OR price = 0)', null, false)
                ->update([
                    'price' => $price,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('rooms', 'price');
    }
}

