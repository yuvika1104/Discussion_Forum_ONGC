<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../Database/db_connect.php';

// Fetch all threads with user information
$sql = "SELECT t.*, u.name as user_name, u.department, u.bio,u.profile_photo_path, u.role, u.designation
        FROM threads t 
        LEFT JOIN user u ON t.cpf_no = u.cpf_no
        where t.active=1
        ORDER BY t.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$threads = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum Threads</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../includes/styles.css">
</head>
<body>
    <?php require_once '../includes/header.php'; ?>
    
    <div class="container my-5">
        <!-- Search Bar and Create New Thread Button -->
        <div class="mb-4">
            <div class="input-group">
                <span class="input-group-text  border-maroon" style="background: linear-gradient(135deg, #ac4c4cff 0%, #b55e5eff 100%);">
                    <i class="fas fa-search text-black"></i>
                </span>
                <input type="text" id="threadSearch" class="form-control border-maroon" 
                       placeholder="Search threads..." 
                       style="background-color: #FDF5E6;">
                <a href="viewCreateThread.php" class="btn text-cream border-maroon" style="background: linear-gradient(135deg, #ac4c4cff 0%, #b55e5eff 100%);">
                    <i class="fas fa-plus me-1"></i> Create New
                </a>
            </div>
        </div>

        <?php if (count($threads) > 0): ?>
            <div class="threads-list">
                <?php foreach ($threads as $thread): ?>
                    <a href="../Thread/viewThread.php?id=<?= $thread['thread_id'] ?>" class="thread-link text-decoration-none"
                    data-title="<?= htmlspecialchars($thread['title']) ?>" 
                       data-username="<?= htmlspecialchars($thread['user_name'] ?? 'Unknown User') ?>">

                        <div class="thread card shadow-lg bg-cream mb-3">
                            <div class="card-body">
                                <div class="thread-header d-flex align-items-center">
                                    
                                        <div class="profile-photo-wrapper">
                                            <?php if ($thread['profile_photo_path']): ?>
                                                <img src="../Uploads/profiles/<?= htmlspecialchars($thread['profile_photo_path']) ?>" 
                                                 alt="Profile Photo" 
                                                 class="profile-photo"
                                                 style="width: 70px; height: 70px; object-fit: cover; border-radius: 50%;">
                                                <?php else:?>
                                                    <img src="../Uploads/profiles/default_user.png" 
                                                 alt="Profile Photo" 
                                                 class="profile-photo"
                                                 style="width: 70px; height: 70px; object-fit: cover; border-radius: 50%;">
                                                <?php endif; ?>
    
                                            <div class="profile-tooltip">
                                                <?php if ($thread['user_name']): ?>
                                                    <h6 class="text-maroon mb-1"><?= htmlspecialchars($thread['user_name']) ?></h6>
                                                <?php else:?>
                                                    <h6 class="text-maroon mb-1">Unkown User</h6>
                                                <?php endif; ?>
                                                <?php if ($thread['designation']): ?>
                                                    <p class="text-muted small mb-1">
                                                        <i class="fas fa-id-badge text-maroon me-1"></i>
                                                        <?= htmlspecialchars($thread['designation']) ?>
                                                    </p>
                                                <?php endif; ?>
                                                <?php if($thread['department']): ?>
                                                <p class="text-muted small mb-1">
                                                    <i class="fas fa-building text-maroon me-1"></i>
                                                    <?= htmlspecialchars($thread['department']) ?>
                                                </p>
                                                <?php endif; ?>
                                                <?php if ($thread['bio']): ?>
                                                    <p class="text-muted small mb-1">
                                                        <i class="fas fa-pen text-maroon me-1"></i>
                                                        <?= htmlspecialchars($thread['bio']) ?>
                                                    </p>
                                                <?php endif; ?>
                                                
                                            </div>
                                        </div>

                                    <div class="user-info ms-3 flex-grow-1">
                                        <?php if ($thread['user_name']): ?>
                                                    <span class="user-name text-maroon"><?= htmlspecialchars($thread['user_name']) ?></span>
                                                <?php else:?>
                                                    <span class="user-name text-maroon">Unkown User</span>
                                                <?php endif; ?>
                                        
                                        <?php if ($thread['role'] == '1'): ?>
                                                    <span class="user-badge admin" style="width: 63px">Admin</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="thread-date text-muted small"><?= date('M j, Y g:i a', strtotime($thread['created_at'])) ?></div>
                                </div>
                                <div class="thread-content mt-3">
                                    <p class="text-maroon"><?= htmlspecialchars($thread['title']) ?></p>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-threads card shadow-lg bg-cream text-center p-4">
                <p class="text-maroon mb-0">No threads found.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php require_once '../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="validateViewAllThread.js"></script>
</body>
</html>