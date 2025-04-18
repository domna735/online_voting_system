<?php
session_start();

// Include the database connection and central sanitization functions
include('db_connect.php');
include('sanitization.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: error.php?error=NotLoggedIn");
    exit;
}

// Verify the CSRF token matches
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header("Location: error.php?error=InvalidCSRFToken");
    exit;
}

$user_id = $_SESSION['user_id'];

// Retrieve raw input using filter_input (FILTER_DEFAULT is used so the content remains intact)
$raw_question = filter_input(INPUT_POST, 'question', FILTER_DEFAULT);
$raw_content  = filter_input(INPUT_POST, 'content', FILTER_DEFAULT);

// Use the sanitization function and enforce maximum length for poll question (250) and content (500)
$question = substr(sanitize_input($raw_question), 0, 250);
$content  = substr(sanitize_input($raw_content), 0, 500);

// Retrieve poll options as an array
$options = $_POST['options'];

// Validate poll options: require at least 2 options and no more than 10
if (!is_array($options) || count($options) < 2 || count($options) > 10) {
    header("Location: error.php?error=InvalidNumberOfOptions");
    exit;
}

// Sanitize each poll option with a maximum length of 100 characters
foreach ($options as $key => $option) {
    $options[$key] = substr(sanitize_input($option), 0, 100);
}

// Insert the poll question and content into the polls table
$sql = "INSERT INTO polls (user_id, question, content) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Prepare statement failed: " . $conn->error);
    header("Location: error.php?error=PollCreationFailed");
    exit;
}
$stmt->bind_param("iss", $user_id, $question, $content);
if ($stmt->execute()) {
    $poll_id = $stmt->insert_id;
    $stmt->close();
    
    // Insert each poll option into the options table
    $sql_option = "INSERT INTO options (poll_id, option_text) VALUES (?, ?)";
    $stmt_option = $conn->prepare($sql_option);
    if (!$stmt_option) {
        error_log("Prepare statement for options failed: " . $conn->error);
        header("Location: error.php?error=PollOptionCreationFailed");
        exit;
    }
    foreach ($options as $option) {
        $stmt_option->bind_param("is", $poll_id, $option);
        if (!$stmt_option->execute()) {
            error_log("Error inserting option: " . $stmt_option->error);
            header("Location: error.php?error=PollOptionCreationFailed");
            exit;
        }
    }
    $stmt_option->close();
    
    // Optionally, regenerate the session ID to mitigate session fixation
    // session_regenerate_id(true);
    
    header('Location: polls.php');
    exit;
} else {
    error_log("Database error in poll creation: " . $stmt->error);
    header("Location: error.php?error=PollCreationFailed");
    exit;
}
?>