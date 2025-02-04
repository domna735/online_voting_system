<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password Mismatch</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Password Mismatch</h1>
        <?php include('nav.php'); display_nav(4); ?>
    </header>
    <main>
        <!-- Popup Modal -->
        <div id="passwordMismatchPopup" class="popup" style="display: block;">
            <div class="popup-content">
                <span class="close" onclick="closePopup()">&times;</span>
                <?php if ($_GET['source'] === 'profile') { ?>
                    <div class="message">Passwords do not match.</div>
                    <div class="message">Please re-enter your password and confirm password.</div>
                    <button class="button" onclick="location.href='manage_profile.php'">Manage Profile</button>
                <?php } else { ?>
                    <div class="message">Passwords do not match.</div>
                    <div class="message">Please re-enter your password and confirm password.</div>
                    <button class="button" onclick="location.href='register.php'">Register</button>
                <?php } ?>
            </div>
        </div>
    </main>
    <script>
        function closePopup() {
            document.getElementById('passwordMismatchPopup').style.display = 'none';
        }
    </script>
    <?php include('footer.php'); ?>
</body>
</html>

