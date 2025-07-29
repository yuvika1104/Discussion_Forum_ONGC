<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once '../Database/db_connect.php';
// Check for pending users (active=2) if user is logged in and an admin
$pendingUsersCount = 0;
if (isset($_SESSION['user_cpf']) && $_SESSION['user_role'] == '1') {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM user WHERE active = 2");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $pendingUsersCount = $result['count'] ?? 0;
    } catch (PDOException $e) {
        // Log error (optional, for debugging)
        error_log("Error fetching pending users: " . $e->getMessage());
        $pendingUsersCount = 0; // Fallback to 0 in case of error
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="bg-maroon text-cream py-3 shadow-sm">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h4 mb-0"><img src="/ONGC/uploads/logo/ongc_logo.webp" alt="ONGC Logo" class="header-icon"
                style="width:45px; height:45px; margin-right: 0.5rem;">Forum</h1>
                <nav>
                    <a href="/ONGC/index.php" class="text-cream text-decoration-none me-3">Home</a>
                    <?php if(!isset($_SESSION['user_cpf'])): ?>
                        <a href="/ONGC/Register/viewRegister.php" class="text-cream text-decoration-none me-3">Register</a>
                    <?php endif;?>
                    <?php if(!isset($_SESSION['user_cpf'])): ?>
                    <a href="/ONGC/Login-Logout/viewLogin.php" class="text-cream text-decoration-none me-3">Login</a>
                    <?php endif;?>
                    <?php if(isset($_SESSION['user_cpf']) && $_SESSION['user_role']=='1'): ?>
                    <span class="notification-badge">
                    <a href="/ONGC/Admin/viewAllUsers.php" class="text-cream text-decoration-none me-3">Users
                    <?php if ($pendingUsersCount > 0): ?>
                                <span class="badge" style="border-radius: 50%;outline: 1px solid #FFFDD0;background-color: #b43441ff;color: white;line-height: 1; min-width: 18px;text-align: center;"><?php echo $pendingUsersCount; ?></span>
                    <?php endif; ?>
                    </a>
                    </span>
                    <?php endif;?>
                    <?php if(isset($_SESSION['user_cpf'])): ?>
                    <a href="/ONGC/Profile/viewProfile.php" class="text-cream text-decoration-none me-3">Profile</a>
                    <?php endif;?>
                    <?php if(isset($_SESSION['user_cpf'])): ?>
                    <a href="/ONGC/Login-Logout/executeLogout.php" class="text-cream text-decoration-none me-3">Logout</a>
                    <?php endif;?>
                    
                </nav>
            </div>
        </div>
    </header>