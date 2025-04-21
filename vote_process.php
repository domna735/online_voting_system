<?php
ini_set('session.cookie_secure', '1'); // Enable Secure flag
ini_set('session.cookie_httponly', '1'); // Enable HttpOnly flag
ini_set('session.cookie_samesite', 'Strict'); // Set SameSite attribute
session_start();
include('db_connect.php');
// Include the central sanitization functions if needed
include('sanitization.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header("Location: error.php?error=InvalidCSRFToken");
    exit;
}

// Retrieve and validate form data using filter_input for integers
$poll_id   = filter_input(INPUT_POST, 'poll_id', FILTER_VALIDATE_INT);
$option_id = filter_input(INPUT_POST, 'option_id', FILTER_VALIDATE_INT);
$user_id   = $_SESSION['user_id'];

if ($poll_id === false || $option_id === false || $poll_id <= 0 || $option_id <= 0) {
    header("Location: error.php?error=InvalidVoteInput");
    exit;
}

// Verify that the selected option exists for this poll
$sql = "SELECT option_id FROM options WHERE poll_id = ? AND option_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Prepare failed (validate option): " . $conn->error);
    header("Location: error.php?error=DBError");
    exit;
}
$stmt->bind_param("ii", $poll_id, $option_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header("Location: error.php?error=InvalidVoteOption");
    exit;
}
$stmt->close();

// Check if the user has already voted in this poll
$sql = "SELECT vote_id FROM votes WHERE poll_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Prepare failed (check existing vote): " . $conn->error);
    header("Location: error.php?error=DBError");
    exit;
}
$stmt->bind_param("ii", $poll_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$has_voted = ($result->num_rows > 0);
$stmt->close();

// Insert a new vote or update an existing one
if ($has_voted) {
    // Update branch: parameter order is option_id, poll_id, user_id.
    $sql = "UPDATE votes SET option_id = ?, voted_at = CURRENT_TIMESTAMP WHERE poll_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed (vote update): " . $conn->error);
        header("Location: error.php?error=DBError");
        exit;
    }
    $stmt->bind_param("iii", $option_id, $poll_id, $user_id);
} else {
    // Insert branch: parameter order is poll_id, option_id, user_id.
    $sql = "INSERT INTO votes (poll_id, option_id, user_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed (vote insert): " . $conn->error);
        header("Location: error.php?error=DBError");
        exit;
    }
    $stmt->bind_param("iii", $poll_id, $option_id, $user_id);
}

if (!$stmt->execute()) {
    error_log("Vote submission failed: " . $stmt->error);
    header("Location: error.php?error=VoteFailed");
    exit;
}
$stmt->close();

// Set a session flag for successful voting and redirect
$_SESSION['vote_successful'] = true;
header("Location: vote.php?poll_id=" . $poll_id);
exit;
?>