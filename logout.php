<?php
ini_set('session.cookie_secure', '1'); // Enable Secure flag
ini_set('session.cookie_httponly', '1'); // Enable HttpOnly flag
ini_set('session.cookie_samesite', 'Strict'); // Set SameSite attribute
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_unset();
session_destroy();

// Delete the user_id cookie securely
setcookie("user_id", "", time() - 3600, "/", "", true, true);

// Redirect to home page
header("Location: index.php");
exit;
?>