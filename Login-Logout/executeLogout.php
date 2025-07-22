<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to login page with success message
session_start();
$_SESSION['message'] = 'You have been logged out successfully.';
$_SESSION['message_type'] = 'success';

header('Location: viewLogin.php');
exit;
?>
