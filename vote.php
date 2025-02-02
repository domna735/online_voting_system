<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vote</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Add styling for the popup */
        .popup {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .popup-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            margin: 10px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
        }
        .button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <header>
        <h1>Vote in Poll</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="polls.php">Polls</a></li>
                <?php
                session_start();
                if (isset($_SESSION['user_id'])) {
                    echo '<li><a href="create_poll.php">Create Poll</a></li>';
                    echo '<li><a href="logout.php">Logout</a></li>';
                } else {
                    echo '<li><a href="login.php">Login</a></li>';
                    echo '<li><a href="register.php">Register</a></li>';
                }
                ?>
            </ul>
        </nav>
    </header>
    <main>
        <?php
        include('db_connect.php');
        
        // Get poll ID from URL
        $poll_id = intval($_GET['poll_id']);
        
        // Fetch poll question, content, and creator
        $sql = "SELECT polls.question, polls.content, users.nickname AS creator
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
            echo "<p><em>Content: " . $poll['content'] . "</em></p>";
            
            // Fetch poll options
            $sql = "SELECT option_id, option_text FROM options WHERE poll_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $poll_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                echo '<form id="voteForm" onsubmit="return checkLoginStatus()" method="POST">';
                echo '<input type="hidden" name="poll_id" value="' . $poll_id . '">';
                
                while ($option = $result->fetch_assoc()) {
                    echo '<label><input type="radio" name="option_id" value="' . $option['option_id'] . '" required> ' . $option['option_text'] . '</label><br>';
                }
                
                echo '<button type="submit">Vote</button>';
                echo '</form>';
            } else {
                echo "No options available for this poll.";
            }

            // Fetch vote details
            $sql = "SELECT votes.option_id, votes.user_id, votes.voted_at, users.nickname
                    FROM votes
                    JOIN users ON votes.user_id = users.user_id
                    WHERE votes.poll_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $poll_id);
            $stmt->execute();
            $result = $stmt->get_result();

            $total_votes = $result->num_rows;

            echo "<h3>Total Votes: $total_votes</h3>";
            if ($total_votes > 0) {
                echo "<ul>";
                while ($vote = $result->fetch_assoc()) {
                    echo "<li><strong>" . $vote['nickname'] . "</strong> voted on " . $vote['voted_at'] . "</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>No votes yet.</p>";
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

            // Comment form
            if (isset($_SESSION['user_id'])) {
                echo '<h3>Leave a Comment</h3>';
                echo '<form action="comment_process.php" method="POST">';
                echo '<input type="hidden" name="poll_id" value="' . $poll_id . '">';
                echo '<textarea name="comment_text" required></textarea><br>';
                echo '<button type="submit">Submit Comment</button>';
                echo '</form>';
            } else {
                echo "<p><a href='login.php'>Log in</a> to leave a comment.</p>";
            }

        } else {
            echo "Poll not found.";
        }
        ?>
    </main>

    <!-- Popup Modal -->
    <div id="loginPopup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closePopup()">&times;</span>
            <div class="message">You must be logged in to vote.</div>
            <div class="message">Do you have an account?</div>
            <button class="button" onclick="location.href='login.php'">Log In</button>
            <div class="message">If not, you can register here:</div>
            <button class="button" onclick="location.href='register.php'">Register</button>
        </div>
    </div>

    <script>
        function checkLoginStatus() {
            <?php if (!isset($_SESSION['user_id'])): ?>
                document.getElementById('loginPopup').style.display = 'block';
                return false;
            <?php endif; ?>
            return true;
        }

        function closePopup() {
            document.getElementById('loginPopup').style.display = 'none';
        }
    </script>
</body>
</html>

