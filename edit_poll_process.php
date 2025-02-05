<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to edit a poll.";
    exit;
}

// Get form data
$poll_id = intval($_POST['poll_id']);
$question = htmlspecialchars($_POST['question']);
$content = htmlspecialchars($_POST['content']);
$options = $_POST['options']; // Array of options
$user_id = $_SESSION['user_id'];

// Check if the poll belongs to the logged-in user
$sql = "SELECT * FROM polls WHERE poll_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $poll_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update the poll
    $sql = "UPDATE polls SET question = ?, content = ? WHERE poll_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $question, $content, $poll_id);
    $stmt->execute();

    // Update the options
    foreach ($options as $option_id => $option_text) {
        $option_text = htmlspecialchars($option_text);
        $sql = "UPDATE options SET option_text = ? WHERE option_id = ? AND poll_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $option_text, $option_id, $poll_id);
        $stmt->execute();
    }

    echo "Poll updated successfully.";
    header("Location: manage_polls.php");
    exit;
} else {
    echo "You do not have permission to edit this poll.";
}
?>