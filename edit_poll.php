<?php
session_start();

// Redirect non‑authenticated users to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include('db_connect.php');

// Get poll ID from URL and ensure it is an integer
$poll_id = intval($_GET['poll_id']);
$user_id = $_SESSION['user_id'];

// Generate a CSRF token if not already set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Prepare and execute the statement for fetching poll details
$sql = "SELECT question, content FROM polls WHERE poll_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Prepare failed (fetch poll): " . $conn->error);
    header("Location: error.php?error=DBError");
    exit;
}
$stmt->bind_param("ii", $poll_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
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
        if ($result->num_rows > 0) {
            $poll = $result->fetch_assoc();
            ?>
            <form action="edit_poll_process.php" method="POST">
                <!-- CSRF token field -->
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="poll_id" value="<?php echo $poll_id; ?>">
                
                <label for="question">Poll Question:</label><br>
                <textarea id="question" name="question" required><?php echo htmlspecialchars($poll['question'], ENT_QUOTES, 'UTF-8'); ?></textarea><br><br>
                
                <label for="content">Content:</label><br>
                <textarea id="content" name="content" required><?php echo htmlspecialchars($poll['content'], ENT_QUOTES, 'UTF-8'); ?></textarea><br><br>
                
                <label for="options">Options:</label><br>
                <?php
                // Fetch poll options
                $sql = "SELECT option_id, option_text FROM options WHERE poll_id = ?";
                $stmt2 = $conn->prepare($sql);
                if (!$stmt2) {
                    error_log("Prepare failed (fetch options): " . $conn->error);
                    header("Location: error.php?error=DBError");
                    exit;
                }
                $stmt2->bind_param("i", $poll_id);
                $stmt2->execute();
                $result2 = $stmt2->get_result();
                
                while ($option = $result2->fetch_assoc()) {
                    // Cast option_id to integer and sanitize the option text for safe output.
                    echo '<input type="text" name="options[' . intval($option['option_id']) . ']" value="' . htmlspecialchars($option['option_text'], ENT_QUOTES, 'UTF-8') . '"><br>';
                }
                $stmt2->close();
                ?>
                <button type="submit">Update Poll</button>
            </form>
            <?php
        } else {
            echo "<p>Poll not found or you do not have permission to edit this poll.</p>";
            exit;
        }
        ?>
    </main>
    <?php include('footer.php'); ?>
</body>
</html>