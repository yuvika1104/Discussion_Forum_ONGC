<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../Database/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_cpf'])) {
    $_SESSION['message'] = 'Please login to update your profile.';
    $_SESSION['message_type'] = 'warning';
    header('Location: ../Login-Logout/viewLogin.php');
    exit;
}

$user_cpf = $_SESSION['user_cpf'];

// Get user details
$sql = "SELECT * FROM user WHERE cpf_no = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_cpf]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['message'] = 'User not found.';
    $_SESSION['message_type'] = 'danger';
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form_type = $_POST['form_type'] ?? '';
    
    if ($form_type === 'profile_update') {
        // Handle profile update
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $department = trim($_POST['department']);
        $bio = trim($_POST['bio']);
        $designation = trim($_POST['designation']);
        
        $errors = [];
        
        // Validation
        if (empty($name) || empty($email) || empty($department)) {
            $errors[] = 'Please fill in all required fields.';
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }
        
        // Check if email is already taken by another user
        if (empty($errors)) {
            $check_sql = "SELECT cpf_no FROM user WHERE email = ? AND cpf_no != ?";
            $check_stmt = $pdo->prepare($check_sql);
            $check_stmt->execute([$email, $user_cpf]);
            
            if ($check_stmt->fetch()) {
                $errors[] = 'Email address is already taken.';
            }
        }
        
        // Handle profile image upload
        $profile_image = $user['profile_photo_path'];
        if (!empty($_FILES['profile_image']['name'])) {
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($_FILES['profile_image']['type'], $allowed_types)) {
                $errors[] = 'Please upload a valid image file (JPEG, PNG, GIF).';
            } elseif ($_FILES['profile_image']['size'] > $max_size) {
                $errors[] = 'Image file size must be less than 5MB.';
            } else {
                $extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
                $new_image = uniqid() . '.' . $extension;
                $upload_path = '../uploads/profiles/' . $new_image;
                
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                    // Delete old image if exists
                    if ($profile_image && file_exists('../uploads/profiles/' . $profile_image)) {
                        unlink('../uploads/profiles/' . $profile_image);
                    }
                    $profile_image = $new_image;
                } else {
                    $errors[] = 'Failed to upload profile image.';
                }
            }
        }
        
        // Update profile if no errors
        if (empty($errors)) {
            try {
                $update_sql = "UPDATE user SET name = ?, email = ?, department = ?, bio = ?, designation = ?, profile_photo_path = ? WHERE cpf_no = ?";
                $update_stmt = $pdo->prepare($update_sql);
                $update_stmt->execute([$name, $email, $department, $bio, $designation, $profile_image, $user_cpf]);
                
                // Update session data
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                
                $_SESSION['message'] = 'Profile updated successfully!';
                $_SESSION['message_type'] = 'success';
            } catch (PDOException $e) {
                $errors[] = 'An error occurred while updating your profile.';
                error_log("Profile update error: " . $e->getMessage());
            }
        }
        
        if (!empty($errors)) {
            $_SESSION['message'] = implode('<br>', $errors);
            $_SESSION['message_type'] = 'danger';
        }
        
        header('Location: viewProfile.php');
        exit;
        
    } elseif ($form_type === 'password_change') {
        // Handle password change
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        $errors = [];
        
        // Validation
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $errors[] = 'Please fill in all password fields.';
        }
        
        if (!password_verify($current_password, $user['hashed_password'])) {
            $errors[] = 'Current password is incorrect.';
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
                $update_stmt->execute([$new_password_hash, $user_cpf]);
                
                $_SESSION['message'] = 'Password changed successfully!';
                $_SESSION['message_type'] = 'success';
            } catch (PDOException $e) {
                $errors[] = 'An error occurred while changing your password.';
                error_log("Password change error: " . $e->getMessage());
            }
        }
        
        if (!empty($errors)) {
            $_SESSION['message'] = implode('<br>', $errors);
            $_SESSION['message_type'] = 'danger';
        }
        
        header('Location: viewProfile.php');
        exit;
    }
}

header('Location: viewProfile.php');
exit;