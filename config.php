<?php
// Database configuration
$host = 'localhost';        // Database host
$db_name = 'supply_ease_new'; // Database name
$username = 'root'; // Database username
$password = ''; // Database password

// Create a connection
$conn = new mysqli($host, $username, $password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Uncomment for debugging to confirm connection
// echo "Connected successfully";
?>
