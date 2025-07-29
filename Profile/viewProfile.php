<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/header.php';
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
$threads_sql = "SELECT COUNT(*) FROM threads WHERE cpf_no = ? and active=1";
$threads_stmt = $pdo->prepare($threads_sql);
$threads_stmt->execute([$user_cpf]);
$threads_count = $threads_stmt->fetchColumn();

$replies_sql = "SELECT COUNT(*) FROM replies WHERE cpf_no = ? and active=1";
$replies_stmt = $pdo->prepare($replies_sql);
$replies_stmt->execute([$user_cpf]);
$replies_count = $replies_stmt->fetchColumn();

// Get user's recent threads
$recent_threads_sql = "SELECT thread_id, title, created_at FROM threads WHERE cpf_no = ? and active=1 ORDER BY created_at DESC LIMIT 5";
$recent_threads_stmt = $pdo->prepare($recent_threads_sql);
$recent_threads_stmt->execute([$user_cpf]);
$recent_threads = $recent_threads_stmt->fetchAll();

// Get user's recent replies
$recent_replies_sql = "SELECT r.reply_id, r.thread_id, r.content, r.created_at, t.title 
                       FROM replies r 
                       JOIN threads t ON r.thread_id = t.thread_id 
                       WHERE r.cpf_no = ? and r.active=1 and t.active=1
                       ORDER BY r.created_at DESC 
                       LIMIT 5";
$recent_replies_stmt = $pdo->prepare($recent_replies_sql);
$recent_replies_stmt->execute([$user_cpf]);
$recent_replies = $recent_replies_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../includes/styles.css">
</head>
<body>
    <?php require_once '../includes/header.php'; ?>
    
    <div class="container my-5">
        <div class="row">
            <div class="col-md-4">
                <!-- Profile Card -->
                <div class="card shadow-lg bg-cream">
                    <div class="card-body text-center">
                        <?php if ($user['profile_photo_path']): ?>
                            <img src="../Uploads/profiles/<?= htmlspecialchars($user['profile_photo_path']) ?>" 
                                 class="profile-img-large mb-3" alt="Profile">
                        <?php else: ?>
                             <img src="../Uploads/profiles/default_user.png" 
                                 class="profile-img-large mb-3" alt="Profile">
                        <?php endif; ?>
                        
                        <h4 class="text-maroon"><?= htmlspecialchars($user['name']) ?></h4>
                        <?php if ($user['role'] == '1'): ?>
                            <span class="user-badge admin mb-2">Admin</span>
                        <?php endif; ?>
                        
                        <?php if ($user['designation']): ?>
                            <p class="text-muted mb-1">
                                <i class="fas fa-id-badge text-maroon me-1"></i>
                                <?= htmlspecialchars($user['designation']) ?>
                            </p>
                        <?php endif; ?>
                        
                        <p class="text-muted mb-1">
                            <i class="fas fa-building text-maroon me-1"></i>
                            <?= htmlspecialchars($user['department']) ?>
                        </p>
                        
                        <p class="text-muted mb-3">
                            <i class="fas fa-calendar text-maroon me-1"></i>
                            Member since <?= date('M Y', strtotime($user['created_at'])) ?>
                        </p>
                        
                        <?php if ($user['bio']): ?>
                            <div class="text-start">
                                <h6 class="text-maroon">Bio</h6>
                                <p class="text-muted"><?= nl2br(htmlspecialchars($user['bio'])) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Stats Card -->
                <div class="card shadow-lg bg-cream mt-4">
                    <div class="card-header bg-maroon text-cream">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar me-1"></i> Activity Stats
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="h4 text-maroon"><?= $threads_count ?></div>
                                <small class="text-muted">Threads</small>
                            </div>
                            <div class="col-6">
                                <div class="h4 text-maroon"><?= $replies_count ?></div>
                                <small class="text-muted">Replies</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <!-- Profile Update Form -->
                <div class="card shadow-lg bg-cream">
                    <div class="card-header bg-maroon text-cream">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-edit me-1"></i> Edit Profile
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['message'])): ?>
                            <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-maroon">
                                <?= $_SESSION['message'] ?>
                            </div>
                            <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                        <?php endif; ?>
                        
                        <form method="POST" action="executeProfileUpdate.php" enctype="multipart/form-data">
                            <input type="hidden" name="form_type" value="profile_update">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label text-maroon">Full Name *</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               required value="<?= htmlspecialchars($user['name']) ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label text-maroon">Email Address *</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               required value="<?= htmlspecialchars($user['email']) ?>">
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
                                        <label for="designation" class="form-label text-maroon">Designation</label>
                                        <input type="text" class="form-control" id="designation" name="designation" 
                                               value="<?= htmlspecialchars($user['designation']) ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="bio" class="form-label text-maroon">Bio</label>
                                <textarea class="form-control" id="bio" name="bio" rows="3"><?= htmlspecialchars($user['bio']) ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="profile_image" class="form-label text-maroon">Profile Image</label>
                                <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
                                <div class="form-text">Leave empty to keep current image. Max 5MB.</div>
                                <div id="imagePreview" class="file-preview"></div>
                            </div>
                            
                            <button type="submit" class="btn btn-maroon">
                                <i class="fas fa-save me-1"></i> Update Profile
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Password Change Form -->
                <div class="card shadow-lg bg-cream mt-4">
                    <div class="card-header bg-maroon text-cream">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-lock me-1"></i> Change Password
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="executeProfileUpdate.php">
                            <input type="hidden" name="form_type" value="password_change">
                            <div class="mb-3">
                                <label for="current_password" class="form-label text-maroon">Current Password</label>
                                <input type="password" class="form-control" id="current_password" 
                                       name="current_password" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label text-maroon">New Password</label>
                                        <input type="password" class="form-control" id="new_password" 
                                               name="new_password" required minlength="6">
                                        <div class="form-text">Minimum 6 characters</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label text-maroon">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_password" 
                                               name="confirm_password" required minlength="6">
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-maroon">
                                <i class="fas fa-key me-1"></i> Change Password
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Recent Threads -->
                <?php if (!empty($recent_threads)): ?>
                    <div class="card shadow-lg bg-cream mt-4">
                        <div class="card-header bg-maroon text-cream">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-comments me-1"></i> Your Recent Threads
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($recent_threads as $thread): ?>
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <div>
                                        <a href="../Thread/viewThread.php?id=<?= $thread['thread_id'] ?>" class="text-decoration-none text-maroon">
                                            <?= htmlspecialchars($thread['title']) ?>
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

                <!-- Recent Replies -->
                <?php if (!empty($recent_replies)): ?>
                    <div class="card shadow-lg bg-cream mt-4">
                        <div class="card-header bg-maroon text-cream">
                            <h5 class="card-title mb-0">
                            <i class="fas fa-comment-dots me-1"></i> Your Recent Replies
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($recent_replies as $reply): ?>
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <div>
                                        <a href="../Thread/viewThread.php?id=<?= $reply['thread_id'] ?>" class="text-decoration-none text-maroon">
                            <?php
                            // Truncate reply content to 50 characters for brevity
                            $content = htmlspecialchars($reply['content']);
                            $display_content = strlen($content) > 50 ? substr($content, 0, 50) . '...' : $content;
                            ?>
                            <?= $display_content ?> <small>(in "<?= htmlspecialchars($reply['title']) ?>")</small>
                                    </a>
                            </div>
                            <small class="text-muted">
                            <?= date('M j, Y', strtotime($reply['created_at'])) ?>
                            </small>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php require_once '../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="validateProfileUpdate.js"></script>
</body>
</html>