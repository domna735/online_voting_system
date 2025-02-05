<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['user_id'])) {
    echo "<div class='message'>You must be logged in to vote.</div>";
    echo "<div class='message'>Do you have an account?</div>";
    echo "<button class='button' onclick=\"location.href='login.php'\">Log In</button>";
    echo "<div class='message'>If not, you can register here:</div>";
    echo "<button class='button' onclick=\"location.href='register.php'\">Register</button>";
    exit;
}

// Get form data
$poll_id = intval($_POST['poll_id']);
$option_id = intval($_POST['option_id']);
$user_id = $_SESSION['user_id'];

// Check if the user has already voted in this poll
$sql = "SELECT * FROM votes WHERE poll_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $poll_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // User has already voted, update their vote
    $sql = "UPDATE votes SET option_id = ?, voted_at = CURRENT_TIMESTAMP WHERE poll_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $option_id, $poll_id, $user_id);
} else {
    // User has not voted, insert a new vote
    $sql = "INSERT INTO votes (poll_id, option_id, user_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $poll_id, $option_id, $user_id);
}

if ($stmt->execute()) {
    // Set session variable for successful vote to trigger popup in vote.php
    $_SESSION['vote_successful'] = true;
    header("Location: vote.php?poll_id=" . $poll_id);
    exit;
} else {
    echo "Error: " . $stmt->error;
}
?>