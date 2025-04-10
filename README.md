 Online Voting System

 Overview
This project is a secure online voting system designed to allow users to create polls, vote, and view results while maintaining robust authentication and security measures.

 Features
 🔹 User Management
- Registration: Users can create an account with a unique login ID, email, nickname, and password.
- Login & Logout: Users securely authenticate using their credentials.
- Profile Management: Users can update their account details, including their profile picture.

 🔹 Poll Management
- Create Polls: Registered users can create polls with multiple options.
- Vote in Polls: Users can select an option and submit their vote securely.
- View Poll Results: Poll results are displayed with real-time updates.
- Manage Polls: Users can edit, update, or delete their own polls.

 🔹 Commenting System
- Leave Comments: Users can provide feedback on polls.
- View Comments: Comments are displayed under each poll.
- Delete Comments: Users can manage their own comments.

 🔹 Password Recovery
- Forgot Password: Users can request a password reset via email.
- Reset Password: Users can securely reset their password using a time-sensitive reset token.


 Security Features Implemented
 🔐 Authentication & Access Control
- Session Authentication: Ensures users are logged in before accessing restricted features.
- Session Hijacking Prevention: Session IDs are regenerated upon login to prevent session fixation attacks.
- Unauthorized Access Prevention: Users attempting to access restricted areas are redirected to the login page.

 🔐 CSRF Protection
- Token Validation: All sensitive actions (poll creation, voting, profile updates) include CSRF token validation to prevent cross-site request forgery.
- Hidden Token Fields: Forms include hidden CSRF tokens that must match the session token.

 🔐 Input Validation & Sanitization
- Preventing XSS Attacks: All user-generated content (comments, poll questions, profile information) is sanitized using `htmlspecialchars()`.
- Form Data Cleaning: The `test_input()` function strips unnecessary characters and slashes before processing input.

 🔐 Database Security
- Prepared Statements: All database queries use prepared statements to prevent SQL injection.
- Duplicate User Checks: Registration checks if login ID or email already exists before creating a new account.
- Poll & Vote Validation: Ensures users vote on valid options within a valid poll.

 🔐 File Upload Security
- Allowed File Types: Profile pictures must be JPG, PNG, or GIF.
- File Size Limit: Profile pictures are limited to 2MB max.
- File Integrity Check: The system verifies uploaded files are actual images before processing.
- Secure Storage: Uploaded images are stored using MD5-hashed file names to prevent arbitrary execution.

 🔐 Error Handling & Logging
- Centralized Error Management (`error.php`): Displays user-friendly error messages based on predefined error codes.
- Error Logging (`error_log()`): System errors are logged instead of exposing details to users.
- Redirections for Security: Any unauthorized or invalid request redirects users safely.

 🔐 Password Security
- Password Hashing: User passwords are stored using PHP’s `password_hash()` function.
- Secure Password Reset: Password resets are handled via a time-limited reset token.

 🔐 Preventing Email Enumeration
- Generic Response for Password Reset Requests: Users receive the same response whether an email exists or not, preventing attackers from determining valid accounts.


 Installation & Setup
 🔧 Requirements
- PHP 7.4+  
- MySQL Database  
- Apache or Nginx Web Server  
