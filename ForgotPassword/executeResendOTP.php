<?php
session_start();
require_once '../Database/db_connect.php';

// Check if we have the required session data
if (!isset($_SESSION['reset_cpf'])) {
    $_SESSION['forgot_message'] = "Session expired. Please start again.";
    $_SESSION['message_type'] = 'danger';
    header('Location: viewForgotPassword.php');
    exit;
}

// Generate new OTP
$otp = rand(100000, 999999);
$otp_expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));

// Update session with new OTP
$_SESSION['reset_otp'] = $otp;
$_SESSION['otp_expiry'] = $otp_expiry;

// In production: Resend the email
$to = $_SESSION['reset_email'];
$subject = "New Password Reset OTP";
$message = "Your new OTP is: $otp";
$headers = "From: no-reply@yourdomain.com";

// mail($to, $subject, $message, $headers); // Uncomment in production

// For demo purposes
$_SESSION['otp_message'] = "New OTP sent (Demo OTP: $otp)";
$_SESSION['otp_message_type'] = 'success';

header('Location: viewVerifyOTP.php');
exit;