<?php
function display_nav($type) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if ($type == 1) { 
        // Used in home page (index.php)
        // edit poll page (edit_poll.php)
        // poll page (poll.php)
        // manage polls page (manage_polls.php)
        // view results page (view_results.php)
        // vote page (vote.php)
        echo '<nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="polls.php">Polls</a></li>';
        if (isset($_SESSION['user_id'])) {
            echo '<li><a href="create_poll.php">Create Poll</a></li>
                  <li><a href="manage_polls.php">Manage Polls</a></li>
                  <li><a href="manage_profile.php">Manage Account</a></li>
                  <li><a href="logout.php">Logout</a></li>';
        } else {
            echo '<li><a href="login.php">Login</a></li>
                  <li><a href="register.php">Register</a></li>';
        }
        echo '    </ul>
              </nav>';
    } elseif ($type == 2) { // Used in login page (login.php)
        echo '<nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="polls.php">Polls</a></li>
                    <li><a href="register.php">Register</a></li>
                </ul>
              </nav>';
    } elseif ($type == 3) { // Used in create poll page (create_poll.php)
        echo '<nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="polls.php">Polls</a></li>';
        if (isset($_SESSION['user_id'])) {
            echo '<li><a href="logout.php">Logout</a></li>';
        } else {
            echo '<li><a href="login.php">Login</a></li>
                  <li><a href="register.php">Register</a></li>';
        }
        echo '    </ul>
              </nav>';
    } elseif ($type == 4) { // Used in register page (register.php)
        echo '<nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="polls.php">Polls</a></li>
                    <li><a href="login.php">Login</a></li>
                </ul>
              </nav>';
    }
}
?>