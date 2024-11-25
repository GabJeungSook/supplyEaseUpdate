<?php
// Start the session
session_start();

// Define the base path for included files
$base_path = __DIR__ . '/pages/';

// Check if the user is logged in (you can modify this based on your session variables)
if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
    header('Location: ../login.php');
    exit();
}

// Determine the requested page or default to 'dashboard.php'
$page = isset($_GET['page']) ? $_GET['page'] . '.php' : 'dashboard.php';

// Construct the full path
$full_path = $base_path . $page;

// Check if the file exists
if (!file_exists($full_path)) {
    $full_path = dirname(__DIR__) . '/404.php';
}

// Include the layout, passing the resolved page
include('layout.php');
?>