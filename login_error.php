<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Error</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Login Error</h1>
        <?php include('nav.php'); display_nav(2); ?>
    </header>
    <main>
        <!-- Popup Modal -->
        <div id="loginPopup" class="popup" style="display: block;">
            <div class="popup-content">
                <span class="close" onclick="closePopup()">&times;</span>
                <div class="message">User not found or incorrect password.</div>
                <div class="message">Do you have an account?</div>
                <button class="button" onclick="location.href='login.php'">Log In</button>
                <div class="message">If not, you can register here:</div>
                <button class="button" onclick="location.href='register.php'">Register</button>
            </div>
        </div>
    </main>
    <script>
        function closePopup() {
            document.getElementById('loginPopup').style.display = 'none';
        }
    </script>
    <?php include('footer.php'); ?>
</body>
</html>