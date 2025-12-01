<?php

namespace App\Controllers\Pharmacy;

use App\Controllers\BaseController;

class DirectInsert extends BaseController
{
    public function index()
    {
        echo "<h1>Direct Insert Test</h1>";
        
        $db = \Config\Database::connect();
        
        // Test 1: Check if table exists
        echo "<h3>Test 1: Check Table</h3>";
        if ($db->tableExists('pharmacy')) {
            echo "✅ Table 'pharmacy' exists<br>";
        } else {
            echo "❌ Table 'pharmacy' does NOT exist<br>";
            return;
        }
        
        // Test 2: Try to insert
        echo "<h3>Test 2: Insert Data</h3>";
        $data = [
            'item_name' => 'Direct Test ' . date('H:i:s'),
            'description' => 'Testing direct insert',
            'quantity' => 999,
            'price' => 99.99,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        echo "Data to insert:<br>";
        echo "<pre>" . print_r($data, true) . "</pre>";
        
        try {
            $result = $db->table('pharmacy')->insert($data);
            
            if ($result) {
                $insertId = $db->insertID();
                echo "✅ SUCCESS! Insert ID: " . $insertId . "<br>";
                
                // Verify
                $check = $db->table('pharmacy')->where('id', $insertId)->get()->getRowArray();
                echo "<h3>Verification:</h3>";
                echo "<pre>" . print_r($check, true) . "</pre>";
                
                echo "<br><a href='/group6/pharmacy/stock-monitoring'>View in Stock Monitoring</a>";
            } else {
                echo "❌ Insert returned FALSE<br>";
                echo "Error: <pre>" . print_r($db->error(), true) . "</pre>";
            }
        } catch (\Exception $e) {
            echo "❌ Exception: " . $e->getMessage() . "<br>";
            echo "Trace: <pre>" . $e->getTraceAsString() . "</pre>";
        }
        
        // Test 3: Check if POST works
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo "<h3>Test 3: POST Data Received</h3>";
            echo "<pre>" . print_r($_POST, true) . "</pre>";
            
            $postData = [
                'item_name' => $_POST['item_name'] ?? 'Test',
                'description' => $_POST['description'] ?? '',
                'quantity' => (int)($_POST['quantity'] ?? 0),
                'price' => (float)($_POST['price'] ?? 0),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $result = $db->table('pharmacy')->insert($postData);
            if ($result) {
                echo "✅ POST Insert SUCCESS! ID: " . $db->insertID();
            } else {
                echo "❌ POST Insert FAILED";
            }
        } else {
            echo "<h3>Test 3: POST Form</h3>";
            echo '<form method="POST">
                <input type="text" name="item_name" placeholder="Medicine Name" required><br><br>
                <input type="text" name="description" placeholder="Description"><br><br>
                <input type="number" name="quantity" placeholder="Quantity" required><br><br>
                <input type="number" name="price" step="0.01" placeholder="Price" required><br><br>
                <button type="submit">Test POST Insert</button>
            </form>';
        }
    }
}

