<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../Database/db_connect.php';
require_once '../Auth/admin_session_check.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_cpf'])) {
    $user_cpf =$_POST['user_cpf'];
    
    // Prevent admin from deleting themselves
    if ($user_cpf === $_SESSION['user_cpf']) {
        $_SESSION['message'] = "You cannot delete your own account!";
        $_SESSION['message_type'] = "danger";
        header("Location: viewAllUsers.php");
        exit();
    }

    try {
        // Begin transaction
        $pdo->beginTransaction();

        // Delete user
        $stmt = $pdo->prepare("DELETE FROM user WHERE cpf_no = ?");
        $stmt->execute([$user_cpf]);
        
        $pdo->commit();
        
        $_SESSION['message'] = "User deleted successfully";
        $_SESSION['message_type'] = "success";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['message'] = "Error deleting user: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }
    
    header("Location: viewAllUsers.php");
    exit();
} else {
    header("Location: viewAllUsers.php");
    exit();
}