<?php
/**
 * Test SuperAdmin Access
 */

echo "<h1>🔍 SuperAdmin Dashboard Test</h1>";

// Start session to check current user
session_start();

echo "<h2>📊 Current Session Data:</h2>";
if (empty($_SESSION)) {
    echo "❌ <strong>No active session - You need to login first!</strong><br><br>";
    echo "🔗 <a href='/Group6/login'>Go to Login Page</a><br>";
} else {
    echo "✅ Active session found:<br>";
    foreach ($_SESSION as $key => $value) {
        if ($key === 'isLoggedIn') {
            echo "- $key: " . ($value ? '✅ TRUE' : '❌ FALSE') . "<br>";
        } else {
            echo "- $key: $value<br>";
        }
    }
    
    echo "<h2>🔐 Access Check:</h2>";
    $isLoggedIn = $_SESSION['isLoggedIn'] ?? false;
    $role = $_SESSION['role'] ?? 'none';
    
    if (!$isLoggedIn) {
        echo "❌ Not logged in<br>";
    } elseif ($role !== 'superadmin') {
        echo "❌ Wrong role: '$role' (need 'superadmin')<br>";
    } else {
        echo "✅ Access granted! You should be able to access SuperAdmin dashboard<br>";
    }
}

echo "<h2>🔗 Test Links:</h2>";
echo '<a href="/Group6/">Homepage</a><br>';
echo '<a href="/Group6/login">Login Page</a><br>';
echo '<a href="/Group6/auth/super-admin-dashboard">SuperAdmin Dashboard</a><br>';

echo "<h2>💡 SuperAdmin Login Credentials:</h2>";
echo "<strong>Username:</strong> superadmin<br>";
echo "<strong>Password:</strong> Admin@123<br>";
?>
