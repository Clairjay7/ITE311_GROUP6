<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class InventorySeeder extends Seeder
{
    public function run()
    {
        $now = Time::now();
        $nowString = $now->toDateTimeString();

        $data = [
            [
                'item_name' => 'Paracetamol 500mg Tablet',
                'description' => 'Pain reliever and fever reducer tablets',
                'quantity' => 120,
                'supplier' => 'MediSupply Corp.',
                'expiration_date' => date('Y-m-d', strtotime('+12 months')),
                'status' => 'available',
                'created_at' => $nowString,
                'updated_at' => $nowString,
            ],
            [
                'item_name' => 'Amoxicillin 500mg Capsule',
                'description' => 'Broad-spectrum antibiotic capsules',
                'quantity' => 40,
                'supplier' => 'HealthPlus Distributors',
                'expiration_date' => date('Y-m-d', strtotime('+6 months')),
                'status' => 'low_stock',
                'created_at' => $nowString,
                'updated_at' => $nowString,
            ],
            [
                'item_name' => 'Ibuprofen 400mg Tablet',
                'description' => 'Anti-inflammatory tablets for pain relief',
                'quantity' => 10,
                'supplier' => 'Wellness Pharma',
                'expiration_date' => date('Y-m-d', strtotime('+21 days')),
                'status' => 'low_stock',
                'created_at' => $nowString,
                'updated_at' => $nowString,
            ],
            [
                'item_name' => 'Insulin Glargine 100IU/mL',
                'description' => 'Long-acting insulin for diabetes management',
                'quantity' => 5,
                'supplier' => 'NovoCare Medical',
                'expiration_date' => date('Y-m-d', strtotime('+7 days')),
                'status' => 'low_stock',
                'created_at' => $nowString,
                'updated_at' => $nowString,
            ],
            [
                'item_name' => 'Salbutamol Nebules 2.5mg/2.5mL',
                'description' => 'Bronchodilator solution for nebulization',
                'quantity' => 25,
                'supplier' => 'Respira Solutions',
                'expiration_date' => date('Y-m-d', strtotime('+2 months')),
                'status' => 'available',
                'created_at' => $nowString,
                'updated_at' => $nowString,
            ],
        ];

        $this->db->table('inventory')->insertBatch($data);
    }
}
