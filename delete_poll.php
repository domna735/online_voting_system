<?php
session_start();
include('db_connect.php');

// Ensure the user is logged in; otherwise, redirect.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=NotLoggedIn");
    exit;
}

// CSRF token verification using POST
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header("Location: error.php?error=InvalidCSRFToken");
    exit;
}

// Get poll ID from POST data and cast it to an integer
$poll_id = intval($_POST['poll_id']);
$user_id = $_SESSION['user_id'];

// Verify poll ownership
$sql = "SELECT poll_id FROM polls WHERE poll_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Prepare failed (verify ownership): " . $conn->error);
    header("Location: error.php?error=DBError");
    exit;
}
$stmt->bind_param("ii", $poll_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Use a transaction to delete the poll and all associated data atomically
    $conn->begin_transaction();
    try {
        // Delete the poll record
        $stmt_del = $conn->prepare("DELETE FROM polls WHERE poll_id = ?");
        if (!$stmt_del) {
            throw new Exception($conn->error);
        }
        $stmt_del->bind_param("i", $poll_id);
        $stmt_del->execute();
        $stmt_del->close();
        
        // Delete poll options
        $stmt_opt = $conn->prepare("DELETE FROM options WHERE poll_id = ?");
        if (!$stmt_opt) {
            throw new Exception($conn->error);
        }
        $stmt_opt->bind_param("i", $poll_id);
        $stmt_opt->execute();
        $stmt_opt->close();
        
        // Delete poll votes
        $stmt_votes = $conn->prepare("DELETE FROM votes WHERE poll_id = ?");
        if (!$stmt_votes) {
            throw new Exception($conn->error);
        }
        $stmt_votes->bind_param("i", $poll_id);
        $stmt_votes->execute();
        $stmt_votes->close();
        
        // Commit transaction
        $conn->commit();
        header("Location: manage_polls.php?message=PollDeleted");
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Deletion transaction failed: " . $e->getMessage());
        header("Location: error.php?error=DeleteFailed");
        exit;
    }
} else {
    header("Location: error.php?error=NoPermission");
    exit;
}
?>