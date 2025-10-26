<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Debug extends Controller
{
    public function index()
    {
        echo "<h1>🔍 Debug Information</h1>";
        
        // Check PHP version
        echo "<h2>PHP Version</h2>";
        echo "<p>PHP Version: " . phpversion() . "</p>";
        
        // Check CodeIgniter
        echo "<h2>CodeIgniter</h2>";
        echo "<p>CI Version: " . \CodeIgniter\CodeIgniter::CI_VERSION . "</p>";
        echo "<p>Environment: " . ENVIRONMENT . "</p>";
        
        // Check database connection
        echo "<h2>Database Connection</h2>";
        try {
            $db = \Config\Database::connect();
            if ($db->connect()) {
                echo "<p>✅ Database connection successful</p>";
                echo "<p>Database: " . $db->getDatabase() . "</p>";
                
                // List tables
                $tables = $db->listTables();
                echo "<p>Tables found: " . count($tables) . "</p>";
                echo "<ul>";
                foreach ($tables as $table) {
                    echo "<li>$table</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>❌ Database connection failed</p>";
            }
        } catch (\Exception $e) {
            echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
        }
        
        // Check session
        echo "<h2>Session Information</h2>";
        $session = session();
        echo "<p>Session ID: " . $session->session_id . "</p>";
        echo "<p>Is Logged In: " . ($session->get('isLoggedIn') ? 'Yes' : 'No') . "</p>";
        echo "<p>User Role: " . ($session->get('role') ?: 'None') . "</p>";
        
        // Check error logs
        echo "<h2>Recent Errors</h2>";
        $logPath = WRITEPATH . 'logs/';
        if (is_dir($logPath)) {
            $files = glob($logPath . '*.log');
            if ($files) {
                $latestLog = max($files);
                echo "<p>Latest log file: " . basename($latestLog) . "</p>";
                $logContent = file_get_contents($latestLog);
                $lines = explode("\n", $logContent);
                $recentLines = array_slice($lines, -10);
                echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 300px; overflow-y: auto;'>";
                echo implode("\n", $recentLines);
                echo "</pre>";
            } else {
                echo "<p>No log files found</p>";
            }
        } else {
            echo "<p>Log directory not found</p>";
        }
        
        // Check routes
        echo "<h2>Available Routes</h2>";
        echo "<p>Current URL: " . current_url() . "</p>";
        echo "<p>Base URL: " . base_url() . "</p>";
        
        // Test links
        echo "<h2>Test Links</h2>";
        echo "<ul>";
        echo "<li><a href='" . base_url('login') . "'>Login Page</a></li>";
        echo "<li><a href='" . base_url('test-db') . "'>Test Database</a></li>";
        echo "<li><a href='" . base_url('create-test-user') . "'>Create Test User</a></li>";
        echo "<li><a href='" . base_url('super-admin/unified') . "'>Super Admin Dashboard</a></li>";
        echo "</ul>";
    }
    
    public function phpinfo()
    {
        phpinfo();
    }
}
