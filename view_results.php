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
        include('db_connect.php');
        
        // Get poll ID from URL
        $poll_id = intval($_GET['poll_id']);
        
        // Fetch poll question and creator
        $sql = "SELECT polls.question, users.nickname AS creator
                FROM polls
                JOIN users ON polls.user_id = users.user_id
                WHERE polls.poll_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $poll_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $poll = $result->fetch_assoc();
            echo "<h2>" . $poll['question'] . "</h2>";
            echo "<p><em>Created by " . $poll['creator'] . "</em></p>";

            // Fetch poll options and vote counts
            $sql = "SELECT options.option_text, COUNT(votes.option_id) as vote_count
                    FROM options
                    LEFT JOIN votes ON options.option_id = votes.option_id
                    WHERE options.poll_id = ?
                    GROUP BY options.option_id";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $poll_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                echo "<ul>";
                while ($row = $result->fetch_assoc()) {
                    echo "<li>" . $row['option_text'] . ": " . $row['vote_count'] . " votes</li>";
                }
                echo "</ul>";
            } else {
                echo "No options available for this poll.";
            }

            // Fetch comments
            $sql = "SELECT comments.comment_text, users.nickname, comments.created_at
                    FROM comments
                    JOIN users ON comments.user_id = users.user_id
                    WHERE comments.poll_id = ?
                    ORDER BY comments.created_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $poll_id);
            $stmt->execute();
            $result = $stmt->get_result();

            $total_comments = $result->num_rows;

            echo "<h3>Comments ($total_comments)</h3>";
            if ($total_comments > 0) {
                while ($comment = $result->fetch_assoc()) {
                    echo "<p><strong>" . $comment['nickname'] . "</strong> (" . $comment['created_at'] . "): " . $comment['comment_text'] . "</p>";
                }
            } else {
                echo "<p>No comments yet. Be the first to comment!</p>";
            }
        } else {
            echo "Poll not found.";
        }
        ?>
    </main>
    <?php include('footer.php'); ?>
</body>
</html>




