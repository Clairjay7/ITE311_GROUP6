<?php
// Load CodeIgniter framework
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../app/Config/Paths.php';

// Set environment
define('ENVIRONMENT', 'development');

// Reset superadmin password
$db = \Config\Database::connect();

// Hash the new password
$password = 'Admin@123';
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Update the password
$db->table('users')
   ->where('username', 'superadmin')
   ->update(['password_hash' => $hashedPassword]);

echo "Password for superadmin has been reset to: $password<br>";

// Verify the update
$user = $db->table('users')
          ->where('username', 'superadmin')
          ->get()
          ->getRowArray();

if ($user && password_verify($password, $user['password_hash'])) {
    echo "Password verification successful!<br>";
    echo "You can now login with:<br>";
    echo "Username: superadmin<br>";
    echo "Password: $password<br>";
    echo "<a href='../public/login'>Go to login page</a>";
} else {
    echo "Password reset failed. Please check the database connection.";
}

// Delete this file for security
@unlink(__FILE__);
