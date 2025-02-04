<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Register</h1>
        <?php include('nav.php'); display_nav(4); ?>
    </header>
    <main>
        <form action="register_process.php" method="POST" enctype="multipart/form-data">
            <label for="login_id">Login ID:</label><br>
            <input type="text" id="login_id" name="login_id" required><br><br>
            <label for="nickname">Nickname:</label><br>
            <input type="text" id="nickname" name="nickname" required><br><br>
            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" required><br><br>
            <label for="profile_picture">Profile Picture:</label><br>
            <input type="file" id="profile_picture" name="profile_picture"><br><br>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>
            <label for="confirm_password">Confirm Password:</label><br>
            <input type="password" id="confirm_password" name="confirm_password" required><br><br>
            <button type="submit">Register</button>
        </form>
    </main>
    <?php include('footer.php'); ?>
</body>
</html>
