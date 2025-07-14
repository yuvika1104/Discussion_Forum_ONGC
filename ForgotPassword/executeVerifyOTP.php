<?php
session_start();
require_once '../Database/db_connect.php';

// Redirect if not a POST request or missing session data
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['reset_otp'])) {
    $_SESSION['forgot_message'] = "Invalid request";
    $_SESSION['message_type'] = 'danger';
    header('Location: viewForgotPassword.php');
    exit;
}

$user_otp = filter_input(INPUT_POST, 'otp', FILTER_SANITIZE_NUMBER_INT);

// Verify OTP matches and isn't expired
if ($user_otp == $_SESSION['reset_otp']) {
    if (time() < strtotime($_SESSION['otp_expiry'])) {
        // OTP verified - allow password reset
        $_SESSION['otp_verified'] = true;
        header('Location: viewResetPassword.php');
        exit;
    } else {
        $_SESSION['otp_message'] = "OTP has expired";
        $_SESSION['otp_message_type'] = 'danger';
    }
} else {
    $_SESSION['otp_message'] = "Invalid OTP code";
    $_SESSION['otp_message_type'] = 'danger';
}

header('Location: viewVerifyOTP.php');
exit;