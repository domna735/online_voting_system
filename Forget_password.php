<?php
session_start();

// Generate a CSRF token if it doesn't exist
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Reset Your Password</h1>
        <?php include('nav.php'); display_nav(2); ?>
    </header>
    <main>
        <form action="forgot_password_process.php" method="POST">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <label for="email">Enter Your Email:</label><br>
            <input type="email" id="email" name="email" required><br><br>
            <button type="submit">Reset Password</button>
        </form>
    </main>
    <?php include('footer.php'); ?>
</body>
</html>