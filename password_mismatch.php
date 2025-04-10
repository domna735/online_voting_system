<?php
session_start();

// Validate the 'source' input to prevent security risks
$source = isset($_GET['source']) ? htmlspecialchars($_GET['source'], ENT_QUOTES, 'UTF-8') : '';
?>
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
                <div class="message">Passwords do not match.</div>
                <div class="message">Please re-enter your password and confirm password.</div>
                <button class="button" onclick="location.href='<?php echo ($source === 'profile') ? 'manage_profile.php' : 'register.php'; ?>'">
                    <?php echo ($source === 'profile') ? 'Manage Profile' : 'Register'; ?>
                </button>
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