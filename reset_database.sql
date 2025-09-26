-- Hospital Management System - Database Reset
-- Run this in phpMyAdmin or MySQL command line to clean up conflicts

SET FOREIGN_KEY_CHECKS = 0;

-- Drop existing tables if they exist
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

-- Drop migration tracking table to start fresh
DROP TABLE IF EXISTS `migrations`;

SET FOREIGN_KEY_CHECKS = 1;

-- Success message
SELECT 'Database cleaned successfully! Now run: php spark migrate' as message;
