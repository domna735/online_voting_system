<?php
session_start();
include('db_connect.php');
// Include the central sanitization functions
include('sanitization.php');

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header("Location: error.php?error=InvalidCSRFToken");
    exit;
}

// Retrieve and sanitize form data using our central function
$login_id = sanitize_input($_POST['login_id']);
$password = $_POST['password']; // Always handle passwords raw for verification

// Check for brute force attempts
$ip = $_SERVER['REMOTE_ADDR'];
$sql = "SELECT COUNT(*) as attempts FROM login_attempts 
        WHERE ip_address = ? AND attempt_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $ip);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$attempts = $row['attempts'];

// Clean up old login attempts from database
$sql = "DELETE FROM login_attempts WHERE attempt_time < DATE_SUB(NOW(), INTERVAL 20 MINUTE)";
$stmt = $conn->prepare($sql);
$stmt->execute();

// If too many attempts from this IP, block login
if ($attempts >= 5) {
    error_log("Too many login attempts from IP: " . $ip);
    header('Location: login_error.php?error=TooManyAttemptsIP');
    exit;
}

// Prepare and execute the query to fetch the user record
$sql = "SELECT * FROM users WHERE login_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $login_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    // Check if account is locked for a period due to failed attempts
    // Convert current time minus 15 minutes to hk timezone (because stored hk time in database)
    $currentTime = new DateTime('now', new DateTimeZone('Asia/Hong_Kong'));
    $currentTime->modify('-15 minutes');
    $convertedTime = $currentTime->format('Y-m-d H:i:s');
    
    if ($user['login_attempts'] >= 5 && $user['last_attempt'] > $convertedTime) {
        error_log("Account locked for user: " . $login_id);
        header('Location: login_error.php?error=TooManyAttemptsAcc');
        exit;
    }
    
    // Verify the provided password against the stored hash
    if (password_verify($password, $user['password'])) {
        // Reset login attempts on successful login
        $sql = "UPDATE users SET login_attempts = 0, last_attempt = NULL WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user['user_id']);
        $stmt->execute();
        
        // Clear login attempts from IP
        $sql = "DELETE FROM login_attempts WHERE ip_address = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $ip);
        $stmt->execute();
        
        // Regenerate the session ID upon successful authentication
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['nickname'] = $user['nickname'];
        $_SESSION['login_success'] = true;
        $_SESSION['last_activity'] = time();

        // Set secure cookie with additional security flags
        setcookie(
            "user_id",
            $user['user_id'],
            [
                'expires'  => time() + (86400 * 30), // 30 days from now
                'path'     => '/',
                'secure'   => true,    // Only send over HTTPS
                'httponly' => true,    // Prevent JavaScript access
                'samesite' => 'Strict' // Prevent CSRF
            ]
        );
        
        // Log the successful login
        error_log("Successful login for user: " . $login_id);
        
        header('Location: index.php');
        exit;
    } else {
        // Increment login attempts for incorrect password
        $sql = "UPDATE users SET login_attempts = login_attempts + 1, last_attempt = NOW() WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user['user_id']);
        $stmt->execute();
        
        // Log the failed attempt, storing the IP in the login_attempts table
        $sql = "INSERT INTO login_attempts (ip_address, attempt_time) VALUES (?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $ip);
        $stmt->execute();
        
        error_log("Failed login attempt for user: " . $login_id . " from IP: " . $ip);
        header('Location: login_error.php');
        exit;
    }
} else {
    // Log failed attempt for non-existent user (to prevent enumeration)
    $sql = "INSERT INTO login_attempts (ip_address, attempt_time) VALUES (?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    
    error_log("Failed login attempt for non-existent user from IP: " . $ip);
    header('Location: login_error.php');
    exit;
}
?>