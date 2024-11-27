<?php
// Database configuration
$host = '127.0.0.1';        // Database host
$db_name = 'supply_ease_new'; // Database name
$username = 'supply_ease_user'; // Database username
$password = 'supply_ease_password'; // Database password

// Create a connection
$conn = new mysqli($host, $username, $password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Uncomment for debugging to confirm connection
// echo "Connected successfully";
?>
