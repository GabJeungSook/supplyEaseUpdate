<?php
// Include database configuration
include('config.php');

// User data
$name = 'Administrator';
$email = 'admin@gmail.com';
$password = 'password'; // Plain text password
$role = 'admin';

// Hash the password using PHP's password_hash() function
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Prepare the SQL query to insert the user
$sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";

// Prepare the statement
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die('Error preparing the statement: ' . $conn->error);
}

// Bind parameters to the SQL query
$stmt->bind_param('ssss', $name, $email, $hashed_password, $role);

// Execute the query
if ($stmt->execute()) {
    echo 'Administrator account created successfully.';
} else {
    echo 'Error: ' . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>