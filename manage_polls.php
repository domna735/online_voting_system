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
        <?php
        if (!isset($_SESSION['user_id'])) {
            echo "You must be logged in to manage your polls.";
            exit;
        }

        include('db_connect.php');

        // Fetch polls created by the logged-in user
        $user_id = $_SESSION['user_id'];
        $sql = "SELECT poll_id, question FROM polls WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        echo "<h2>Your Created Polls</h2>";
        if ($result->num_rows > 0) {
            echo "<ul>";
            while ($row = $result->fetch_assoc()) {
                echo "<li>" . $row['question'] . " 
                    <a href='vote.php?poll_id=" . $row['poll_id'] . "'>View</a> 
                    <a href='edit_poll.php?poll_id=" . $row['poll_id'] . "'>Edit</a> 
                    <a href='delete_poll.php?poll_id=" . $row['poll_id'] . "' onclick=\"return confirm('Are you sure you want to delete this poll?')\">Delete</a>
                </li>";
            }
            echo "</ul>";
        } else {
            echo "You haven't created any polls yet.";
        }

        // Fetch polls voted by the logged-in user
        $sql = "SELECT DISTINCT polls.poll_id, polls.question 
                FROM polls 
                JOIN votes ON polls.poll_id = votes.poll_id 
                WHERE votes.user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        echo "<h2>Polls You Voted On</h2>";
        if ($result->num_rows > 0) {
            echo "<ul>";
            while ($row = $result->fetch_assoc()) {
                echo "<li>" . $row['question'] . " 
                    <a href='vote.php?poll_id=" . $row['poll_id'] . "'>View</a> 
                    <a href='view_results.php?poll_id=" . $row['poll_id'] . "'>View Results</a>
                </li>";
            }
            echo "</ul>";
        } else {
            echo "You haven't voted on any polls yet.";
        }
        ?>
    </main>
    <?php include('footer.php'); ?>
</body>
</html>
