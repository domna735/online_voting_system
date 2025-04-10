<?php
session_start();
include('db_connect.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Polls</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Polls</h1>
        <?php include('nav.php'); display_nav(1); ?>
    </header>
    <main>
        <section class="intro">
            <h2>Create, Vote, and Discover Opinions</h2>
            <p>Join our community to express your views and see what others think.</p>
            <section>
                <h2>All Polls</h2>
                <ul>
                    <?php
                    $sql = "SELECT polls.poll_id, polls.question, users.nickname AS creator
                            FROM polls
                            JOIN users ON polls.user_id = users.user_id";
                    $result = $conn->query($sql);
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Sanitize output to avoid XSS attacks
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
            // Show appropriate call-to-action based on authentication
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