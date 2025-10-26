<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class DatabaseAnalyzer extends Controller
{
    public function analyzeDatabase()
    {
        try {
            $db = \Config\Database::connect();
            
            if (!$db->connect()) {
                echo "❌ Database connection failed!";
                return;
            }

            echo "<h1>🔍 Complete Database Analysis</h1>";
            echo "<p>Database: <strong>" . $db->getDatabase() . "</strong></p>";
            
            // Get all tables
            $tables = $db->listTables();
            
            echo "<h2>📊 Database Summary</h2>";
            echo "<ul>";
            foreach ($tables as $table) {
                $count = $db->table($table)->countAllResults();
                echo "<li><strong>$table</strong>: $count records</li>";
            }
            echo "</ul>";

            // Analyze each table in detail
            foreach ($tables as $table) {
                $this->analyzeTable($db, $table);
            }
            
            // Generate recommendations
            $this->generateRecommendations($db, $tables);
            
        } catch (\Exception $e) {
            echo "❌ Error: " . $e->getMessage();
        }
    }
    
    private function analyzeTable($db, $tableName)
    {
        echo "<div style='border: 1px solid #ddd; margin: 20px 0; padding: 15px; border-radius: 5px;'>";
        echo "<h3>📋 Table: $tableName</h3>";
        
        // Get table structure
        $fields = $db->getFieldData($tableName);
        
        echo "<h4>Structure:</h4>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-bottom: 15px;'>";
        echo "<tr style='background: #f5f5f5;'>";
        echo "<th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th>";
        echo "</tr>";
        
        foreach ($fields as $field) {
            echo "<tr>";
            echo "<td><strong>{$field->name}</strong></td>";
            echo "<td>{$field->type}</td>";
            echo "<td>" . ($field->nullable ? 'YES' : 'NO') . "</td>";
            echo "<td>" . ($field->primary_key ? 'PRI' : '') . "</td>";
            echo "<td>" . ($field->default ?? 'NULL') . "</td>";
            echo "<td></td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Get sample data
        $sampleData = $db->table($tableName)->limit(2)->get()->getResultArray();
        
        if (!empty($sampleData)) {
            echo "<h4>Sample Data (first 2 rows):</h4>";
            echo "<table border='1' style='border-collapse: collapse; width: 100%; font-size: 12px;'>";
            
            // Header
            echo "<tr style='background: #e8f4f8;'>";
            foreach (array_keys($sampleData[0]) as $column) {
                echo "<th>$column</th>";
            }
            echo "</tr>";
            
            // Data rows
            foreach ($sampleData as $row) {
                echo "<tr>";
                foreach ($row as $value) {
                    $displayValue = $value === null ? '<em>NULL</em>' : htmlspecialchars(substr($value, 0, 50));
                    echo "<td>$displayValue</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p><em>No data in this table</em></p>";
        }
        
        echo "</div>";
    }
    
    private function generateRecommendations($db, $tables)
    {
        echo "<h2>💡 Recommendations for SuperAdmin Dashboard</h2>";
        
        // Check what tables exist and suggest correct field mappings
        echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 5px;'>";
        
        if (in_array('users', $tables)) {
            $userFields = $db->getFieldNames('users');
            echo "<h3>✅ Users Table Configuration:</h3>";
            echo "<ul>";
            echo "<li>Table exists: <strong>users</strong></li>";
            echo "<li>Available fields: " . implode(', ', $userFields) . "</li>";
            
            if (in_array('first_name', $userFields) && in_array('last_name', $userFields)) {
                echo "<li>✅ Use: <code>first_name + last_name</code> for full name</li>";
            }
            if (in_array('status', $userFields)) {
                echo "<li>✅ Use: <code>status</code> field for active/inactive</li>";
            }
            echo "</ul>";
        }
        
        if (in_array('patients', $tables)) {
            $patientFields = $db->getFieldNames('patients');
            echo "<h3>✅ Patients Table Configuration:</h3>";
            echo "<ul>";
            echo "<li>Table exists: <strong>patients</strong></li>";
            echo "<li>Available fields: " . implode(', ', $patientFields) . "</li>";
            echo "</ul>";
        }
        
        if (in_array('appointments', $tables)) {
            $appointmentFields = $db->getFieldNames('appointments');
            echo "<h3>✅ Appointments Table Configuration:</h3>";
            echo "<ul>";
            echo "<li>Table exists: <strong>appointments</strong></li>";
            echo "<li>Available fields: " . implode(', ', $appointmentFields) . "</li>";
            if (in_array('appointment_date', $appointmentFields)) {
                echo "<li>✅ Use: <code>appointment_date</code> for filtering today's appointments</li>";
            }
            echo "</ul>";
        }
        
        if (in_array('doctors', $tables)) {
            echo "<h3>✅ Doctors Table exists - use for doctor count</h3>";
        } else {
            echo "<h3>⚠️ No doctors table - use users with role='doctor'</h3>";
        }
        
        if (in_array('billing', $tables)) {
            echo "<h3>✅ Billing Table exists - use for pending bills</h3>";
        } else {
            echo "<h3>⚠️ No billing table - set pending bills to 0</h3>";
        }
        
        echo "</div>";
        
        // Generate code snippets
        echo "<h3>📝 Recommended Code Updates:</h3>";
        echo "<textarea style='width: 100%; height: 200px; font-family: monospace;'>";
        echo "// Update your SuperAdmin controller statistics:\n";
        echo "\$totalDoctors = " . (in_array('doctors', $tables) ? "\$db->table('doctors')->countAllResults();" : "\$this->userModel->where('role', 'doctor')->countAllResults();") . "\n";
        echo "\$totalPatients = " . (in_array('patients', $tables) ? "\$db->table('patients')->countAllResults();" : "0; // No patients table found") . "\n";
        echo "\$todaysAppointments = " . (in_array('appointments', $tables) ? "\$db->table('appointments')->where('DATE(appointment_date)', date('Y-m-d'))->countAllResults();" : "0; // No appointments table found") . "\n";
        echo "\$pendingBills = " . (in_array('billing', $tables) ? "\$db->table('billing')->where('status', 'pending')->countAllResults();" : "0; // No billing table found") . "\n";
        echo "</textarea>";
    }
}
