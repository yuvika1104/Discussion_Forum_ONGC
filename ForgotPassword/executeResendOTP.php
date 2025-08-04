<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../Database/db_connect.php';
require_once '../password.php';

// Redirect if user is already logged in
if (isset($_SESSION['user_cpf'])) {
    header('Location: ../index.php');
    exit;
}

// Check if we have the required session data
if (!isset($_SESSION['reset_cpf'])) {
    $_SESSION['forgot_message'] = "Session expired. Please start again.";
    $_SESSION['message_type'] = 'danger';
    header('Location: viewForgotPassword.php');
    exit;
}

// Include PHPMailer files manually
require '../includes/PHPMailer-master/src/PHPMailer.php';
require '../includes/PHPMailer-master/src/SMTP.php';
require '../includes/PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Generate new OTP
$otp = random_int(100000, 999999); // More secure than rand()
$otp_expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));

// Update session with new OTP
$_SESSION['reset_otp'] = $otp;
$_SESSION['otp_expiry'] = $otp_expiry;

// Send new OTP via email using PHPMailer
$mail = new PHPMailer(true);
try {
    // SMTP settings for Gmail
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = SENDER_MAIL; // Your Gmail address
    $mail->Password = APP_PASSWORD_FOR_MAIL; // App Password from Gmail
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Email content
    $mail->setFrom(SENDER_MAIL, 'ONGC Forum');
    $mail->addAddress($_SESSION['reset_email']);
    $mail->Subject = 'New Password Reset OTP';
    $mail->Body = "Your new OTP for password reset is: $otp\nThis OTP is valid for 15 minutes.";
    $mail->AltBody = "Your new OTP for password reset is: $otp\nThis OTP is valid for 15 minutes."; // Plain text version

    // Send email
    $mail->send();
    $_SESSION['otp_message'] = "New OTP sent to your registered email (Demo OTP: $otp)"; // Include OTP for testing
    $_SESSION['otp_message_type'] = 'success';
} catch (Exception $e) {
    $_SESSION['otp_message'] = "Failed to send new OTP: {$mail->ErrorInfo}";
    $_SESSION['otp_message_type'] = 'danger';
}

header('Location: viewVerifyOTP.php');
exit;