<?php
ini_set('session.cookie_secure', '1'); // Enable Secure flag
ini_set('session.cookie_httponly', '1'); // Enable HttpOnly flag
ini_set('session.cookie_samesite', 'Strict'); // Set SameSite attribute
session_start();

// Include sanitization functions
include('sanitization.php'); // Adjust the path if needed

// Include the database connection file
include('db_connect.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Verify CSRF token to protect against CSRF attacks
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header("Location: error.php?error=InvalidCSRFToken");
    exit;
}

// Retrieve and validate input data
// For poll_id we are using filter_input with validation
$poll_id = filter_input(INPUT_POST, 'poll_id', FILTER_VALIDATE_INT);
if ($poll_id === false || $poll_id <= 0) {
    header("Location: error.php?error=InvalidPollId");
    exit;
}

// Retrieve raw comment text (using FILTER_DEFAULT so it is not altered too early)
$raw_comment_text = filter_input(INPUT_POST, 'comment_text', FILTER_DEFAULT);

// Use the custom sanitization function to clean the comment text
$comment_text = sanitize_input($raw_comment_text);

// Check for an empty comment (after sanitization)
if (empty($comment_text)) {
    header("Location: error.php?error=EmptyComment");
    exit;
}

$user_id = $_SESSION['user_id'];

// Insert the comment into the database using a prepared statement
$sql = "INSERT INTO comments (poll_id, user_id, comment_text) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Prepare failed (comment insert): " . $conn->error);
    header("Location: error.php?error=DBError");
    exit;
}
$stmt->bind_param("iis", $poll_id, $user_id, $comment_text);

if ($stmt->execute()) {
    // Comment inserted successfully; redirect back to the vote page
    header("Location: vote.php?poll_id=" . $poll_id);
    exit;
} else {
    error_log("Error inserting comment: " . $stmt->error);
    header("Location: error.php?error=CommentFailed");
    exit;
}
$stmt->close();
?>