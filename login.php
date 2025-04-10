<?php
session_start();
// Generate a CSRF token for the login form if not already set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Login</h1>
        <?php include('nav.php'); display_nav(2); ?>
    </header>
    <main>
        <form action="login_process.php" method="POST">
            <!-- Include the CSRF token in a hidden field -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <label for="login_id">Login ID:</label><br>
            <input type="text" id="login_id" name="login_id" required><br><br>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>
            <button type="submit">Login</button><br><br>
            <a href="Forget_password.php">Forget Password</a>
        </form>
    </main>
    <?php include('footer.php'); ?>
</body>
</html>