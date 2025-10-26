<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class QuickTest extends Controller
{
    public function users()
    {
        try {
            // Direct database connection
            $db = \Config\Database::connect();
            
            echo "<h1>🔍 Direct Database Users Test</h1>";
            echo "<p>Testing direct connection to your users table...</p>";
            
            if ($db->connect()) {
                echo "<p>✅ Database connected successfully</p>";
                echo "<p>Database: " . $db->getDatabase() . "</p>";
                
                if ($db->tableExists('users')) {
                    echo "<p>✅ Users table exists</p>";
                    
                    // Get all users directly
                    $query = $db->query("SELECT * FROM users ORDER BY id ASC");
                    $users = $query->getResultArray();
                    
                    echo "<h2>📊 Found " . count($users) . " users in database:</h2>";
                    
                    if (count($users) > 0) {
                        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
                        echo "<tr style='background: #f0f0f0;'>";
                        echo "<th style='padding: 10px;'>ID</th>";
                        echo "<th style='padding: 10px;'>Username</th>";
                        echo "<th style='padding: 10px;'>First Name</th>";
                        echo "<th style='padding: 10px;'>Last Name</th>";
                        echo "<th style='padding: 10px;'>Email</th>";
                        echo "<th style='padding: 10px;'>Role</th>";
                        echo "<th style='padding: 10px;'>Status</th>";
                        echo "</tr>";
                        
                        foreach ($users as $user) {
                            echo "<tr>";
                            echo "<td style='padding: 8px;'>" . ($user['id'] ?? 'N/A') . "</td>";
                            echo "<td style='padding: 8px;'>" . ($user['username'] ?? 'N/A') . "</td>";
                            echo "<td style='padding: 8px;'>" . ($user['first_name'] ?? 'N/A') . "</td>";
                            echo "<td style='padding: 8px;'>" . ($user['last_name'] ?? 'N/A') . "</td>";
                            echo "<td style='padding: 8px;'>" . ($user['email'] ?? 'N/A') . "</td>";
                            echo "<td style='padding: 8px;'>" . ($user['role'] ?? 'N/A') . "</td>";
                            echo "<td style='padding: 8px;'>" . ($user['status'] ?? 'N/A') . "</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                        
                        // Show JSON format for API
                        echo "<h3>📋 JSON Format (for API):</h3>";
                        echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto;'>";
                        echo json_encode($users, JSON_PRETTY_PRINT);
                        echo "</pre>";
                        
                    } else {
                        echo "<p>❌ No users found in database</p>";
                        echo "<p><strong>Solutions:</strong></p>";
                        echo "<ul>";
                        echo "<li><a href='" . base_url('create-test-user') . "'>Create Test User</a></li>";
                        echo "<li>Run database migrations</li>";
                        echo "<li>Check if users were deleted</li>";
                        echo "</ul>";
                    }
                    
                } else {
                    echo "<p>❌ Users table does not exist</p>";
                    echo "<p>Please run migrations first</p>";
                }
                
            } else {
                echo "<p>❌ Cannot connect to database</p>";
            }
            
            echo "<hr>";
            echo "<h3>🔗 Test Links:</h3>";
            echo "<ul>";
            echo "<li><a href='" . base_url('super-admin/api/simple-users') . "'>Test Simple Users API</a></li>";
            echo "<li><a href='" . base_url('super-admin/unified') . "'>Back to Dashboard</a></li>";
            echo "<li><a href='" . base_url('create-test-user') . "'>Create Test User</a></li>";
            echo "</ul>";
            
        } catch (\Exception $e) {
            echo "<p>❌ Error: " . $e->getMessage() . "</p>";
            echo "<p>Stack trace:</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
    }
    
    public function apiTest()
    {
        header('Content-Type: application/json');
        
        try {
            $db = \Config\Database::connect();
            
            if ($db->tableExists('users')) {
                $users = $db->table('users')->orderBy('id', 'ASC')->get()->getResultArray();
                
                // Format users for the dashboard
                $formattedUsers = [];
                foreach ($users as $user) {
                    $formattedUsers[] = [
                        'id' => $user['id'],
                        'username' => $user['username'] ?? 'N/A',
                        'first_name' => $user['first_name'] ?? '',
                        'last_name' => $user['last_name'] ?? '',
                        'email' => $user['email'] ?? 'N/A',
                        'role' => $user['role'] ?? 'user',
                        'phone' => $user['phone'] ?? 'N/A',
                        'status' => $user['status'] ?? 'active',
                        'last_login' => $user['last_login'] ?? 'Never',
                        'created_at' => $user['created_at'] ?? 'N/A'
                    ];
                }
                
                echo json_encode([
                    'success' => true,
                    'count' => count($formattedUsers),
                    'data' => $formattedUsers,
                    'message' => 'All users loaded successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Users table does not exist'
                ]);
            }
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}
