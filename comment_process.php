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

// Validate and sanitize input data
$poll_id = isset($_POST['poll_id']) ? intval($_POST['poll_id']) : 0;
if ($poll_id <= 0) {
    header("Location: error.php?error=InvalidPollId");
    exit;
}

$comment_text = isset($_POST['comment_text']) ? trim($_POST['comment_text']) : '';
if (empty($comment_text)) {
    header("Location: error.php?error=EmptyComment");
    exit;
}

// Sanitize comment text to prevent XSS when later displayed.
// Alternatively, you can store raw text and sanitize on output.
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
    // Redirect the user back to the voting page for the poll.
    header("Location: vote.php?poll_id=" . $poll_id);
    exit;
} else {
    error_log("Error inserting comment: " . $stmt->error);
    header("Location: error.php?error=CommentFailed");
    exit;
}
?>