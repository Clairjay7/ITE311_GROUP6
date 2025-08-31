<?php
// Database configuration
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'ite311_group6';

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>Checking users table...</h2>";

// Check if users table exists
$result = $conn->query("SHOW TABLES LIKE 'users'");

if ($result->num_rows === 0) {
    echo "<p>Users table does not exist. Creating table...</p>";
    
    // SQL to create users table
    $sql = "CREATE TABLE IF NOT EXISTS `users` (
        `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `username` varchar(50) NOT NULL,
        `email` varchar(100) NOT NULL,
        `password_hash` varchar(255) NOT NULL,
        `first_name` varchar(50) DEFAULT NULL,
        `last_name` varchar(50) DEFAULT NULL,
        `role` enum('admin','doctor','nurse','patient','it_staff','laboratory','accountant') DEFAULT 'patient',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `username` (`username`),
        UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p>Users table created successfully!</p>";
    } else {
        die("<p>Error creating users table: " . $conn->error . "</p>");
    }
} else {
    echo "<p>Users table exists.</p>";
}

// Check if any users exist
$result = $conn->query("SELECT COUNT(*) as count FROM users");
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    echo "<h3>No users found. Creating test user...</h3>";
    
    // Create a test user
    $username = 'admin';
    $email = 'admin@example.com';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $role = 'admin';
    
    $sql = "INSERT INTO users (username, email, password_hash, role, first_name, last_name) 
            VALUES (?, ?, ?, ?, 'System', 'Administrator')";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $email, $password, $role);
    
    if ($stmt->execute()) {
        echo "<p>Test user created successfully!</p>";
        echo "<p>Username: admin</p>";
        echo "<p>Password: admin123</p>";
    } else {
        echo "<p>Error creating test user: " . $stmt->error . "</p>";
    }
} else {
    echo "<h3>Existing users:</h3>";
    $result = $conn->query("SELECT id, username, email, role FROM users");
    
    if ($result->num_rows > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . htmlspecialchars($row['role']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}

$conn->close();

echo "<p><a href='direct_db_check.php'>Back to database check</a></p>";
