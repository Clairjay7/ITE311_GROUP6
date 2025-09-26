<?php

/**
 * Hospital Management System - Database Migration Runner
 * 
 * Run this file to create all database tables for the HMS system.
 * Make sure your database configuration is correct in app/Config/Database.php
 */

require_once 'vendor/autoload.php';

// Bootstrap CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();

echo "🏥 Hospital Management System - Database Setup\n";
echo "=============================================\n\n";

try {
    // Get migration service
    $migrate = \Config\Services::migrations();
    
    echo "📋 Running database migrations...\n\n";
    
    // Run all migrations
    $migrate->latest();
    
    echo "✅ All migrations completed successfully!\n\n";
    
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
    
    echo "🎉 Database setup complete! Your HMS system is ready to use.\n";
    echo "💡 Next steps:\n";
    echo "   - Run the UserSeeder to create default user accounts\n";
    echo "   - Configure your web server to point to the public/ directory\n";
    echo "   - Access your HMS system through the browser\n\n";
    
} catch (\Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    echo "💡 Please check your database configuration and try again.\n";
}
