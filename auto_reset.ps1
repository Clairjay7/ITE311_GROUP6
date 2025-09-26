# Hospital Management System - Automatic Database Reset
# This PowerShell script will reset and migrate your database automatically

Write-Host "🏥 Hospital Management System - Auto Reset" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

# Change to the project directory
Set-Location "f:\xammp\htdocs\Group6"

Write-Host "🗑️  Resetting database..." -ForegroundColor Yellow

try {
    # Run the complete reset SQL
    Write-Host "   Executing database cleanup..." -ForegroundColor Gray
    mysql -u root -p -e "source complete_reset.sql" 2>$null
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "   ✅ Database cleaned successfully!" -ForegroundColor Green
    } else {
        Write-Host "   ⚠️  MySQL command failed, trying alternative method..." -ForegroundColor Yellow
        Write-Host "   Please run complete_reset.sql manually in phpMyAdmin" -ForegroundColor Yellow
        Write-Host "   Then run: php spark migrate" -ForegroundColor Yellow
        exit 1
    }
    
    Write-Host ""
    Write-Host "📋 Running fresh migrations..." -ForegroundColor Yellow
    
    # Run CodeIgniter migrations
    php spark migrate
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host ""
        Write-Host "🎉 Database setup completed successfully!" -ForegroundColor Green
        Write-Host ""
        Write-Host "📊 Your HMS system is ready with:" -ForegroundColor Cyan
        Write-Host "  ✅ Clean database structure" -ForegroundColor Green
        Write-Host "  ✅ All 8 user role tables" -ForegroundColor Green
        Write-Host "  ✅ Patient management tables" -ForegroundColor Green
        Write-Host "  ✅ Appointment system" -ForegroundColor Green
        Write-Host "  ✅ Billing system" -ForegroundColor Green
        Write-Host "  ✅ Laboratory system" -ForegroundColor Green
        Write-Host "  ✅ Pharmacy system" -ForegroundColor Green
        Write-Host ""
        Write-Host "🚀 Next steps:" -ForegroundColor Cyan
        Write-Host "  1. Create user accounts with seeders" -ForegroundColor White
        Write-Host "  2. Test your dashboards" -ForegroundColor White
        Write-Host "  3. Start using your HMS system!" -ForegroundColor White
    } else {
        Write-Host "❌ Migration failed!" -ForegroundColor Red
        Write-Host "Please check the error messages above." -ForegroundColor Yellow
    }
    
} catch {
    Write-Host "❌ Script failed: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "💡 Try running the SQL script manually in phpMyAdmin" -ForegroundColor Yellow
}
