<?php
// Database configuration
$servername = "localhost";
$username   = "root";
$password   = ""; // For XAMPP, the default is empty
$dbname     = "3117_online_voting_system";

// Create connection using mysqli
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection and handle errors securely
if ($conn->connect_error) {
    // Log the error for administrator review
    error_log("Database connection failed: " . $conn->connect_error);
    // Redirect to a generic error page without revealing details
    header('Location: error.php?error=DBConnection');
    exit;
}

// Set the character encoding to ensure correct data handling
$conn->set_charset("utf8mb4");
?>