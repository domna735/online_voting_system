<?php
session_start();
include('db_connect.php');

// Redirect non‑authenticated users
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// CSRF token validation
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header("Location: error.php?error=InvalidCSRFToken");
    exit;
}

// Helper function to sanitize input and enforce a maximum length
function test_input($data, $maxLength = 1000) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return substr($data, 0, $maxLength);
}

// Retrieve and sanitize form data
$poll_id = intval($_POST['poll_id']);
$question = test_input($_POST['question'], 250);
$content  = test_input($_POST['content'], 500);
$options  = $_POST['options'];  // This is assumed to be an associative array: option_id => option_text
$user_id  = $_SESSION['user_id'];

// Check if the poll belongs to the logged-in user
$sql = "SELECT * FROM polls WHERE poll_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Prepare failed (check poll ownership): " . $conn->error);
    header("Location: error.php?error=DBError");
    exit;
}
$stmt->bind_param("ii", $poll_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header("Location: error.php?error=NoPermission");
    exit;
}
$stmt->close();

// Update the poll question and content
$sql = "UPDATE polls SET question = ?, content = ? WHERE poll_id = ?";
$stmt_update = $conn->prepare($sql);
if (!$stmt_update) {
    error_log("Prepare failed (update poll): " . $conn->error);
    header("Location: error.php?error=DBError");
    exit;
}
$stmt_update->bind_param("ssi", $question, $content, $poll_id);
if (!$stmt_update->execute()) {
    error_log("Poll update failed: " . $stmt_update->error);
    header("Location: error.php?error=PollUpdateFailed");
    exit;
}
$stmt_update->close();

// Update each poll option
foreach ($options as $option_id => $option_text) {
    $sanitized_text = test_input($option_text, 100);
    $sql = "UPDATE options SET option_text = ? WHERE option_id = ? AND poll_id = ?";
    $stmt_option = $conn->prepare($sql);
    if (!$stmt_option) {
        error_log("Prepare failed (update option): " . $conn->error);
        header("Location: error.php?error=DBError");
        exit;
    }
    $stmt_option->bind_param("sii", $sanitized_text, $option_id, $poll_id);
    if (!$stmt_option->execute()) {
        error_log("Option update failed: " . $stmt_option->error);
        header("Location: error.php?error=OptionUpdateFailed");
        exit;
    }
    $stmt_option->close();
}

// Redirect the user back to the manage polls page after success
header("Location: manage_polls.php");
exit;
?>