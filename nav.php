<?php
function display_nav($type) {
    // Ensure session is started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    echo '<nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="polls.php">Polls</a></li>';

    if ($type === 2) { // Used in login page (login.php)
        echo '<li><a href="login.php">Login</a></li>
              <li><a href="register.php">Register</a></li>';
    } else { 
        // Used in index.php, edit_poll.php, poll.php, manage_polls.php, view_results.php, vote.php
        if (isset($_SESSION['user_id'])) {
            echo '<li><a href="create_poll.php">Create Poll</a></li>
                  <li><a href="manage_polls.php">Manage Polls</a></li>
                  <li><a href="manage_profile.php">Manage Account</a></li>
                  <li><a href="logout.php">Logout</a></li>';
        } else {
            echo '<li><a href="login.php">Login</a></li>
                  <li><a href="register.php">Register</a></li>';
        }
    }
    echo '    </ul>
          </nav>';
}
?>