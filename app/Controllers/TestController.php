<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class TestController extends Controller
{
    public function index()
    {
        echo "<h1>🧪 System Test</h1>";
        
        // Test basic functionality
        echo "<h2>✅ Basic Tests</h2>";
        echo "<p>✅ PHP is working</p>";
        echo "<p>✅ CodeIgniter is loaded</p>";
        echo "<p>✅ Controller is accessible</p>";
        
        // Test database
        echo "<h2>📊 Database Test</h2>";
        try {
            $db = \Config\Database::connect();
            if ($db->connect()) {
                echo "<p>✅ Database connection successful</p>";
                echo "<p>Database: " . $db->getDatabase() . "</p>";
                
                // Test tables
                $tables = $db->listTables();
                echo "<p>Tables found: " . count($tables) . "</p>";
                
                // Test specific tables
                $requiredTables = ['users', 'patients', 'appointments'];
                foreach ($requiredTables as $table) {
                    if (in_array($table, $tables)) {
                        echo "<p>✅ Table '$table' exists</p>";
                    } else {
                        echo "<p>❌ Table '$table' missing</p>";
                    }
                }
            } else {
                echo "<p>❌ Database connection failed</p>";
            }
        } catch (\Exception $e) {
            echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
        }
        
        // Test session
        echo "<h2>🔐 Session Test</h2>";
        $session = session();
        echo "<p>Session ID: " . $session->session_id . "</p>";
        echo "<p>Is Logged In: " . ($session->get('isLoggedIn') ? 'Yes' : 'No') . "</p>";
        
        // Test links
        echo "<h2>🔗 Navigation Links</h2>";
        echo "<ul>";
        echo "<li><a href='" . base_url('debug') . "'>🔍 Debug Info</a></li>";
        echo "<li><a href='" . base_url('test-db') . "'>📊 Database Test</a></li>";
        echo "<li><a href='" . base_url('create-test-user') . "'>👤 Create Test User</a></li>";
        echo "<li><a href='" . base_url('login') . "'>🔑 Login Page</a></li>";
        echo "<li><a href='" . base_url('super-admin/unified') . "'>🎛️ SuperAdmin Dashboard</a></li>";
        echo "</ul>";
        
        // Test SuperAdmin controller
        echo "<h2>🎛️ SuperAdmin Controller Test</h2>";
        try {
            $superAdmin = new \App\Controllers\SuperAdmin();
            echo "<p>✅ SuperAdmin controller can be instantiated</p>";
        } catch (\Exception $e) {
            echo "<p>❌ SuperAdmin controller error: " . $e->getMessage() . "</p>";
        }
        
        echo "<hr>";
        echo "<p><strong>Next Steps:</strong></p>";
        echo "<ol>";
        echo "<li>If all tests pass, try accessing the <a href='" . base_url('super-admin/unified') . "'>SuperAdmin Dashboard</a></li>";
        echo "<li>If you get errors, check the <a href='" . base_url('debug') . "'>Debug Info</a> page</li>";
        echo "<li>Create a test user if needed: <a href='" . base_url('create-test-user') . "'>Create Test User</a></li>";
        echo "</ol>";
    }
}
