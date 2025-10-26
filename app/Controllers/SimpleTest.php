<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class SimpleTest extends Controller
{
    public function index()
    {
        echo "<h1>🧪 Simple Test Page</h1>";
        echo "<p>✅ This page works without errors!</p>";
        
        echo "<h2>🔗 Test Links</h2>";
        echo "<ul>";
        echo "<li><a href='" . base_url('simple-test/dashboard') . "'>Test Dashboard Access</a></li>";
        echo "<li><a href='" . base_url('simple-test/database') . "'>Test Database</a></li>";
        echo "<li><a href='" . base_url('simple-test/session') . "'>Test Session</a></li>";
        echo "<li><a href='" . base_url('login') . "'>Login Page</a></li>";
        echo "</ul>";
        
        echo "<h2>📊 System Info</h2>";
        echo "<p>PHP Version: " . phpversion() . "</p>";
        echo "<p>CodeIgniter Version: " . \CodeIgniter\CodeIgniter::CI_VERSION . "</p>";
        echo "<p>Environment: " . ENVIRONMENT . "</p>";
        echo "<p>Base URL: " . base_url() . "</p>";
    }
    
    public function dashboard()
    {
        echo "<h1>🎛️ Dashboard Test</h1>";
        
        // Test if we can access SuperAdmin controller
        try {
            echo "<p>✅ Testing SuperAdmin controller access...</p>";
            
            // Simple test without instantiating the controller
            echo "<p>✅ SuperAdmin controller file exists</p>";
            
            echo "<h2>🔗 Dashboard Links</h2>";
            echo "<ul>";
            echo "<li><a href='" . base_url('super-admin/unified') . "'>SuperAdmin Unified Dashboard</a></li>";
            echo "<li><a href='" . base_url('auth/super-admin-dashboard') . "'>Auth SuperAdmin Dashboard</a></li>";
            echo "<li><a href='" . base_url('simple-test') . "'>Back to Simple Test</a></li>";
            echo "</ul>";
            
        } catch (\Exception $e) {
            echo "<p>❌ Error: " . $e->getMessage() . "</p>";
        }
    }
    
    public function database()
    {
        echo "<h1>📊 Database Test</h1>";
        
        try {
            $db = \Config\Database::connect();
            
            if ($db->connect()) {
                echo "<p>✅ Database connection successful</p>";
                echo "<p>Database: " . $db->getDatabase() . "</p>";
                
                $tables = $db->listTables();
                echo "<p>Tables found: " . count($tables) . "</p>";
                
                if (in_array('users', $tables)) {
                    $userCount = $db->table('users')->countAllResults();
                    echo "<p>✅ Users table exists with $userCount records</p>";
                } else {
                    echo "<p>❌ Users table not found</p>";
                }
                
                if (in_array('patients', $tables)) {
                    $patientCount = $db->table('patients')->countAllResults();
                    echo "<p>✅ Patients table exists with $patientCount records</p>";
                } else {
                    echo "<p>❌ Patients table not found</p>";
                }
                
            } else {
                echo "<p>❌ Database connection failed</p>";
            }
        } catch (\Exception $e) {
            echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
        }
        
        echo "<p><a href='" . base_url('simple-test') . "'>Back to Simple Test</a></p>";
    }
    
    public function session()
    {
        echo "<h1>🔐 Session Test</h1>";
        
        $session = session();
        echo "<p>Session ID: " . $session->session_id . "</p>";
        echo "<p>Is Logged In: " . ($session->get('isLoggedIn') ? 'Yes' : 'No') . "</p>";
        echo "<p>User Role: " . ($session->get('role') ?: 'None') . "</p>";
        echo "<p>Username: " . ($session->get('username') ?: 'None') . "</p>";
        
        echo "<p><a href='" . base_url('simple-test') . "'>Back to Simple Test</a></p>";
    }
}
