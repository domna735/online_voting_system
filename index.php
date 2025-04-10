<?php
// Start session at the very beginning
session_start();

// Include database connection
include('db_connect.php');

// If there is a valid user_id cookie, try to restore the session.
// Ensure the cookie value is properly cast to an integer.
if (isset($_COOKIE['user_id']) && !isset($_SESSION['user_id'])) {
    $cookie_user_id = intval($_COOKIE['user_id']);
    if ($cookie_user_id > 0) {
        // Use prepared statement to retrieve user details
        $sql = "SELECT user_id, nickname FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $cookie_user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['nickname'] = $user['nickname'];
            }
            $stmt->close();
        } else {
            error_log("User retrieval prepare failed: " . $conn->error);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Online Voting System</h1>
        <?php include('nav.php'); display_nav(1); ?>
    </header>
    <main>
        <h2>Welcome to the Online Voting System!</h2>
        <?php
        // If the user is logged in, display a welcome message
        if (isset($_SESSION['user_id']) && isset($_SESSION['nickname'])) {
            echo "<p>Welcome, " . htmlspecialchars($_SESSION['nickname'], ENT_QUOTES, 'UTF-8') . "! You have successfully logged in.</p>";
        }
        ?>
        <section class="intro">
            <h2>Create, Vote, and Discover Opinions</h2>
            <p>Join our community to express your views and see what others think.</p>
            
            <section>
                <h2>All Polls</h2>
                <ul>
                    <?php
                    // Retrieve all polls; since there is no user input, this is safe.
                    $sql = "SELECT polls.poll_id, polls.question, users.nickname AS creator 
                            FROM polls 
                            JOIN users ON polls.user_id = users.user_id";
                    $result = $conn->query($sql);
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Sanitize dynamic content to prevent XSS
                            $question = htmlspecialchars($row['question'], ENT_QUOTES, 'UTF-8');
                            $creator  = htmlspecialchars($row['creator'], ENT_QUOTES, 'UTF-8');
                            $poll_id  = intval($row['poll_id']);
                            
                            echo "<li>" . $question . " <em>by " . $creator . "</em> " .
                                 "<a href='vote.php?poll_id=" . $poll_id . "'>Vote</a> " .
                                 "<a href='view_results.php?poll_id=" . $poll_id . "'>View Results</a></li>";
                        }
                    } else {
                        echo "<li>No polls found.</li>";
                    }
                    ?>
                </ul>
            </section>
            <br>
            <?php
            // If the user is logged in, show the "Create Poll" button; otherwise, prompt them to log in.
            if (isset($_SESSION['user_id'])) {
                echo '<a href="create_poll.php" class="btn">Create Poll</a>';
            } else {
                echo '<a href="login.php" class="btn">Get Started</a>';
            }
            ?>
        </section>       
    </main>
    <?php include('footer.php'); ?>
</body>
</html>

