<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Reset Password</h1>
        <?php include('nav.php'); display_nav(2); ?>
    </header>
    <main>
        <form action="reset_password_process.php" method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
            <label for="password">New Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>
            <label for="confirm_password">Confirm New Password:</label><br>
            <input type="password" id="confirm_password" name="confirm_password" required><br><br>
            <button type="submit">Reset Password</button>
        </form>
    </main>
    <?php include('footer.php'); ?>
</body>
</html>
