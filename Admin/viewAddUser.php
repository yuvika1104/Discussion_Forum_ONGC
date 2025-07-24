<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../Database/db_connect.php';
require_once '../Auth/admin_session_check.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../includes/styles.css">
</head>
<body>
    <?php require_once '../includes/header.php'; ?>
    
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="form-container shadow-lg bg-cream">
                    <h2 class="text-maroon mb-4"><i class="fas fa-user-plus text-maroon me-2"></i>Register for Forum</h2>
                    
                    <?php if (isset($_SESSION['message']) && isset($_SESSION['message_type']) && $_SESSION['message_type']=='danger'): ?>
                        <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-maroon">
                            <?= $_SESSION['message'] ?>
                        </div>
                        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                    <?php endif; ?>
                    
                    <form method="POST" action="executeAddUser.php">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cpf_no" class="form-label text-maroon">CPF Number *</label>
                                    <input type="text" class="form-control" id="cpf_no" name="cpf_no" 
                                           placeholder="12345" maxlength="5" required 
                                           value="<?= htmlspecialchars($_POST['cpf_no'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label text-maroon">Full Name *</label>
                                    <input type="text" class="form-control" id="name" name="name" required
                                           value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label text-maroon">Email Address *</label>
                                    <input type="email" class="form-control" id="email" name="email" required
                                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone_number" class="form-label text-maroon">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone_number" name="phone_number"
                                           value="<?= htmlspecialchars($_POST['phone_number'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="department" class="form-label text-maroon">Department *</label>
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
                            <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="role" class="form-label text-maroon">Role *</label>
                                    <select class="form-select" id="role" name="role" required>
                                        <option value="">Select Role</option>
                                        <?php
                                        $roles = [
                                            'Admin','User'
                                        ];
                                        foreach ($roles as $role) {
                                            $selected = ($_POST['role'] ?? '') === $role ? 'selected' : '';
                                            echo "<option value=\"$role\" $selected>$role</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="designation" class="form-label text-maroon">Designation</label>
                                    <input type="text" class="form-control" id="designation" name="designation" 
                                           placeholder="e.g., Software Developer, Manager, etc."
                                           value="<?= htmlspecialchars($_POST['designation'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label text-maroon">Password *</label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           required minlength="6">
                                    <div class="form-text">Minimum 6 characters</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label text-maroon">Confirm Password *</label>
                                    <input type="password" class="form-control" id="confirm_password" 
                                           name="confirm_password" required minlength="6">
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-maroon w-100 mb-3">
                            <i class="fas fa-user-plus me-2"></i> Register
                        </button>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>

    <?php require_once '../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="validateRegister.js"></script>
</body>
</html>