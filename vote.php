<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vote</title>
    <link rel="stylesheet" href="styles.css">
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
        
        // Fetch poll question
        $sql = "SELECT question FROM polls WHERE poll_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $poll_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $poll = $result->fetch_assoc();
            echo "<h2>" . $poll['question'] . "</h2>";
            
            // Fetch poll options
            $sql = "SELECT option_id, option_text FROM options WHERE poll_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $poll_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                echo '<form action="vote_process.php" method="POST">';
                echo '<input type="hidden" name="poll_id" value="' . $poll_id . '">';
                
                while ($option = $result->fetch_assoc()) {
                    echo '<label><input type="radio" name="option_id" value="' . $option['option_id'] . '" required> ' . $option['option_text'] . '</label><br>';
                }
                
                echo '<button type="submit">Vote</button>';
                echo '</form>';
            } else {
                echo "No options available for this poll.";
            }
        } else {
            echo "Poll not found.";
        }
        ?>
    </main>
</body>
</html>
