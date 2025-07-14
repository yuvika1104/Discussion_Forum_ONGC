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
                <h2><i class="fas fa-sign-in-alt text-primary"></i> Login to Forum</h2>
                
                <form method="POST" action="executeLogin.php">
                    <div class="mb-3">
                        <label for="cpf_no" class="form-label">CPF Number</label>
                        <input type="text" class="form-control" id="cpf_no" name="cpf_no" 
                               placeholder="12345" maxlength="5" 
                               value="<?php echo isset($_POST['cpf_no']) ? htmlspecialchars($_POST['cpf_no']) : ''; ?>" required 
                               >
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    
                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                    
                </form>
                <div class="text-center">
                        <a href="../ForgotPassword/viewForgotPassword.php" class="text-decoration-none">Forgot Password?</a>
                </div>
                <div class="text-center">
                    <p class="mb-0">Don't have an account? 
                        <a href="../Register/viewRegister.php" class="text-decoration-none">Register here</a>
                    </p>
                </div>
                
                <hr>
                
            </div>
        </div>
    </div>
</div>

<script src="validateLogin.js"></script>


<!-- <?php require_once 'includes/footer.php'; ?> -->
