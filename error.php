<?php
session_start();

// Retrieve the error code passed to the page
$errorCode = isset($_GET['error']) ? $_GET['error'] : 'UnknownError';

// Define an array of friendly error messages for each error code
$errorMessages = [
    'DBError'                      => 'A database error occurred. Please try again later.',
    'DBConnection'                 => 'Database connection failed. Please try again later.',
    'DeleteFailed'                 => 'Failed to delete item. Please try again.',
    'NoPermission'                 => 'You do not have permission to perform this action.',
    'PollUpdateFailed'             => 'Failed to update the poll. Please try again later.',
    'OptionUpdateFailed'           => 'Error updating poll option. Please try again.',
    'InvalidVoteInput'             => 'Invalid vote input. Please check your selection and try again.',
    
    'InvalidCSRFToken'             => 'Invalid security token. Please refresh the page and try again.',
    'InvalidPollId'                => 'An invalid poll ID was provided.',
    'InvalidVoteOption'            => 'Invalid vote option selected. Please try again.',
    'EmptyComment'                 => 'Your comment cannot be empty.',
    'CommentFailed'                => 'There was an error submitting your comment. Please try again later.',
    'NotLoggedIn'                  => 'You must be logged in to perform this action.',
    'InvalidNumberOfOptions'       => 'Invalid number of poll options provided.',
    'PollCreationFailed'           => 'Unable to create poll. Please try again later.',
    'PollOptionCreationFailed'     => 'Unable to create poll options. Please try again later.',
    'VoteFailed'                   => 'An error occurred while submitting your vote. Please try again.',
    'RegistrationFailed'           => 'Registration failed. Please try again later.',

    'InvalidEmail'                 => 'The email address provided is not valid.',
    'PasswordMismatch'             => 'Passwords do not match. Please try again.',
    'InvalidImage'                 => 'The uploaded file is not recognized as a valid image.',
    'FileTooLarge'                 => 'The uploaded file exceeds the maximum allowed size.',
    'UploadFailed'                 => 'File upload failed. Please try again.',
    'InvalidFileType'              => 'The uploaded file type is not allowed.',
    
    'DuplicateUser'                => 'Login ID or email already exists.',
    'ProfileUpdateFailed'          => 'Failed to update profile. Please try again.',
    'UserNotFound'                 => 'User not found. Please check the details and try again.',
];

// Choose the message; if error code is not found, use a generic message.
$message = isset($errorMessages[$errorCode]) ? $errorMessages[$errorCode] : 'An unexpected error has occurred.';

// Optional: Log the error code if needed for troubleshooting.
// error_log("Error occurred: " . $errorCode);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Error</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Error</h1>
        <?php include('nav.php'); ?>
    </header>
    <main>
        <p><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
        <a href="index.php">Back to Home</a>
    </main>
    <?php include('footer.php'); ?>
</body>
</html>