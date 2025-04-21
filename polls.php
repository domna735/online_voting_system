<?php
ini_set('session.cookie_secure', '1'); // Enable Secure flag
ini_set('session.cookie_httponly', '1'); // Enable HttpOnly flag
ini_set('session.cookie_samesite', 'Strict'); // Set SameSite attribute
session_start();
include('db_connect.php');

// Function to validate and sanitize input
function validate_input($data, $type = 'string', $max_length = 255) {
    $data = trim($data);
    $data = stripslashes($data);
    
    switch($type) {
        case 'int':
            return intval($data);
        case 'string':
            return substr(htmlspecialchars($data, ENT_QUOTES, 'UTF-8'), 0, $max_length);
        default:
            return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
}

// Validate and sanitize any GET parameters
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$sort = isset($_GET['sort']) ? htmlspecialchars($_GET['sort'], ENT_QUOTES, 'UTF-8') : 'created_at';
$order = isset($_GET['order']) ? htmlspecialchars($_GET['order'], ENT_QUOTES, 'UTF-8') : 'DESC';

// Validate sort and order values against whitelist
$allowed_sorts = ['created_at', 'question', 'creator'];
$allowed_orders = ['ASC', 'DESC'];

if (!in_array($sort, $allowed_sorts)) {
    $sort = 'created_at';
}
if (!in_array($order, $allowed_orders)) {
    $order = 'DESC';
}

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
                    // Use prepared statement with parameterized query
                    $sql = "SELECT polls.poll_id, polls.question, users.nickname AS creator, polls.created_at
                            FROM polls
                            JOIN users ON polls.user_id = users.user_id
                            ORDER BY " . $sort . " " . $order;
                    
                    $stmt = $conn->prepare($sql);
                    if (!$stmt) {
                        error_log("Prepare failed (fetch polls): " . $conn->error);
                        echo "<li>An error occurred. Please try again later.</li>";
                    } else {
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                // Sanitize output to avoid XSS attacks
                                $question = htmlspecialchars($row['question'], ENT_QUOTES, 'UTF-8');
                                $creator  = htmlspecialchars($row['creator'], ENT_QUOTES, 'UTF-8');
                                $poll_id  = intval($row['poll_id']);
                                $created_at = htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8');
                                
                                echo "<li>" . $question . " <em>by " . $creator . "</em> " . 
                                     "<span class='date'>(" . $created_at . ")</span> " .
                                     "<a href='vote.php?poll_id=" . $poll_id . "'>Vote</a> " . 
                                     "<a href='view_results.php?poll_id=" . $poll_id . "'>View Results</a></li>";
                            }
                        } else {
                            echo "<li>No polls found.</li>";
                        }
                        $stmt->close();
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