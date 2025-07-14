<?php
session_start();
// require_once 'includes/header.php';
require_once '../Database/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_cpf'])) {
    $_SESSION['message'] = 'Please login to view your profile.';
    $_SESSION['message_type'] = 'warning';
    header('Location: ../Login-Logout/viewLogin.php');
    exit;
}

$user_cpf = $_SESSION['user_cpf'];

// Get user details
$sql = "SELECT * FROM user WHERE cpf_no = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_cpf]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['message'] = 'User not found.';
    $_SESSION['message_type'] = 'danger';
    header('Location: ../index.php');
    exit;
}

// Get user's threads and replies count
$threads_sql = "SELECT COUNT(*) FROM threads WHERE cpf_no = ?";
$threads_stmt = $pdo->prepare($threads_sql);
$threads_stmt->execute([$user_cpf]);
$threads_count = $threads_stmt->fetchColumn();

$replies_sql = "SELECT COUNT(*) FROM replies WHERE cpf_no = ?";
$replies_stmt = $pdo->prepare($replies_sql);
$replies_stmt->execute([$user_cpf]);
$replies_count = $replies_stmt->fetchColumn();

// Get user's recent threads
$recent_threads_sql = "SELECT thread_id, question, created_at FROM threads WHERE cpf_no = ? ORDER BY created_at DESC LIMIT 5";
$recent_threads_stmt = $pdo->prepare($recent_threads_sql);
$recent_threads_stmt->execute([$user_cpf]);
$recent_threads = $recent_threads_stmt->fetchAll();
?>

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <!-- Profile Card -->
            <div class="card">
                <div class="card-body text-center">
                    <?php if ($user['profile_photo_path']): ?>
                        <img src="../uploads/profiles/<?= htmlspecialchars($user['profile_photo_path']) ?>" 
                             class="profile-img-large mb-3" alt="Profile" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                    <?php else: ?>
                        <div class="profile-img-large bg-primary text-white d-flex align-items-center justify-content-center mb-3 mx-auto">
                            <span style="font-size: 2rem;">
                                <?= strtoupper(substr($user['name'], 0, 1)) ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    
                    <h4><?= htmlspecialchars($user['name']) ?></h4>
                    <?php if ($user['role'] == '1'): ?>
                        <span class="user-badge admin mb-2">Admin</span>
                    <?php endif; ?>
                    
                    <?php if ($user['designation']): ?>
                        <p class="text-muted mb-1">
                            <i class="fas fa-id-badge"></i>
                            <?= htmlspecialchars($user['designation']) ?>
                        </p>
                    <?php endif; ?>
                    
                    <p class="text-muted mb-1">
                        <i class="fas fa-building"></i>
                        <?= htmlspecialchars($user['department']) ?>
                    </p>
                    
                    <p class="text-muted mb-3">
                        <i class="fas fa-calendar"></i>
                        Member since <?= date('M Y', strtotime($user['created_at'])) ?>
                    </p>
                    
                    <?php if ($user['bio']): ?>
                        <div class="text-start">
                            <h6>Bio</h6>
                            <p class="text-muted"><?= nl2br(htmlspecialchars($user['bio'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Stats Card -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar"></i> Activity Stats
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h4 text-primary"><?= $threads_count ?></div>
                            <small class="text-muted">Threads</small>
                        </div>
                        <div class="col-6">
                            <div class="h4 text-success"><?= $replies_count ?></div>
                            <small class="text-muted">Replies</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <!-- Profile Update Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-edit"></i> Edit Profile
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?= $_SESSION['message_type'] ?>">
                            <?= $_SESSION['message'] ?>
                        </div>
                        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                    <?php endif; ?>
                    
                    <form method="POST" action="executeProfileUpdate.php" enctype="multipart/form-data">
                        <input type="hidden" name="form_type" value="profile_update">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           required value="<?= htmlspecialchars($user['name']) ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           required value="<?= htmlspecialchars($user['email']) ?>">
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
                                            // Trim and strict comparison
                                            $isSelected = (trim($user['department']) === trim($dept)) ? 'selected' : '';
                                            echo "<option value=\"" . htmlspecialchars($dept) . "\" $isSelected>" 
                                             . htmlspecialchars($dept) . "</option>";
}
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="designation" class="form-label">Designation</label>
                                    <input type="text" class="form-control" id="designation" name="designation" 
                                           value="<?= htmlspecialchars($user['designation']) ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="bio" class="form-label">Bio</label>
                            <textarea class="form-control" id="bio" name="bio" rows="3"><?= htmlspecialchars($user['bio']) ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="profile_image" class="form-label">Profile Image</label>
                            <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
                            <div class="form-text">Leave empty to keep current image. Max 5MB.</div>
                            <div id="imagePreview" class="file-preview"></div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Password Change Form -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-lock"></i> Change Password
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="executeProfileUpdate.php">
                        <input type="hidden" name="form_type" value="password_change">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current_password" 
                                   name="current_password" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="new_password" 
                                           name="new_password" required minlength="6">
                                    <div class="form-text">Minimum 6 characters</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirm_password" 
                                           name="confirm_password" required minlength="6">
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key"></i> Change Password
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Recent Threads -->
            <?php if (!empty($recent_threads)): ?>
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-comments"></i> Your Recent Threads
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($recent_threads as $thread): ?>
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <div>
                                    <a href="../Thread/viewThread.php?id=<?= $thread['thread_id'] ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($thread['question']) ?>
                                    </a>
                                </div>
                                <small class="text-muted">
                                    <?= date('M j, Y', strtotime($thread['created_at'])) ?>
                                </small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="validateProfileUpdate.js"></script>
<!-- <?php require_once 'includes/footer.php'; ?> -->