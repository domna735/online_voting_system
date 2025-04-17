<?php
session_start();
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

// Retrieve and validate input data using filter_input
$poll_id = filter_input(INPUT_POST, 'poll_id', FILTER_VALIDATE_INT);
if ($poll_id === false || $poll_id <= 0) {
    header("Location: error.php?error=InvalidPollId");
    exit;
}

// Retrieve and sanitize comment text
$comment_text = trim(filter_input(INPUT_POST, 'comment_text', FILTER_SANITIZE_STRING));
if (empty($comment_text)) {
    header("Location: error.php?error=EmptyComment");
    exit;
}

// Option 1: Sanitize before storing (as you're doing)
// Option 2: Store raw comment and sanitize on output (consider for future improvements)
$comment_text_sanitized = htmlspecialchars($comment_text, ENT_QUOTES, 'UTF-8');

$user_id = $_SESSION['user_id'];

// Insert comment into the database using a prepared statement
$sql = "INSERT INTO comments (poll_id, user_id, comment_text) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Prepare failed (comment insert): " . $conn->error);
    header("Location: error.php?error=DBError");
    exit;
}
$stmt->bind_param("iis", $poll_id, $user_id, $comment_text_sanitized);

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