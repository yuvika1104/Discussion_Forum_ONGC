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
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../includes/styles.css">
    
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5"> <div class="form-container">
                <h2 class="text-maroon mb-4">
                    <i class="fas fa-key me-2"></i> Reset Password
                </h2>
                
                <?php if (isset($_SESSION['forgot_message']) && isset($_SESSION['message_type']) && $_SESSION['message_type'] == 'danger' ): ?>
                    <div class="alert alert-danger alert-maroon fade show" role="alert">
                        <?php echo htmlspecialchars($_SESSION['forgot_message']); 
                        unset($_SESSION['forgot_message']);
                        unset($_SESSION['message_type']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['message']) && isset($_SESSION['message_type']) && $_SESSION['message_type'] == 'danger' ): ?>
                    <div class="alert alert-danger alert-maroon fade show" role="alert">
                        <?php echo htmlspecialchars($_SESSION['message']); 
                        unset($_SESSION['message']);
                        unset($_SESSION['message_type']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="executeForgotPassword.php">
                    <div class="mb-4"> <label for="cpf_no" class="form-label">CPF Number</label>
                        <input type="text" class="form-control" id="cpf_no" name="cpf_no" 
                               placeholder="e.g., 12345" maxlength="5" required
                               pattern="[0-9]{5}" title="Please enter a 5-digit CPF number"> </div>
                    
                    <div class="d-grid gap-2 mb-4"> <button type="submit" class="btn btn-maroon btn-lg"> <i class="fas fa-paper-plane me-2"></i> Send OTP
                        </button>
                    </div>
                </form>
                
                <hr class="my-4 border-maroon"> <div class="text-center">
                    <a href="../Login-Logout/viewLogin.php" class="text-maroon text-decoration-none fw-bold"> <i class="fas fa-arrow-left me-2"></i> Back to Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>