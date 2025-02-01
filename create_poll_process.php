<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to create a poll.";
    exit;
}

// Function to sanitize user input
function test_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Get and sanitize form data
$user_id = $_SESSION['user_id'];
$question = test_input($_POST['question']);
$options = explode("\n", test_input($_POST['options']));

// Insert poll question into polls table
$sql = "INSERT INTO polls (user_id, question) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $question);

if ($stmt->execute()) {
    $poll_id = $stmt->insert_id;

    // Insert options into options table
    $sql = "INSERT INTO options (poll_id, option_text) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);

    foreach ($options as $option) {
        $stmt->bind_param("is", $poll_id, $option);
        $stmt->execute();
    }

    echo "Poll created successfully!";
    header('Location: polls.php');
    exit;
} else {
    echo "Error: " . $stmt->error;
}
?>
