<?php
ini_set('session.cookie_secure', '1'); // Enable Secure flag
ini_set('session.cookie_httponly', '1'); // Enable HttpOnly flag
ini_set('session.cookie_samesite', 'Strict'); // Set SameSite attribute
session_start();
include('db_connect.php');
// Include the central sanitization functions
include('sanitization.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header("Location: error.php?error=InvalidCSRFToken");
    exit;
}

// Get and sanitize form data using the central function
$login_id         = sanitize_input($_POST['login_id']);
$nickname         = sanitize_input($_POST['nickname']);
$email            = sanitize_input($_POST['email']);
$password         = $_POST['password'];            // Password will be hashed if provided
$confirm_password = $_POST['confirm_password'];
$user_id          = $_SESSION['user_id'];

// Check if passwords match before hashing (if a new password is provided)
if (!empty($password) && $password !== $confirm_password) {
    header('Location: error.php?error=PasswordMismatch');
    exit;
}

// Handle file upload for profile picture
$profile_pic = '';
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
    // Use our central sanitization for filenames (optional)
    $fileName = sanitize_filename(basename($_FILES['profile_picture']['name']));
    $fileSize = $_FILES['profile_picture']['size'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    // Allowed file extensions
    $allowedFileExtensions = ['jpg', 'gif', 'png'];
    if (in_array($fileExtension, $allowedFileExtensions)) {
        // Validate that the file is an actual image
        $check = getimagesize($fileTmpPath);
        if ($check === false) {
            header("Location: error.php?error=InvalidImage");
            exit;
        }

        // Enforce max file size limit (2MB)
        $maxFileSize = 2 * 1024 * 1024;
        if ($fileSize > $maxFileSize) {
            header("Location: error.php?error=FileTooLarge");
            exit;
        }

        // Generate a unique file name and move it to the uploads directory
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        $uploadDir = 'uploads/';
        $destination = $uploadDir . $newFileName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        if (!move_uploaded_file($fileTmpPath, $destination)) {
            header("Location: error.php?error=UploadFailed");
            exit;
        }
        $profile_pic = $destination;
    } else {
        header("Location: error.php?error=InvalidFileType");
        exit;
    }
}

// Check if login ID or email already exists for another user
$sql = "SELECT * FROM users WHERE (login_id = ? OR email = ?) AND user_id != ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Prepare failed (duplicate check): " . $conn->error);
    header("Location: error.php?error=DBError");
    exit;
}
$stmt->bind_param("ssi", $login_id, $email, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    header("Location: error.php?error=DuplicateUser");
    exit;
}
$stmt->close();

// Update user data securely. If the password is provided, hash and update it.
if (!empty($password)) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE users SET login_id = ?, nickname = ?, email = ?, profile_pic = ?, password = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed (update profile with password): " . $conn->error);
        header("Location: error.php?error=DBError");
        exit;
    }
    $stmt->bind_param("sssssi", $login_id, $nickname, $email, $profile_pic, $hashed_password, $user_id);
} else {
    $sql = "UPDATE users SET login_id = ?, nickname = ?, email = ?, profile_pic = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed (update profile): " . $conn->error);
        header("Location: error.php?error=DBError");
        exit;
    }
    $stmt->bind_param("ssssi", $login_id, $nickname, $email, $profile_pic, $user_id);
}

if ($stmt->execute()) {
    header('Location: manage_profile.php?message=ProfileUpdated');
    exit;
} else {
    error_log("Profile update failed: " . $stmt->error);
    header("Location: error.php?error=ProfileUpdateFailed");
    exit;
}
?>