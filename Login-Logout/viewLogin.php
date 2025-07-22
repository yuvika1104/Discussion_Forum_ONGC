<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user_cpf'])) {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login to Forum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../includes/styles.css">
</head>
<body>
    <?php require_once '../includes/header.php'; ?>
    
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="form-container shadow-lg p-4 bg-cream">
                    
                    <h2 class="text-center mb-4"><i class="fas fa-sign-in-alt text-maroon"></i> Login to Forum</h2>
                    <?php if(isset($_SESSION['message'])&& isset($_SESSION['message_type']) && $_SESSION['message_type']=='danger'): ?>
                                <h5 class="text-center mb-4" style="color: maroon"><?php echo $_SESSION['message']; ?></h5>
                        <?php endif; ?>
                    <form method="POST" action="executeLogin.php">
                        <div class="mb-3">
                            <label for="cpf_no" class="form-label text-maroon">CPF Number</label>
                            <input type="text" class="form-control" id="cpf_no" name="cpf_no" 
                                   placeholder="12345" maxlength="5" 
                                   value="<?php echo isset($_POST['cpf_no']) ? htmlspecialchars($_POST['cpf_no']) : ''; ?>" 
                                   required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label text-maroon">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-maroon w-100 mb-3">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>
                    </form>
                    
                    <div class="text-center">
                        <a href="../ForgotPassword/viewForgotPassword.php" class="text-decoration-none text-maroon">Forgot Password?</a>
                    </div>
                    <div class="text-center mt-2">
                        <p class="mb-0">Don't have an account? 
                            <a href="../Register/viewRegister.php" class="text-decoration-none text-maroon">Register here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once '../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="validateLogin.js"></script>
</body>
</html>