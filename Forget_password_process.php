<?php
session_start();
include('db_connect.php');

// Ensure CSRF token matches
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header("Location: error.php?error=InvalidCSRFToken");
    exit;
}

// Function to sanitize user input
function test_input($data) {
    return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Get and sanitize email input
$email = test_input($_POST['email']);

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: error.php?error=InvalidEmail");
    exit;
}

// Check if email exists (Using prepared statements)
$sql = "SELECT user_id FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Prepare failed (email lookup): " . $conn->error);
    header("Location: error.php?error=DBError");
    exit;
}
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Generate password reset token regardless of whether email exists to prevent enumeration
$token = bin2hex(random_bytes(32));
$token_expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $user_id = $user['user_id'];

    // Store the reset token and expiry in the database
    $sql = "UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed (token storage): " . $conn->error);
        header("Location: error.php?error=DBError");
        exit;
    }
    $stmt->bind_param("ssi", $token, $token_expiry, $user_id);
    $stmt->execute();
}

// Send password reset email (Avoid exposing whether email exists)
$to = $email;
$subject = "Password Reset Request";
$message = "If you requested a password reset, click the link below:\n\n";
$message .= "http://yourdomain.com/reset_password.php?token=" . $token . "\n\n";
$message .= "This link will expire in one hour.";
$headers = "From: no-reply@yourdomain.com";

mail($to, $subject, $message, $headers);

// Display a generic message (Prevents email enumeration)
header("Location: password_reset_sent.php");
exit;
?>
