<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;

class TestAuth extends Controller
{
    public function createTestUser()
    {
        $userModel = new UserModel();
        
        // Check if test user already exists
        $existingUser = $userModel->where('username', 'admin')->first();
        
        if ($existingUser) {
            echo "Test user 'admin' already exists!<br>";
            echo "Username: admin<br>";
            echo "Password: admin123<br>";
            echo "Role: " . $existingUser['role'] . "<br>";
            return;
        }
        
        // Create test user
        $userData = [
            'username' => 'admin',
            'email' => 'admin@hospital.com',
            'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'role' => 'superadmin',
            'status' => 'active'
        ];
        
        try {
            if ($userModel->insert($userData)) {
                echo "✅ Test user created successfully!<br>";
                echo "Username: admin<br>";
                echo "Password: admin123<br>";
                echo "Role: superadmin<br>";
                echo "<br><a href='" . base_url('login') . "'>Go to Login Page</a>";
            } else {
                echo "❌ Failed to create test user<br>";
                echo "Errors: " . json_encode($userModel->errors());
            }
        } catch (\Exception $e) {
            echo "❌ Error: " . $e->getMessage();
        }
    }
    
    public function testDatabase()
    {
        try {
            $db = \Config\Database::connect();
            
            // Test connection
            if ($db->connect()) {
                echo "✅ Database connection successful!<br>";
                echo "Database: " . $db->getDatabase() . "<br><br>";
                
                // Show all tables in database
                echo "<h2>📋 All Tables in Database:</h2>";
                $tables = $db->listTables();
                echo "<ul>";
                foreach ($tables as $table) {
                    echo "<li><strong>$table</strong></li>";
                }
                echo "</ul><br>";
                
                // Check each important table structure
                $importantTables = ['users', 'patients', 'doctors', 'nurses', 'appointments', 'roles'];
                
                foreach ($importantTables as $tableName) {
                    if ($db->tableExists($tableName)) {
                        echo "<h3>📊 Table: $tableName</h3>";
                        
                        // Show table structure
                        $fields = $db->getFieldData($tableName);
                        echo "<table border='1' style='border-collapse: collapse; margin-bottom: 20px;'>";
                        echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
                        foreach ($fields as $field) {
                            echo "<tr>";
                            echo "<td>{$field->name}</td>";
                            echo "<td>{$field->type}</td>";
                            echo "<td>" . ($field->nullable ? 'YES' : 'NO') . "</td>";
                            echo "<td>{$field->primary_key}</td>";
                            echo "<td>{$field->default}</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                        
                        // Show sample data (first 3 rows)
                        $sampleData = $db->table($tableName)->limit(3)->get()->getResultArray();
                        if (!empty($sampleData)) {
                            echo "<strong>Sample Data (first 3 rows):</strong><br>";
                            echo "<pre>";
                            print_r($sampleData);
                            echo "</pre>";
                        } else {
                            echo "<em>No data in this table</em><br>";
                        }
                        echo "<hr>";
                    } else {
                        echo "<h3>❌ Table: $tableName (does not exist)</h3>";
                    }
                }
                
            } else {
                echo "❌ Database connection failed<br>";
            }
        } catch (\Exception $e) {
            echo "❌ Database error: " . $e->getMessage();
            echo "<br>Stack trace:<br><pre>" . $e->getTraceAsString() . "</pre>";
        }
    }
}
