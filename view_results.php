<?php
session_start();
include('db_connect.php');

// Validate that a poll_id is provided, and cast it to an integer.
if (!isset($_GET['poll_id'])) {
    echo "Poll not found.";
    exit;
}
$poll_id = intval($_GET['poll_id']);
if ($poll_id <= 0) {
    echo "Invalid poll ID.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Poll Results</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Poll Results</h1>
        <?php include('nav.php'); display_nav(1); ?>
    </header>
    <main>
        <?php
        // Fetch poll question, creator, and profile picture.
        $sql = "SELECT polls.question, users.nickname AS creator, users.profile_pic
                FROM polls
                JOIN users ON polls.user_id = users.user_id
                WHERE polls.poll_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed (poll details): " . $conn->error);
            echo "An error occurred. Please try again later.";
            exit;
        }
        $stmt->bind_param("i", $poll_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $poll = $result->fetch_assoc();
            echo "<h2>" . htmlspecialchars($poll['question'], ENT_QUOTES, 'UTF-8') . "</h2>";
            echo "<p><em>Created by " . htmlspecialchars($poll['creator'], ENT_QUOTES, 'UTF-8') . "</em></p>";
            
            // Show profile picture if available.
            if (!empty($poll['profile_pic'])) {
                echo '<img src="' . htmlspecialchars($poll['profile_pic'], ENT_QUOTES, 'UTF-8') . '" alt="Profile Picture" width="100"><br>';
            }
            
            $stmt->close();
            
            // Fetch poll options with their vote counts.
            $sql = "SELECT options.option_text, COUNT(votes.option_id) AS vote_count
                    FROM options
                    LEFT JOIN votes ON options.option_id = votes.option_id
                    WHERE options.poll_id = ?
                    GROUP BY options.option_id";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                error_log("Prepare failed (options): " . $conn->error);
                echo "An error occurred. Please try again later.";
                exit;
            }
            $stmt->bind_param("i", $poll_id);
            $stmt->execute();
            $result_options = $stmt->get_result();
            
            if ($result_options->num_rows > 0) {
                echo "<ul>";
                while ($row = $result_options->fetch_assoc()) {
                    echo "<li>" . htmlspecialchars($row['option_text'], ENT_QUOTES, 'UTF-8') . ": " . intval($row['vote_count']) . " votes</li>";
                }
                echo "</ul>";
            } else {
                echo "No options available for this poll.";
            }
            $stmt->close();
            
            // Fetch comments for the poll.
            $sql = "SELECT comments.comment_text, users.nickname, comments.created_at
                    FROM comments
                    JOIN users ON comments.user_id = users.user_id
                    WHERE comments.poll_id = ?
                    ORDER BY comments.created_at DESC";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                error_log("Prepare failed (comments): " . $conn->error);
                echo "An error occurred. Please try again later.";
                exit;
            }
            $stmt->bind_param("i", $poll_id);
            $stmt->execute();
            $result_comments = $stmt->get_result();
            $total_comments = $result_comments->num_rows;
            
            echo "<h3>Comments (" . $total_comments . ")</h3>";
            if ($total_comments > 0) {
                while ($comment = $result_comments->fetch_assoc()) {
                    echo "<p><strong>" . htmlspecialchars($comment['nickname'], ENT_QUOTES, 'UTF-8') . "</strong> (" .
                         htmlspecialchars($comment['created_at'], ENT_QUOTES, 'UTF-8') . "): " .
                         htmlspecialchars($comment['comment_text'], ENT_QUOTES, 'UTF-8') . "</p>";
                }
            } else {
                echo "<p>No comments yet. Be the first to comment!</p>";
            }
            $stmt->close();
            
            // Show the comment form if the user is logged in.
            if (isset($_SESSION['user_id'])) {
                // Generate a CSRF token for the comment form if one isn't already set.
                if (!isset($_SESSION['csrf_token'])) {
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                }
                echo '<h3>Leave a Comment</h3>';
                echo '<form action="comment_process.php" method="POST">';
                echo '    <input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
                echo '    <input type="hidden" name="poll_id" value="' . $poll_id . '">';
                echo '    <textarea name="comment_text" required></textarea><br>';
                echo '    <button type="submit">Submit Comment</button>';
                echo '</form>';
            } else {
                echo "<p><a href='login.php'>Log in</a> to leave a comment.</p>";
            }
            
        } else {
            echo "Poll not found.";
        }
        ?>
    </main>
    <?php include('footer.php'); ?>
</body>
</html>