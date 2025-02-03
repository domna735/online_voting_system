<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to update your profile.";
    exit;
}

// Function to sanitize user input
function test_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Get and sanitize form data
$login_id = test_input($_POST['login_id']);
$nickname = test_input($_POST['nickname']);
$email = test_input($_POST['email']);
$password = $_POST['password']; // Password will be hashed if provided
$user_id = $_SESSION['user_id'];

// Handle file upload for profile picture
$profile_pic = '';
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
    $fileName = basename($_FILES['profile_picture']['name']);
    $fileSize = $_FILES['profile_picture']['size'];
    $fileType = $_FILES['profile_picture']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    // Allowed file extensions
    $allowedfileExtensions = array('jpg', 'gif', 'png');
    if (in_array($fileExtension, $allowedfileExtensions)) {
        // Sanitize file name and move it to the uploads directory
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        $uploadFileDir = 'uploads/';
        $destination = $uploadFileDir . $newFileName;
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0755, true);
        }
        move_uploaded_file($fileTmpPath, $destination);
        $profile_pic = $destination;
    } else {
        echo "Upload failed. Allowed file types: " . implode(',', $allowedfileExtensions);
        exit;
    }
}

// Check if login_id or email already exists for another user
$sql = "SELECT * FROM users WHERE (login_id = ? OR email = ?) AND user_id != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $login_id, $email, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "Login ID or Email already exists.";
    exit;
}

// Update user data
$sql = "UPDATE users SET login_id = ?, nickname = ?, email = ?, profile_pic = ? WHERE user_id = ?";
if ($password) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE users SET login_id = ?, nickname = ?, email = ?, profile_pic = ?, password = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $login_id, $nickname, $email, $profile_pic, $hashed_password, $user_id);
} else {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $login_id, $nickname, $email, $profile_pic, $user_id);
}

if ($stmt->execute()) {
    echo "Profile updated successfully!";
    header('Location: manage_profile.php');
    exit;
} else {
    echo "Error: " . $stmt->error;
}
?>
