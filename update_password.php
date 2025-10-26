<?php
// Database configuration
$config = [
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'hospital_management',
    'port' => 3306
];

// Create connection
$conn = new mysqli(
    $config['hostname'],
    $config['username'],
    $config['password'],
    $config['database'],
    3306
);

// Check connection
if ($conn->connect_error) {
    die("<p style='color:red'>❌ Connection failed: " . $conn->connect_error . "</p>");
}
echo "<p style='color:green'>✅ Connected to database successfully</p>";

// Update superadmin password
$username = 'superadmin';
$password = 'SuperAdmin@123';
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE username = ?");
$stmt->bind_param("ss", $hashedPassword, $username);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo "<p style='color:green'>✅ Password updated successfully for user: $username</p>";
        echo "<p>New password hash: $hashedPassword</p>";
    } else {
        echo "<p>No rows updated. User might not exist or password is the same.</p>";
    }
} else {
    echo "<p style='color:red'>❌ Error updating password: " . $stmt->error . "</p>";
}

// Verify the update
$stmt = $conn->prepare("SELECT password_hash FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password_hash'])) {
        echo "<p style='color:green'>✅ Password verification successful!</p>";
    } else {
        echo "<p style='color:red'>❌ Password verification failed after update</p>";
    }
}

$conn->close();
?>

<p><a href="/Group6/auth/login">Go to Login Page</a></p>
