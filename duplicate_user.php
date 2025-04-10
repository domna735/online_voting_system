<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Duplicate User</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Registration Error</h1>
        <?php include('nav.php'); display_nav(4); ?>
    </header>
    <main>
        <!-- Popup Modal -->
        <div id="duplicateUserPopup" class="popup" style="display: block;">
            <div class="popup-content">
                <span class="close" onclick="closePopup()">&times;</span>
                <div class="message">Login ID or Email already exists.</div>
                <div class="message">Please choose a different Login ID or Email.</div>
                <button class="button" onclick="location.href='register.php'">Register</button>
            </div>
        </div>
    </main>
    <script>
        function closePopup() {
            document.getElementById('duplicateUserPopup').style.display = 'none';
        }
    </script>
    <?php include('footer.php'); ?>
</body>
</html>