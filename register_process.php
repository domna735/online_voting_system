<?php
session_start();
include('db_connect.php');

// CSRF Token Verification
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    // Token is invalid, possibly a CSRF attack!
    header("Location: error.php?error=InvalidCSRFToken");
    exit;
}

// Function to sanitize user input
function test_input($data) {
    return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Get and sanitize form data
$login_id = test_input($_POST['login_id']);
$nickname = test_input($_POST['nickname']);
$email    = test_input($_POST['email']);
$password = $_POST['password']; // Do not alter password value—hashing takes care of safety.
$confirm_password = $_POST['confirm_password'];

// Check if passwords match
if ($password !== $confirm_password) {
    header('Location: password_mismatch.php?source=register');
    exit;
}

// Handle file upload for profile picture
$profile_pic = '';
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
    $fileName = basename($_FILES['profile_picture']['name']);
    $fileSize = $_FILES['profile_picture']['size'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    // Allowed file extensions
    $allowedfileExtensions = array('jpg', 'gif', 'png');
    if (in_array($fileExtension, $allowedfileExtensions)) {
        // Enhanced File Validation: Verify the file is an actual image
        $check = getimagesize($fileTmpPath);
        if ($check === false) {
            header("Location: upload_error.php?error=NotAnImage");
            exit;
        }

        // Enforce a file size limit (2MB maximum)
        $maxFileSize = 2 * 1024 * 1024;  // 2MB
        if ($fileSize > $maxFileSize) {
            header("Location: upload_error.php?error=FileTooLarge");
            exit;
        }

        // Sanitize file name and move it to the uploads directory
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        $uploadFileDir = 'uploads/';
        $destination = $uploadFileDir . $newFileName;
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0755, true);
        }
        if (!move_uploaded_file($fileTmpPath, $destination)) {
            header("Location: upload_error.php?error=UploadFail");
            exit;
        }
        $profile_pic = $destination;
    } else {
        header("Location: upload_error.php?error=InvalidFileType");
        exit;
    }
}

// Check if login_id or email already exists
$sql = "SELECT * FROM users WHERE login_id = ? OR email = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Prepare failed (duplicate check): " . $conn->error);
    header("Location: error.php?error=DBError");
    exit;
}
$stmt->bind_param("ss", $login_id, $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    header('Location: duplicate_user.php');
    exit;
}
$stmt->close();

// Hash the password using PHP's password_hash function
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert the new user into the database using prepared statements
$sql = "INSERT INTO users (login_id, nickname, email, profile_pic, password) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Prepare failed (insert user): " . $conn->error);
    header("Location: error.php?error=DBError");
    exit;
}
$stmt->bind_param("sssss", $login_id, $nickname, $email, $profile_pic, $hashed_password);

if ($stmt->execute()) {
    // Regenerate session ID to avoid session fixation attacks
    session_regenerate_id(true);
    $_SESSION['user_id'] = $stmt->insert_id;
    $_SESSION['nickname'] = $nickname;
    
    // Redirect to the home page upon successful registration
    header('Location: index.php');
    exit;
} else {
    // Log the database error rather than outputting it to the user
    error_log("Database error during registration: " . $stmt->error);
    header('Location: error.php?error=RegistrationFailed');
    exit;
}
?>