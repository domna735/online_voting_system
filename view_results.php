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
        } else {
            echo "Poll not found.";
        }
        ?>
    </main>
</body>
</html>
