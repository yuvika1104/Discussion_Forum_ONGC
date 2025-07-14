<?php 
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Session Debug</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
            text-align: center;
        }
        .session-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: left;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 0 10px;
            transition: background-color 0.3s;
        }
        .btn-profile {
            background-color: #007bff;
        }
        .btn-profile:hover {
            background-color: #0056b3;
        }
        .btn-login {
            background-color: #28a745;
        }
        .btn-login:hover {
            background-color: #218838;
        }
        .btn-logout {
            background-color: rgb(214, 34, 34);
        }
        .btn-logout:hover {
            background-color:rgb(136, 33, 33);
        }
        .btn-viewAll {
            background-color: rgb(50, 50, 159);
        }
        .btn-viewAll:hover {
            background-color:rgb(136, 33, 33);
        }
        .btn-create {
            background-color: rgb(50, 50, 159);
        }
        .btn-create:hover {
            background-color:rgb(136, 33, 33);
        }
        .btn-admin {
            background-color: #6f42c1;
        }
        .btn-admin:hover {
            background-color: #5a2d9a;
        }
        .button-container {
            margin-top: 20px;
        }
        .admin-panel {
            background-color: #f8d7da;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 5px solid #dc3545;
        }
    </style>
</head>
<body>
    <div class="session-info">
        <h2>INDEX PAGE</h2>
        <p><strong>Test Message:</strong> hello</p>
        <p><strong>User CPF:</strong> <?php echo isset($_SESSION['user_cpf']) ? htmlspecialchars($_SESSION['user_cpf']) : 'Not set'; ?></p>
        <p><strong>Message:</strong> <?php echo isset($_SESSION['message']) ? htmlspecialchars($_SESSION['message']) : 'Not set'; ?></p>
        <p><strong>Thread ID:</strong> <?php echo isset($_SESSION['thread_id']) ? htmlspecialchars($_SESSION['thread_id']) : 'Not set'; ?></p>
        <p><strong>Role:</strong> <?php echo isset($_SESSION['user_role']) ? htmlspecialchars($_SESSION['user_role']) : 'Not set'; ?></p>
    </div>
    
    <div class="button-container">
        <a href="Profile/viewProfile.php" class="btn btn-profile">Go to Profile</a>
        <a href="Login-Logout/viewLogin.php" class="btn btn-login">Login</a>
        <a href="Login-Logout/executeLogout.php" class="btn btn-logout">Logout</a>
    </div>
    
    <div class="button-container">
        <a href="Thread/viewAllThread.php" class="btn btn-viewAll">View All Threads</a>
        <a href="Thread/createThread.php" class="btn btn-create">Create New Thread</a>
    </div>
    
    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === '1'): ?>
        <div class="admin-panel">
            <h3>Admin Panel</h3>
            <p>Welcome, Administrator. You have special privileges.</p>
            <div class="button-container">
                <a href="Admin/viewAllUsers.php" class="btn btn-admin">View Users</a>
            </div>
        </div>
    <?php endif; ?>
    
    <?php
    // Clear the debug output after displaying
    unset($_SESSION['message']);
    ?>
</body>
</html>