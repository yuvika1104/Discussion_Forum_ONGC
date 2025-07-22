<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../Database/db_connect.php';


if (isset($_SESSION['user_cpf'])) {
    header('Location: ../index.php');
    exit;
}
// Redirect if no CPF/OTP session exists
if (!isset($_SESSION['reset_cpf'])) {
    $_SESSION['forgot_message'] = "Session expired. Please start again.";
    $_SESSION['message_type'] = 'danger';
    header('Location: viewForgotPassword.php');
    exit;
}

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