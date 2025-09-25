<?php
require_once 'vendor/autoload.php';

use CodeIgniter\CodeIgniter;

// Manually verify the password
$password = 'SuperAdmin@123';
$storedHash = '$2y$10$GVMBvtBSpQhEDHdD1EKgxu824P4ikUq/ykgP1Is/L2y0ZQotsSnZm';

$result = password_verify($password, $storedHash);

echo "Password verification result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";

// Check what's being received from the form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "\nForm data received:\n";
    echo "Username: " . ($_POST['username'] ?? 'not set') . "\n";
    echo "Password: " . ($_POST['password'] ?? 'not set') . "\n";
    echo "Role: " . ($_POST['role'] ?? 'not set') . "\n";
}
