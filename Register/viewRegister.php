<?php
session_start();
// require_once 'includes/header.php';
require_once '../Database/db_connect.php';

// Redirect if already logged in
if (isset($_SESSION['user_cpf'])) {
    header('Location: ../index.php');
    exit;
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="form-container">
                <h2><i class="fas fa-user-plus text-primary"></i> Register for Forum</h2>
                
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?= $_SESSION['message_type'] ?>">
                        <?= $_SESSION['message'] ?>
                    </div>
                    <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                <?php endif; ?>
                
                <form method="POST" action="executeRegister.php">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cpf_no" class="form-label">CPF Number *</label>
                                <input type="text" class="form-control" id="cpf_no" name="cpf_no" 
                                       placeholder="12345" maxlength="5" required 
                                       value="<?= htmlspecialchars($_POST['cpf_no'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="name" name="name" required
                                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email" required
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone_number" name="phone_number"
                                       value="<?= htmlspecialchars($_POST['phone_number'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="department" class="form-label">Department *</label>
                                <select class="form-select" id="department" name="department" required>
                                    <option value="">Select Department</option>
                                    <?php
                                    $departments = [
                                        'IT Department', 'Human Resources', 'Finance', 
                                        'Marketing', 'Sales', 'Operations', 
                                        'Customer Service', 'Research & Development'
                                    ];
                                    foreach ($departments as $dept) {
                                        $selected = ($_POST['department'] ?? '') === $dept ? 'selected' : '';
                                        echo "<option value=\"$dept\" $selected>$dept</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="designation" class="form-label">Designation</label>
                                <input type="text" class="form-control" id="designation" name="designation" 
                                       placeholder="e.g., Software Developer, Manager, etc."
                                       value="<?= htmlspecialchars($_POST['designation'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password *</label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       required minlength="6">
                                <div class="form-text">Minimum 6 characters</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password *</label>
                                <input type="password" class="form-control" id="confirm_password" 
                                       name="confirm_password" required minlength="6">
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="fas fa-user-plus"></i> Register
                    </button>
                </form>
                
                <div class="text-center">
                    <p class="mb-0">Already have an account? 
                        <a href="../Login-Logout/viewLogin.php" class="text-decoration-none">Login here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="validateRegister.js"></script>
<!-- <?php require_once 'includes/footer.php'; ?> -->