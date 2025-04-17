<?php
session_start();
include('db_connect.php');

// Validate poll_id from URL
if (!isset($_GET['poll_id'])) {
    header("Location: error.php?error=InvalidPollId");
    exit;
}
$poll_id = intval($_GET['poll_id']);
if ($poll_id <= 0) {
    header("Location: error.php?error=InvalidPollId");
    exit;
}

// Generate a CSRF token if it doesn't exist
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch poll details: question, content, and creator's nickname
$sql = "SELECT polls.question, polls.content, users.nickname AS creator 
        FROM polls
        JOIN users ON polls.user_id = users.user_id
        WHERE polls.poll_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Prepare failed (fetch poll): " . $conn->error);
    header("Location: error.php?error=DBError");
    exit;
}
$stmt->bind_param("i", $poll_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    echo "Poll not found.";
    exit;
}
$poll = $result->fetch_assoc();
$stmt->close();

// Optionally, fetch the profile picture of the poll creator
$sql = "SELECT profile_pic FROM users WHERE nickname = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("s", $poll['creator']);
    $stmt->execute();
    $profile_result = $stmt->get_result();
    if ($profile_result->num_rows > 0) {
        $profile = $profile_result->fetch_assoc();
        $profile_pic = $profile['profile_pic'];
    }
    $stmt->close();
}

// Fetch poll options
$sql = "SELECT option_id, option_text FROM options WHERE poll_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Prepare failed (fetch options): " . $conn->error);
    header("Location: error.php?error=DBError");
    exit;
}
$stmt->bind_param("i", $poll_id);
$stmt->execute();
$result = $stmt->get_result();
$options = [];
while ($row = $result->fetch_assoc()) {
    $options[] = $row;
}
$stmt->close();

// Fetch votes for display
$sql = "SELECT votes.option_id, votes.user_id, votes.voted_at, users.nickname 
        FROM votes
        JOIN users ON votes.user_id = users.user_id
        WHERE votes.poll_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $poll_id);
    $stmt->execute();
    $votesResult = $stmt->get_result();
    $total_votes = $votesResult->num_rows;
    $votes = [];
    while ($vote = $votesResult->fetch_assoc()) {
         $votes[] = $vote;
    }
    $stmt->close();
} else {
    $total_votes = 0;
}

// Fetch comments for the poll
$sql = "SELECT comments.comment_text, users.nickname, comments.created_at
        FROM comments
        JOIN users ON comments.user_id = users.user_id
        WHERE comments.poll_id = ?
        ORDER BY comments.created_at DESC";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $poll_id);
    $stmt->execute();
    $commentsResult = $stmt->get_result();
    $total_comments = $commentsResult->num_rows;
    $comments = [];
    while ($comment = $commentsResult->fetch_assoc()) {
         $comments[] = $comment;
    }
    $stmt->close();
} else {
    $total_comments = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vote on Poll</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Vote in Poll</h1>
        <?php include('nav.php'); display_nav(1); ?>
    </header>
    <main>
        <h2><?php echo htmlspecialchars($poll['question'], ENT_QUOTES, 'UTF-8'); ?></h2>
        <p><em>Created by <?php echo htmlspecialchars($poll['creator'], ENT_QUOTES, 'UTF-8'); ?></em></p>
        <?php
           if (isset($profile_pic) && !empty($profile_pic)) {
               echo '<img src="' . htmlspecialchars($profile_pic, ENT_QUOTES, 'UTF-8') . '" alt="Profile Picture" width="100"><br>';
           }
        ?>
        <p><em>Content: <?php echo htmlspecialchars($poll['content'], ENT_QUOTES, 'UTF-8'); ?></em></p>
        <?php if (count($options) > 0): ?>
            <form id="voteForm" action="vote_process.php" onsubmit="return checkLoginStatus()" method="POST">
                <!-- Include CSRF token and poll_id -->
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="poll_id" value="<?php echo $poll_id; ?>">
                <?php foreach ($options as $option): ?>
                    <label>
                        <input type="radio" name="option_id" value="<?php echo intval($option['option_id']); ?>" required>
                        <?php echo htmlspecialchars($option['option_text'], ENT_QUOTES, 'UTF-8'); ?>
                    </label><br>
                <?php endforeach; ?>
                <button type="submit">Vote</button>
            </form>
        <?php else: ?>
            <p>No options available for this poll.</p>
        <?php endif; ?>
        
        <h3>Total Votes: <?php echo $total_votes; ?></h3>
        <?php if ($total_votes > 0): ?>
            <ul>
                <?php foreach ($votes as $vote): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($vote['nickname'], ENT_QUOTES, 'UTF-8'); ?></strong> voted on <?php echo htmlspecialchars($vote['voted_at'], ENT_QUOTES, 'UTF-8'); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No votes yet.</p>
        <?php endif; ?>
        
        <h3>Comments (<?php echo $total_comments; ?>)</h3>
        <?php if ($total_comments > 0): ?>
            <?php foreach ($comments as $comment): ?>
                <p>
                    <strong><?php echo htmlspecialchars($comment['nickname'], ENT_QUOTES, 'UTF-8'); ?></strong> (<?php echo htmlspecialchars($comment['created_at'], ENT_QUOTES, 'UTF-8'); ?>):
                    <?php echo htmlspecialchars($comment['comment_text'], ENT_QUOTES, 'UTF-8'); ?>
                </p>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No comments yet. Be the first to comment!</p>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['user_id'])): ?>
    <h3>Leave a Comment</h3>
    <form action="comment_process.php" method="POST">
        <input type="hidden" name="poll_id" value="<?php echo $poll_id; ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <textarea name="comment_text" required></textarea><br>
        <button type="submit">Submit Comment</button>
    </form>
<?php else: ?>
    <p><a href="login.php">Log in</a> to leave a comment.</p>
<?php endif; ?>
    </main>
    <?php include('footer.php'); ?>
    
    <!-- Popup Modals -->
    <div id="loginPopup" class="popup" style="display: none;">
        <div class="popup-content">
            <span class="close" onclick="closePopup()">&times;</span>
            <div class="message">You must be logged in to vote.</div>
            <div class="message">Do you have an account?</div>
            <button class="button" onclick="location.href='login.php'">Log In</button>
            <div class="message">If not, you can register here:</div>
            <button class="button" onclick="location.href='register.php'">Register</button>
        </div>
    </div>
    
    <div id="successPopup" class="popup" style="display: none;">
        <div class="popup-content">
            <span class="close" onclick="closePopup()">&times;</span>
            <div class="message">Vote recorded successfully!</div>
            <button class="button" onclick="location.href='view_results.php?poll_id=<?php echo $poll_id; ?>'">View Results</button>
        </div>
    </div>
    
    <script>
        function closePopup() {
            document.getElementById('loginPopup').style.display = 'none';
            document.getElementById('successPopup').style.display = 'none';
        }
        
        // Optional check for login status before form submission.
        function checkLoginStatus() {
            <?php if (!isset($_SESSION['user_id'])): ?>
                document.getElementById('loginPopup').style.display = 'block';
                return false;
            <?php else: ?>
                return true;
            <?php endif; ?>
        }
        
        // Show success popup if vote was successful
        <?php if (isset($_SESSION['vote_successful']) && $_SESSION['vote_successful']): ?>
            document.addEventListener("DOMContentLoaded", function() {
                document.getElementById('successPopup').style.display = 'block';
            });
            <?php unset($_SESSION['vote_successful']); ?>
        <?php endif; ?>
    </script>
</body>
</html>