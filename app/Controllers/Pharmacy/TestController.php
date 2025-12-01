<?php

namespace App\Controllers\Pharmacy;

use App\Controllers\BaseController;

class TestController extends BaseController
{
    public function testInsert()
    {
        $db = \Config\Database::connect();
        
        $data = [
            'item_name' => 'Test Medicine ' . time(),
            'description' => 'Auto test insert',
            'quantity' => 100,
            'price' => 50.00,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        try {
            $result = $db->table('pharmacy')->insert($data);
            
            if ($result) {
                $insertId = $db->insertID();
                echo "✅ SUCCESS! Inserted medicine with ID: " . $insertId . "<br>";
                echo "Medicine: " . $data['item_name'] . "<br>";
                echo "<a href='/pharmacy/stock-monitoring'>View Stock Monitoring</a>";
            } else {
                echo "❌ FAILED! Could not insert.<br>";
                print_r($db->error());
            }
        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage();
        }
    }
}

