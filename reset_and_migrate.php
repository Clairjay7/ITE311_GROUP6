<?php

/**
 * Hospital Management System - Database Reset and Migration
 * 
 * This script will safely reset and recreate all database tables
 */

require_once 'vendor/autoload.php';

// Bootstrap CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();

echo "🏥 Hospital Management System - Database Reset\n";
echo "=============================================\n\n";

try {
    // Get database connection
    $db = \Config\Database::connect();
    
    echo "🗑️  Dropping existing tables (if any)...\n";
    
    // List of tables to drop in correct order (reverse of creation)
    $tables = [
        'system_logs',
        'billing',
        'lab_requests', 
        'prescriptions',
        'appointments',
        'patients',
        'it_staff',
        'accountants',
        'pharmacists',
        'laboratories',
        'receptionists',
        'nurses',
        'doctors',
        'users'
    ];
    
    // Disable foreign key checks
    $db->query('SET FOREIGN_KEY_CHECKS = 0');
    
    foreach ($tables as $table) {
        if ($db->tableExists($table)) {
            $db->query("DROP TABLE `{$table}`");
            echo "  ✅ Dropped table: {$table}\n";
        }
    }
    
    // Re-enable foreign key checks
    $db->query('SET FOREIGN_KEY_CHECKS = 1');
    
    echo "\n📋 Running fresh migrations...\n";
    
    // Get migration service
    $migrate = \Config\Services::migrations();
    
    // Run all migrations
    $migrate->latest();
    
    echo "\n✅ All migrations completed successfully!\n\n";
    
    echo "📊 Created Tables:\n";
    echo "  1. ✅ users - Main user accounts for all roles\n";
    echo "  2. ✅ doctors - Doctor-specific information\n";
    echo "  3. ✅ nurses - Nurse-specific information\n";
    echo "  4. ✅ receptionists - Receptionist-specific information\n";
    echo "  5. ✅ laboratories - Laboratory staff information\n";
    echo "  6. ✅ pharmacists - Pharmacist-specific information\n";
    echo "  7. ✅ accountants - Accountant-specific information\n";
    echo "  8. ✅ it_staff - IT staff information\n";
    echo "  9. ✅ patients - Patient records\n";
    echo " 10. ✅ appointments - Appointment scheduling\n";
    echo " 11. ✅ prescriptions - Prescription management\n";
    echo " 12. ✅ lab_requests - Laboratory test requests\n";
    echo " 13. ✅ billing - Billing and payment records\n";
    echo " 14. ✅ system_logs - System activity logs\n\n";
    
    echo "🎉 Database reset and setup complete!\n";
    echo "💡 Your HMS system is ready with a clean database.\n\n";
    
} catch (\Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    echo "💡 Please check your database configuration and try again.\n";
}
