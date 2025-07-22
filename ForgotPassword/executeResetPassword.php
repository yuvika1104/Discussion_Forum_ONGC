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
if (!isset($_SESSION['reset_cpf']) || !isset($_SESSION['reset_otp'])) {
    $_SESSION['forgot_message'] = "Session expired. Please start again.";
    $_SESSION['message_type'] = 'danger';
    header('Location: viewForgotPassword.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'] ?? ' ';
    $confirm_password = $_POST['confirm_password'] ?? ' ';

    $errors = [];

    // Validation
    if (empty($new_password) || empty($confirm_password)) {
        $errors[] = 'Please fill in all password fields.';
    }

    if (strlen($new_password) < 6) {
        $errors[] = 'New password must be at least 6 characters long.';
    }

    if ($new_password !== $confirm_password) {
        $errors[] = 'New passwords do not match.';
    }

    // Update password if no errors
    if (empty($errors)) {
        try {
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $update_sql = "UPDATE user SET hashed_password = ? WHERE cpf_no = ?";
            $update_stmt = $pdo->prepare($update_sql);
            $update_stmt->execute([$new_password_hash, $_SESSION['reset_cpf']]);

            $_SESSION['message'] = 'Password changed successfully!';
            $_SESSION['message_type'] = 'success';
            header('Location: ../Login-Logout/viewLogin.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'An error occurred while changing your password.';
            error_log("Password change error: " . $e->getMessage());
        }
    }

    if (!empty($errors)) {
        $_SESSION['message'] = implode('<br>', $errors);
        $_SESSION['message_type'] = 'danger';
        header('Location: viewResetPassword.php');
        exit;
    }
} else {
    // Redirect if accessed directly
    $_SESSION['message'] = 'Invalid request method.';
    $_SESSION['message_type'] = 'danger';
    header('Location: ../Login-Logout/viewLogin.php');
    exit;
}
?>