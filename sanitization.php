<?php
/**
 * Sanitize a general input string.
 * This function trims whitespace, strips backslashes,
 * and converts special characters to their HTML entities.
 *
 * @param string $data The raw input.
 * @return string The sanitized string.
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Sanitize a filename.
 * This function removes any path info and then filters out 
 * any characters not allowed in a safe filename.
 *
 * @param string $filename The raw filename (e.g., from an upload).
 * @return string The sanitized filename.
 */
function sanitize_filename($filename) {
    // Remove directory path and null bytes
    $filename = basename($filename);
    // Allow only letters, numbers, dashes, underscores, and dots in filename
    $filename = preg_replace("/[^A-Za-z0-9_\-\.]/", "", $filename);
    return $filename;
}

/**
 * Sanitize an email address.
 * This function uses PHP's filter_var to remove illegal characters.
 *
 * @param string $email The raw email address.
 * @return string The sanitized email address.
 */
function sanitize_email($email) {
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}

/**
 * Sanitize an integer.
 * This function filters the data as an integer to ensure only numeric input.
 *
 * @param mixed $data The raw input.
 * @return int The sanitized integer.
 */
function sanitize_int($data) {
    return filter_var($data, FILTER_SANITIZE_NUMBER_INT);
}

/**
 * Sanitize a URL.
 * This function uses PHP's filter_var to clean up a URL.
 *
 * @param string $url The raw URL.
 * @return string The sanitized URL.
 */
function sanitize_url($url) {
    return filter_var(trim($url), FILTER_SANITIZE_URL);
}
?>