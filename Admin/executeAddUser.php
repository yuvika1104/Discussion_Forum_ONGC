<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../Database/db_connect.php';
require_once '../Auth/admin_session_check.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cpf_no = trim($_POST['cpf_no']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $department = trim($_POST['department']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $designation = trim($_POST['designation']);
    $phone_number = trim($_POST['phone_number']);
    $role= trim($_POST  ['role']);
    
    $errors = [];
    
    // Validation
    if (empty($cpf_no) || empty($name) || empty($email) || empty($department) || empty($password)|| empty($role)) {
        $errors[] = 'Please fill in all required fields.';
    }
    
    if (strlen($cpf_no) !== 5 || !ctype_digit($cpf_no)) {
        $errors[] = 'CPF number must be exactly 5 digits.';
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    }
    
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }
    
    // Check if CPF or email already exists
    if (empty($errors)) {
        try {
            $check_sql = "SELECT cpf_no FROM user WHERE cpf_no = ? OR email = ?";
            $check_stmt = $pdo->prepare($check_sql);
            $check_stmt->execute([$cpf_no, $email]);
            
            if ($check_stmt->fetch()) {
                $errors[] = 'CPF number or email already exists.';
            }
        } catch (PDOException $e) {
            $errors[] = 'An error occurred. Please try again.';
            error_log("Database error: " . $e->getMessage());
        }
    }
    $role_no= $role=='Admin'? 1:0;
    // Register user if no errors
    if (empty($errors)) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO user (cpf_no, name, email, department, hashed_password, designation, phone_number, role,active) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?,?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$cpf_no, $name, $email, $department, $hashed_password, $designation, $phone_number, $role_no,1]);
            
            $_SESSION['message'] = 'Registration successful! New user added.';
            $_SESSION['message_type'] = 'success';
            header('Location: viewAllUsers.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'An error occurred during registration. Please try again.';
            error_log("Registration error: " . $e->getMessage());
        }
    }
    
    if (!empty($errors)) {
        $_SESSION['message'] = implode('<br>', $errors);
        $_SESSION['message_type'] = 'danger';
        $_SESSION['form_data'] = $_POST;
        header('Location: viewAllUsers.php');
        exit;
    }
}

header('Location: viewAllUsers.php');
exit;