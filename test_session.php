<?php
require 'vendor/autoload.php';

$app = \Config\Services::codeigniter();
$app->initialize();

$session = \Config\Services::session();

echo "<h2>Session Debug</h2>";
echo "<pre>";
echo "Is Logged In: " . ($session->get('isLoggedIn') ? 'YES' : 'NO') . "\n";
echo "Role: " . ($session->get('role') ?? 'NONE') . "\n";
echo "User ID: " . ($session->get('user_id') ?? 'NONE') . "\n";
echo "Username: " . ($session->get('username') ?? 'NONE') . "\n";
echo "\nAll Session Data:\n";
print_r($session->get());
echo "</pre>";

