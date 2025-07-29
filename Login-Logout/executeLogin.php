<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../Database/db_connect.php';

// Initialize message variables if not set
$_SESSION['message'] = $_SESSION['message'] ?? '';
$_SESSION['message_type'] = $_SESSION['message_type'] ?? '';

// Redirect if already logged in
if (isset($_SESSION['user_cpf'])) {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cpf_no = trim($_POST['cpf_no']) ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($cpf_no) || empty($password)) {
        $_SESSION['message'] = 'Please fill in all fields.';
        $_SESSION['message_type'] = 'danger';
    } else {
        try {
            $sql = "SELECT cpf_no, name, email, hashed_password, role, active FROM user WHERE cpf_no = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$cpf_no]);
            $user = $stmt->fetch();
            if($user['active']!= 1)
            {
                $_SESSION['message'] = 'Your acount is inactive. Please contact the Administrator.';
                $_SESSION['message_type'] = 'danger';
                header('Location: viewLogin.php');
            }
            else
            {
            if ($user && password_verify($password, $user['hashed_password'])) {
                // Regenerate session ID to prevent fixation
                session_regenerate_id(true);
                
                $_SESSION['user_cpf'] = $user['cpf_no'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['logged_in'] = true;
                $_SESSION['last_activity'] = time();
                
                $_SESSION['message'] = 'Welcome back, ' . htmlspecialchars($user['name']) . '!';
                $_SESSION['message_type'] = 'success';
                header('Location: ../index.php');
                exit;
            } else {
                $_SESSION['message'] = 'Invalid CPF number or password.';
                $_SESSION['message_type'] = 'danger';
                header('Location: viewLogin.php');
            }
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            $_SESSION['message'] = 'An error occurred. Please try again.';
            $_SESSION['message_type'] = 'danger';
        }
    }
}
?>