<?php
session_start();
include('db_connect.php');

// Function to sanitize user input
function test_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Get and sanitize form data
$login_id = test_input($_POST['login_id']);
$nickname = test_input($_POST['nickname']);
$email = test_input($_POST['email']);
$password = $_POST['password']; // Password will be hashed, no need to sanitize yet

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

// Check if login_id or email already exists
$sql = "SELECT * FROM users WHERE login_id = ? OR email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $login_id, $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "Login ID or Email already exists.";
    exit;
}

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert into database
$sql = "INSERT INTO users (login_id, nickname, email, profile_pic, password) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $login_id, $nickname, $email, $profile_pic, $hashed_password);

if ($stmt->execute()) {
    $_SESSION['user_id'] = $stmt->insert_id;
    $_SESSION['nickname'] = $nickname;
    echo "Registration successful!";
    header('Location: index.php');
    exit;
} else {
    echo "Error: " . $stmt->error;
}
?>

