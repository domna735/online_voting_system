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
        <?php
        include('db_connect.php');

        if (!isset($_SESSION['user_id'])) {
            echo "You must be logged in to manage your profile.";
            exit;
        }

        $user_id = $_SESSION['user_id'];

        // Fetch user data
        $sql = "SELECT login_id, nickname, email, profile_pic FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            ?>

            <form action="manage_profile_process.php" method="POST" enctype="multipart/form-data">
                <label for="login_id">Login ID:</label><br>
                <input type="text" id="login_id" name="login_id" value="<?php echo htmlspecialchars($user['login_id']); ?>" required><br><br>
                <label for="nickname">Nickname:</label><br>
                <input type="text" id="nickname" name="nickname" value="<?php echo htmlspecialchars($user['nickname']); ?>" required><br><br>
                <label for="email">Email:</label><br>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br><br>
                <label for="profile_picture">Profile Picture:</label><br>
                <?php if ($user['profile_pic']): ?>
                    <img src="<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Profile Picture" width="100"><br>
                <?php endif; ?>
                <input type="file" id="profile_picture" name="profile_picture"><br><br>
                <label for="password">Password (leave blank to keep current password):</label><br>
                <input type="password" id="password" name="password"><br><br>
                <label for="confirm_password">Confirm Password:</label><br>
                <input type="password" id="confirm_password" name="confirm_password"><br><br>
                <button type="submit">Update Profile</button>
            </form>

            <?php
        } else {
            echo "User not found.";
        }
        ?>
    </main>
    <?php include('footer.php'); ?>
</body>
</html>