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
        <h2>All Polls</h2>
        <ul>
            <?php
            include('db_connect.php');
            $sql = "SELECT polls.poll_id, polls.question, users.nickname AS creator
                    FROM polls
                    JOIN users ON polls.user_id = users.user_id";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<li>" . $row['question'] . " <em>by " . $row['creator'] . "</em> <a href='vote.php?poll_id=" . $row['poll_id'] . "'>Vote</a> <a href='view_results.php?poll_id=" . $row['poll_id'] . "'>View Results</a></li>";
                }
            } else {
                echo "No polls found.";
            }
            ?>
        </ul>
    </main>
    <?php include('footer.php'); ?>
</body>
</html>


