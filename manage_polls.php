<?php
session_start();

// Ensure the user is logged in; if not, redirect to login page.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include('db_connect.php');

$user_id = $_SESSION['user_id'];

// Fetch polls created by the logged-in user
$sql = "SELECT poll_id, question FROM polls WHERE user_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Prepare failed (created polls): " . $conn->error);
    header("Location: error.php?error=DBError");
    exit;
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Polls</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Manage Your Polls</h1>
        <?php include('nav.php'); display_nav(1); ?>
    </header>
    <main>
        <h2>Your Created Polls</h2>
        <?php
        if ($result->num_rows > 0) {
            echo "<ul>";
            while ($row = $result->fetch_assoc()) {
                // Sanitize the poll question for safe output.
                $question = htmlspecialchars($row['question'], ENT_QUOTES, 'UTF-8');
                // Cast poll_id to integer to ensure safety in URL parameters.
                $poll_id = intval($row['poll_id']);
                
                echo "<li>" . $question . " 
                        <a href='vote.php?poll_id=" . $poll_id . "'>View</a> 
                        <a href='edit_poll.php?poll_id=" . $poll_id . "'>Edit</a> 
                        <a href='delete_poll.php?poll_id=" . $poll_id . "' onclick=\"return confirm('Are you sure you want to delete this poll?')\">Delete</a>
                      </li>";
            }
            echo "</ul>";
        } else {
            echo "<p>You haven't created any polls yet.</p>";
        }
        $stmt->close();

        // Fetch polls voted on by the logged-in user
        $sql = "SELECT DISTINCT polls.poll_id, polls.question  
                FROM polls  
                JOIN votes ON polls.poll_id = votes.poll_id  
                WHERE votes.user_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed (voted polls): " . $conn->error);
            header("Location: error.php?error=DBError");
            exit;
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        ?>
        
        <h2>Polls You Voted On</h2>
        <?php
        if ($result->num_rows > 0) {
            echo "<ul>";
            while ($row = $result->fetch_assoc()) {
                $question = htmlspecialchars($row['question'], ENT_QUOTES, 'UTF-8');
                $poll_id = intval($row['poll_id']);
                
                echo "<li>" . $question . " 
                        <a href='vote.php?poll_id=" . $poll_id . "'>View</a> 
                        <a href='view_results.php?poll_id=" . $poll_id . "'>View Results</a>
                      </li>";
            }
            echo "</ul>";
        } else {
            echo "<p>You haven't voted on any polls yet.</p>";
        }
        $stmt->close();
        ?>
    </main>
    <?php include('footer.php'); ?>
</body>
</html>