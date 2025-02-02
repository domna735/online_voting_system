<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to leave a comment.";
    exit;
}

// Get form data
$poll_id = intval($_POST['poll_id']);
$comment_text = htmlspecialchars($_POST['comment_text']);
$user_id = $_SESSION['user_id'];

// Insert comment into database
$sql = "INSERT INTO comments (poll_id, user_id, comment_text) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $poll_id, $user_id, $comment_text);

if ($stmt->execute()) {
    echo "Comment submitted successfully!";
    header("Location: vote.php?poll_id=" . $poll_id);
    exit;
} else {
    echo "Error: " . $stmt->error;
}
?>
