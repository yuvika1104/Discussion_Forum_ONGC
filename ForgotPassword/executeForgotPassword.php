<?php
session_start();
require_once '../Database/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cpf_no = filter_input(INPUT_POST, 'cpf_no', FILTER_SANITIZE_STRING);
    
    // Check if CPF exists and get the associated email
    $stmt = $pdo->prepare("SELECT email FROM user WHERE cpf_no = ?");
    $stmt->execute([$cpf_no]);
    $user = $stmt->fetch();
    
    if ($user && !empty($user['email'])) {
        // Generate OTP (6 digits)
        $otp = rand(100000, 999999);
        $otp_expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        
        // Store OTP and user info in session
        $_SESSION['reset_cpf'] = $cpf_no;
        $_SESSION['reset_email'] = $user['email'];
        $_SESSION['reset_otp'] = $otp;
        $_SESSION['otp_expiry'] = $otp_expiry;
        
        // In production: Send OTP via email
        $to = $user['email'];
        $subject = "Password Reset OTP";
        $message = "Your OTP for password reset is: $otp";
        $headers = "From: no-reply@yourdomain.com";
        
        // Uncomment to actually send email
        // mail($to, $subject, $message, $headers);
        
        // For testing/demo purposes - show the OTP
        $_SESSION['forgot_message'] = "OTP sent to your registered email (Demo OTP: $otp)";
        $_SESSION['message_type'] = 'success';
        
        header('Location: viewVerifyOTP.php');
        exit;
    } else {
        $_SESSION['forgot_message'] = "CPF number not found or no email registered";
        $_SESSION['message_type'] = 'danger';
        header('Location: viewForgotPassword.php');
        exit;
    }
}

header('Location: viewForgotPassword.php');
exit;