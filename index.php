<?php 
session_start();
header("Location: Thread/viewAllThread.php");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Debug</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="includes/styles.css">
</head>
<body>
    <?php require_once 'includes/header.php'; ?>
    
    <div class="container my-5">
        <div class="session-info shadow-lg p-4 bg-cream">
            <h2 class="text-center text-maroon mb-4">INDEX PAGE</h2>
            <p><strong>Test Message:</strong> hello</p>
            <p><strong>User CPF:</strong> <?php echo isset($_SESSION['user_cpf']) ? htmlspecialchars($_SESSION['user_cpf']) : 'Not set'; ?></p>
            <p><strong>Message:</strong> <?php echo isset($_SESSION['message']) ? htmlspecialchars($_SESSION['message']) : 'Not set'; ?></p>
            <p><strong>Thread ID:</strong> <?php echo isset($_SESSION['thread_id']) ? htmlspecialchars($_SESSION['thread_id']) : 'Not set'; ?></p>
            <p><strong>Role:</strong> <?php echo isset($_SESSION['user_role']) ? htmlspecialchars($_SESSION['user_role']) : 'Not set'; ?></p>
        </div>
        
        <div class="button-container text-center mt-4">
            <a href="Profile/viewProfile.php" class="btn btn-maroon">Go to Profile</a>
            <a href="Login-Logout/viewLogin.php" class="btn btn-maroon">Login</a>
            <a href="Login-Logout/executeLogout.php" class="btn btn-maroon">Logout</a>
        </div>
        
        <div class="button-container text-center mt-4">
            <a href="Thread/viewAllThread.php" class="btn btn-maroon">View All Threads</a>
            <a href="Thread/viewCreateThread.php" class="btn btn-maroon">Create New Thread</a>
        </div>
        
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === '1'): ?>
            <div class="admin-panel shadow-lg p-4 bg-cream mt-4">
                <h3 class="text-maroon">Admin Panel</h3>
                <p>Welcome, Administrator. You have special privileges.</p>
                <div class="button-container text-center">
                    <a href="Admin/viewAllUsers.php" class="btn btn-maroon">View Users</a>
                </div>
            </div>
        <?php endif; ?>
        
        <?php
        // Clear the debug output after displaying
        unset($_SESSION['message']);
        ?>
    </div>
    
    <?php require_once 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>