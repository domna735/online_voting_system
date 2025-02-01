<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Online Voting System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Welcome to the Online Voting Platform</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="polls.php">Polls</a></li>
                <?php
                session_start();
                if (isset($_SESSION['user_id'])) {
                    echo '<li><a href="create_poll.php">Create Poll</a></li>';
                    echo '<li><a href="logout.php">Logout</a></li>';
                } else {
                    echo '<li><a href="login.php">Login</a></li>';
                    echo '<li><a href="register.php">Register</a></li>';
                }
                ?>
            </ul>
        </nav>
    </header>
    <main>
        <?php
        if (isset($_SESSION['login_success'])) {
            echo "<p>Welcome, " . $_SESSION['nickname'] . "! You have successfully logged in.</p>";
            unset($_SESSION['login_success']); // Unset the flag after displaying the message
        }
        ?>
        <section class="intro">
            <h2>Create, Vote, and Discover Opinions</h2>
            <p>Join our community to express your views and see what others think.</p>
            <?php
            if (isset($_SESSION['user_id'])) {
                echo '<a href="create_poll.php" class="btn">Create Poll</a>';
            } else {
                echo '<a href="login.php" class="btn">Get Started</a>';
            }
            ?>
        </section>
    </main>
    <footer>
        <p>&copy; 2023 Online Voting System. All rights reserved.</p>
    </footer>
</body>
</html>


