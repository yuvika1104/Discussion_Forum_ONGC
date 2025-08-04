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
// Note: echo $_SESSION['reset_otp']; should probably be removed in a production environment
// as it displays the OTP directly on the page, which is a security risk.
// echo $_SESSION['reset_otp']; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../includes/styles.css">


</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5"> <div class="form-container">
                <h2 class="text-maroon mb-4">
                    <i class="fas fa-shield-alt me-2"></i> Verify OTP
                </h2>
                
                <?php if (isset($_SESSION['otp_message']) &&  isset($_SESSION['otp_message_type']) && $_SESSION['otp_message_type'] == 'danger'): ?>
                    <div class="alert alert-danger alert-maroon fade show" role="alert">
                        <?php echo htmlspecialchars($_SESSION['otp_message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php 
                    // Unset messages after displaying
                    unset($_SESSION['otp_message']);
                    unset($_SESSION['otp_message_type']); 
                    ?>
                <?php endif; ?>
                
                <p class="text-muted mb-4 text-center"> We sent a 6-digit code to <strong><?= isset($_SESSION['reset_email']) ? htmlspecialchars($_SESSION['reset_email']) : 'your email' ?></strong>
                </p>
                
                <form method="POST" action="executeVerifyOTP.php">
                    <div class="mb-4"> <label for="otp" class="form-label">Enter 6-digit OTP</label>
                        <input type="text" class="form-control text-center" id="otp" name="otp" 
                               maxlength="6" pattern="\d{6}" required
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                               placeholder="e.g., 123456"> </div>
                    
                    <div class="d-grid gap-2 mb-4"> <button type="submit" class="btn btn-maroon btn-lg">
                            <i class="fas fa-check me-2"></i> Verify OTP
                        </button>
                    </div>

                    <hr class="my-4 border-maroon"> <div class="text-center">
                        <a href="executeResendOTP.php" class="text-maroon text-decoration-none fw-bold"> <i class="fas fa-sync-alt me-2"></i> Resend OTP
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>