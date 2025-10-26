<?php
// Database credentials
$host = 'localhost';
$user = 'root';
$pass = '';

// Create connection
$conn = new mysqli($host, $user, $pass);

// Check connection
if ($conn->connect_error) {
    die("<h2>Connection failed:</h2> " . $conn->connect_error);
}

echo "<h2>Connected to MySQL server successfully!</h2>";

// List all databases
echo "<h3>Available Databases:</h3>";
$result = $conn->query("SHOW DATABASES");

echo "<ul>";
while ($row = $result->fetch_array()) {
    $dbName = $row[0];
    echo "<li>$dbName";
    
    // Check if database has a users table
    $db = new mysqli($host, $user, $pass, $dbName);
    if ($tables = $db->query("SHOW TABLES LIKE 'users'")) {
        if ($tables->num_rows > 0) {
            $count = $db->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
            echo " - <strong>Has users table ($count users)</strong>";
            
            // Show first 5 users if available
            if ($count > 0) {
                $users = $db->query("SELECT username, email, role FROM users LIMIT 5");
                echo "<ul>";
                while ($user = $users->fetch_assoc()) {
                    echo "<li>{$user['username']} ({$user['email']}) - {$user['role']}</li>";
                }
                if ($count > 5) echo "<li>... and " . ($count - 5) . " more</li>";
                echo "</ul>";
            }
        }
    }
    echo "</li>";
    $db->close();
}
echo "</ul>";

$conn->close();
?>
