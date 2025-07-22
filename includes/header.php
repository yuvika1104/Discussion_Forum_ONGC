<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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
                    <a href="/ONGC/Login-Logout/viewLogin.php" class="text-cream text-decoration-none">Login</a>
                    <?php endif;?>
                    <?php if(isset($_SESSION['user_cpf'])): ?>
                    <a href="/ONGC/Login-Logout/executeLogout.php" class="text-cream text-decoration-none">Logout</a>
                    <?php endif;?>
                </nav>
            </div>
        </div>
    </header>