<?php
session_start();
include('db_connect.php');

// Function to sanitize user input
function test_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Get form data
$login_id = test_input($_POST['login_id']);
$password = $_POST['password'];

// Fetch user from database
$sql = "SELECT * FROM users WHERE login_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $login_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    // Verify password
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['nickname'] = $user['nickname'];
        $_SESSION['login_success'] = true; // Set a flag to indicate successful login
        
        header('Location: index.php');
        exit;
    } else {
        header('Location: login_error.php');
        exit;
    }
} else {
    header('Location: login_error.php');
    exit;
}
?>