<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to delete a poll.";
    exit;
}

// Get poll ID from URL
$poll_id = intval($_GET['poll_id']);
$user_id = $_SESSION['user_id'];

// Check if the poll belongs to the logged-in user
$sql = "SELECT * FROM polls WHERE poll_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $poll_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Delete the poll and associated options and votes
    $sql = "DELETE FROM polls WHERE poll_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $poll_id);
    $stmt->execute();

    $sql = "DELETE FROM options WHERE poll_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $poll_id);
    $stmt->execute();

    $sql = "DELETE FROM votes WHERE poll_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $poll_id);
    $stmt->execute();

    echo "Poll deleted successfully.";
    header("Location: manage_polls.php");
    exit;
} else {
    echo "You do not have permission to delete this poll.";
}
?>
