<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user_cpf'])) {
    header('Location: ../index.php');
    exit;
}
// Redirect if no CPF/OTP session exists
if (!isset($_SESSION['reset_cpf']) || !isset($_SESSION['reset_otp'])) {
    $_SESSION['forgot_message'] = "Session expired. Please start again.";
    $_SESSION['message_type'] = 'danger';
    header('Location: viewForgotPassword.php');
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../includes/styles.css" rel= "stylesheet">
</head>
<body>
    <div class="container py-5">
        <div class="card shadow-lg">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-lock"></i> Reset Password
                </h5>
            </div>
            <?php if(isset($_SESSION['message'])&& isset($_SESSION['message_type']) && $_SESSION['message_type']=='danger'): ?>
                                <h5 class="text-center mb-4" style="color: maroon"><?php echo $_SESSION['message']; ?></h5>
            <?php endif; ?>
            <div class="card-body p-4">
                <form method="POST" action="executeResetPassword.php" onsubmit="return validateForm()">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3 position-relative">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" 
                                       name="new_password" required minlength="6">
                                <i class="fas fa-eye password-toggle" onclick="togglePassword('new_password', this)"></i>
                                <div class="form-text">Minimum 6 characters</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 position-relative">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" 
                                       name="confirm_password" required minlength="6">
                                <i class="fas fa-eye password-toggle" onclick="togglePassword('confirm_password', this)"></i>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-maroon">
                        <i class="fas fa-key me-1"></i> Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="validateResetPassword.js"></script>
       
</body>
</html>