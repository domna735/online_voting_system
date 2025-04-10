<?php
session_start();
include('db_connect.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Generate a CSRF token if it doesn't exist
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch user data
$sql = "SELECT login_id, nickname, email, profile_pic FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Prepare failed (fetch user profile): " . $conn->error);
    header("Location: error.php?error=DBError");
    exit;
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    error_log("User profile not found for ID: " . $user_id);
    header("Location: error.php?error=UserNotFound");
    exit;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Manage Profile</h1>
        <?php include('nav.php'); display_nav(1); ?>
    </header>
    <main>
        <form action="manage_profile_process.php" method="POST" enctype="multipart/form-data">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <label for="login_id">Login ID:</label><br>
            <input type="text" id="login_id" name="login_id" value="<?php echo htmlspecialchars($user['login_id'], ENT_QUOTES, 'UTF-8'); ?>" required><br><br>
            
            <label for="nickname">Nickname:</label><br>
            <input type="text" id="nickname" name="nickname" value="<?php echo htmlspecialchars($user['nickname'], ENT_QUOTES, 'UTF-8'); ?>" required><br><br>
            
            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?>" required><br><br>
            
            <label for="profile_picture">Profile Picture:</label><br>
            <?php if (!empty($user['profile_pic'])): ?>
                <img src="<?php echo htmlspecialchars($user['profile_pic'], ENT_QUOTES, 'UTF-8'); ?>" alt="Profile Picture" width="100"><br>
            <?php endif; ?>
            <input type="file" id="profile_picture" name="profile_picture"><br><br>
            
            <label for="password">New Password (leave blank to keep current password):</label><br>
            <input type="password" id="password" name="password"><br><br>
            
            <label for="confirm_password">Confirm Password:</label><br>
            <input type="password" id="confirm_password" name="confirm_password"><br><br>
            
            <button type="submit">Update Profile</button>
        </form>
    </main>
    <?php include('footer.php'); ?>
</body>
</html>