<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Online Voting System</h1>
        <?php include('nav.php'); display_nav(2); ?>
    </header>
    <main>
        <form action="forgot_password_process.php" method="POST">
            <label for="email">Enter Your Email:</label><br>
            <input type="email" id="email" name="email" required><br><br>
            <button type="submit">Reset Password</button>
        </form>
    </main>
    <?php include('footer.php'); ?>
</body>
</html>