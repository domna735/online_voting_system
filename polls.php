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
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="create_poll.php">Create Poll</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2>All Polls</h2>
        <ul>
            <?php
            include('db_connect.php');
            $sql = "SELECT * FROM polls";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<li>" . $row['question'] . " <a href='vote.php?poll_id=" . $row['poll_id'] . "'>Vote</a> <a href='view_results.php?poll_id=" . $row['poll_id'] . "'>View Results</a></li>";
                }
            } else {
                echo "No polls found.";
            }
            ?>
        </ul>
    </main>
</body>
</html>
