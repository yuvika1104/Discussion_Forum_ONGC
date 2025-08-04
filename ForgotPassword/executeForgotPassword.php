
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

// Include PHPMailer files manually
require '../includes/PHPMailer-master/src/PHPMailer.php';
require '../includes/PHPMailer-master/src/SMTP.php';
require '../includes/PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cpf_no = filter_input(INPUT_POST, 'cpf_no', FILTER_SANITIZE_STRING);
    
    // Check if CPF exists and get the associated email
    $stmt = $pdo->prepare("SELECT email,active FROM user WHERE cpf_no = ?");
    $stmt->execute([$cpf_no]);
    $user = $stmt->fetch();
    
    if ($user && !empty($user['email']) && $user['active']==1) {
        // Generate OTP (6 digits)
        $otp = random_int(100000, 999999); // More secure than rand()
        $otp_expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        
        // Store OTP and user info in session
        $_SESSION['reset_cpf'] = $cpf_no;
        $_SESSION['reset_email'] = $user['email'];
        $_SESSION['reset_otp'] = $otp;
        $_SESSION['otp_expiry'] = $otp_expiry;
        
        // Send OTP via email using PHPMailer
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
            $mail->addAddress($user['email']);
            $mail->Subject = 'Password Reset OTP';
            $mail->Body = "Your OTP for password reset is: $otp \nThis OTP is valid for 15 minutes.";
            $mail->AltBody = "Your OTP for password reset is: $otp \nThis OTP is valid for 15 minutes."; // Plain text version

            // Send email
            $mail->send();
            $_SESSION['forgot_message'] = "OTP sent to your registered email (Demo OTP: $otp)"; // Include OTP for testing
            $_SESSION['message_type'] = 'success';
            header('Location: viewVerifyOTP.php');
            exit;
        } catch (Exception $e) 
        {
            echo $mail->ErrorInfo;
            $_SESSION['forgot_message'] = "Failed to send OTP: {$mail->ErrorInfo}";
            $_SESSION['message_type'] = 'danger';
            // header('Location: viewForgotPassword.php');
            exit;
        }
    } else {
        $_SESSION['forgot_message'] = "CPF number not found or no email registered or User is deactivated.";
        $_SESSION['message_type'] = 'danger';
        header('Location: viewForgotPassword.php');
        exit;
    }
}

header('Location: viewForgotPassword.php');
exit;