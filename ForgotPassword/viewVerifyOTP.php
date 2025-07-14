<?php
session_start();
// Redirect if no CPF/OTP session exists
if (!isset($_SESSION['reset_cpf']) || !isset($_SESSION['reset_otp'])) {
    header('Location: viewForgotPassword.php');
    exit;
}
echo $_SESSION['reset_otp'];
?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="form-container">
                <h2><i class="fas fa-shield-alt"></i> Verify OTP</h2>
                
                <?php if (isset($_SESSION['otp_message'])): ?>
                    <!-- <div class="alert alert-<?= $_SESSION['otp_message_type'] ?? 'info' ?>">
                        <?= htmlspecialchars($_SESSION['otp_message']) ?> -->
                        <?php 
                        unset($_SESSION['otp_message']);
                        unset($_SESSION['otp_message_type']); 
                        ?>
                    </div>
                <?php endif; ?>
                
                <p class="text-muted mb-3">
                    We sent a 6-digit code to <?= isset($_SESSION['reset_email']) ? htmlspecialchars($_SESSION['reset_email']) : 'your email' ?>
                </p>
                
                <form method="POST" action="executeVerifyOTP.php">
                    <div class="mb-3">
                        <label for="otp" class="form-label">Enter 6-digit OTP</label>
                        <input type="text" class="form-control" id="otp" name="otp" 
                               maxlength="6" pattern="\d{6}" required
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="fas fa-check"></i> Verify OTP
                    </button>
                    <div class="text-center">
                        <a href="executeResendOTP.php" class="text-decoration-none">
                            <i class="fas fa-sync-alt"></i> Resend OTP
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>