<?php
ini_set('session.cookie_secure', '1'); // Enable Secure flag
ini_set('session.cookie_httponly', '1'); // Enable HttpOnly flag
ini_set('session.cookie_samesite', 'Strict'); // Set SameSite attribute
session_start();
$error_message = "User not found or incorrect password."; // Default error message

// Check if an error parameter is passed in the URL
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'TooManyAttemptsIP':
            $error_message = "Too many failed login attempts. Please try again after 15 minutes.";
            break;
        case 'TooManyAttemptsAcc':
            $error_message = "Too many failed login attempts on this account. Please try again after 15 minutes.";
            break;
        case 'InvalidCSRFToken':
            $error_message = "Invalid CSRF token. Please try again.";
            break;
    }
}
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
                <div class="message"><?php echo htmlspecialchars($error_message); ?></div>
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