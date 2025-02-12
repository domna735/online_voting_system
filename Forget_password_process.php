<?php
session_start();
include('db_connect.php');

// Function to sanitize user input
function test_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

$email = test_input($_POST['email']);

// Check if email exists
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $user_id = $user['user_id'];

    // Generate a password reset token
    $token = bin2hex(random_bytes(32));

    // Store the token in the database
    $sql = "UPDATE users SET reset_token = ?, reset_token_expires = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $token, $user_id);
    $stmt->execute();

    // Send reset email
    $to = $email;
    $subject = "Password Reset";
    $message = "Please click the link below to reset your password:\n\n";
    $message .= "http://yourdomain.com/reset_password.php?token=" . $token;
    $headers = "From: no-reply@yourdomain.com";

    mail($to, $subject, $message, $headers);

    echo "A password reset email has been sent to your email address.";
} else {
    echo "No account found with that email address.";
}
?>

