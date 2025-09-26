-- Hospital Management System - Complete Database Reset
-- This will completely clean your database and prepare for fresh migrations

SET FOREIGN_KEY_CHECKS = 0;

-- Drop all possible existing tables
DROP TABLE IF EXISTS `system_logs`;
DROP TABLE IF EXISTS `billing`;
DROP TABLE IF EXISTS `lab_requests`;
DROP TABLE IF EXISTS `prescriptions`;
DROP TABLE IF EXISTS `appointments`;
DROP TABLE IF EXISTS `patients`;
DROP TABLE IF EXISTS `it_staff`;
DROP TABLE IF EXISTS `accountants`;
DROP TABLE IF EXISTS `pharmacists`;
DROP TABLE IF EXISTS `laboratories`;
DROP TABLE IF EXISTS `receptionists`;
DROP TABLE IF EXISTS `nurses`;
DROP TABLE IF EXISTS `doctors`;
DROP TABLE IF EXISTS `users`;

-- Drop any other potential conflicting tables
DROP TABLE IF EXISTS `roles`;
DROP TABLE IF EXISTS `departments`;
DROP TABLE IF EXISTS `rooms`;
DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `security_settings`;
DROP TABLE IF EXISTS `system_backups`;
DROP TABLE IF EXISTS `invoices`;
DROP TABLE IF EXISTS `expenses`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `medicine_dispense`;
DROP TABLE IF EXISTS `pharmacy_inventory`;
DROP TABLE IF EXISTS `lab_inventory`;
DROP TABLE IF EXISTS `lab_results`;
DROP TABLE IF EXISTS `lab_samples`;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `patient_check_ins`;
DROP TABLE IF EXISTS `visitors`;
DROP TABLE IF EXISTS `admissions`;
DROP TABLE IF EXISTS `patient_progress`;
DROP TABLE IF EXISTS `patient_vitals`;
DROP TABLE IF EXISTS `nurse_assignments`;
DROP TABLE IF EXISTS `medical_records`;

-- Drop migration tracking table to start completely fresh
DROP TABLE IF EXISTS `migrations`;

-- Drop any views that might exist
DROP VIEW IF EXISTS `user_roles_view`;
DROP VIEW IF EXISTS `patient_summary_view`;
DROP VIEW IF EXISTS `appointment_summary_view`;

SET FOREIGN_KEY_CHECKS = 1;

-- Success message
SELECT 'Database completely cleaned! Ready for fresh migrations.' as status,
       'Run: php spark migrate' as next_step;
