<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Poll</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Edit Poll</h1>
        <?php include('nav.php'); display_nav(1); ?>
    </header>
    <main>
        <?php
        if (!isset($_SESSION['user_id'])) {
            echo "You must be logged in to edit a poll.";
            exit;
        }

        include('db_connect.php');

        // Get poll ID from URL
        $poll_id = intval($_GET['poll_id']);
        $user_id = $_SESSION['user_id'];

        // Fetch poll details
        $sql = "SELECT question, content FROM polls WHERE poll_id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $poll_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $poll = $result->fetch_assoc();
            ?>

            <form action="edit_poll_process.php" method="POST">
                <input type="hidden" name="poll_id" value="<?php echo $poll_id; ?>">
                <label for="question">Poll Question:</label><br>
                <textarea id="question" name="question" required><?php echo $poll['question']; ?></textarea><br><br>
                <label for="content">Content:</label><br>
                <textarea id="content" name="content" required><?php echo $poll['content']; ?></textarea><br><br>
                
                <label for="options">Options:</label><br>
                <?php
                // Fetch poll options
                $sql = "SELECT option_id, option_text FROM options WHERE poll_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $poll_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                while ($option = $result->fetch_assoc()) {
                    echo '<input type="text" name="options[' . $option['option_id'] . ']" value="' . htmlspecialchars($option['option_text']) . '"><br>';
                }
                ?>
                
                <button type="submit">Update Poll</button>
            </form>

            <?php
        } else {
            echo "Poll not found or you do not have permission to edit this poll.";
        }
        ?>
    </main>
    <?php include('footer.php'); ?>
</body>
</html>