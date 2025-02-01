<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = ""; // Default password is empty for XAMPP
$dbname = "online_voting_system"; // The name of your database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
