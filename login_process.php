<?php
session_start();
include('db_connect.php');

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header("Location: error.php?error=InvalidCSRFToken");
    exit;
}

// Function to sanitize user input
function test_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Retrieve and sanitize form data
$login_id = test_input($_POST['login_id']);
$password = $_POST['password']; // Always handle passwords raw for verification

// Prepare and execute the query to fetch the user record
$sql = "SELECT * FROM users WHERE login_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $login_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    // Verify the provided password against the stored hash
    if (password_verify($password, $user['password'])) {
        // Regenerate the session ID upon successful authentication to mitigate session fixation
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['nickname'] = $user['nickname'];
        $_SESSION['login_success'] = true;

        // Set a secure cookie for user authentication with secure, HttpOnly, and SameSite flags
        setcookie(
            "user_id",
            $user['user_id'],
            [
                'expires'  => time() + (86400 * 30), // 30 days from now
                'path'     => '/',
                'secure'   => true,    // Use true if you're serving over HTTPS
                'httponly' => true,
                'samesite' => 'Strict' // Adjust as needed (or 'Lax')
            ]
        );
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