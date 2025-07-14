<?php
session_start();
if (isset($_SESSION['user_cpf'])) {
    header('Location: ../index.php');
    exit;
}
?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="form-container">
                <h2><i class="fas fa-key"></i> Reset Password</h2>
                
                <?php if (isset($_SESSION['forgot_message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['message_type'] ?? 'info'; ?>">
                        <?php echo htmlspecialchars($_SESSION['forgot_message']); 
                        unset($_SESSION['forgot_message']);
                        unset($_SESSION['message_type']); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="executeForgotPassword.php">
                    <div class="mb-3">
                        <label for="cpf_no" class="form-label">CPF Number</label>
                        <input type="text" class="form-control" id="cpf_no" name="cpf_no" 
                               placeholder="12345" maxlength="5" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-paper-plane"></i> Send OTP
                    </button>
                </form>
                
                <div class="text-center mt-3">
                    <a href="../Login-Logout/viewLogin.php" class="text-decoration-none">
                        <i class="fas fa-arrow-left"></i> Back to Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>