<?php
require_once 'executeViewThread.php';
// echo $thread['profile_photo_path'];
?>

<div class="container">
    <!-- Thread Content -->
    <div class="thread-card">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <h1 class="mb-0"><?php echo htmlspecialchars($thread['question']); ?></h1>
            
            <?php if (isset($_SESSION['user_cpf']) && 
                     ($thread['cpf_no'] == $_SESSION['user_cpf'] || $_SESSION['user_role'] == '1')): ?>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" 
                            data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <form method="POST" class="d-inline" action="executeViewThread.php"
                                  onsubmit="return confirm('Are you sure you want to delete this thread?')">
                                <input type="hidden" name="item_type" value="thread">
                                <input type="hidden" name="item_id" value="<?php echo $thread['thread_id']; ?>">
                                <button type="submit" name="delete_item" class="dropdown-item text-danger">
                                    <i class="fas fa-trash"></i> Delete Thread
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="d-flex align-items-start mb-3">
            <div class="me-3">
                <?php if ($thread['profile_photo_path']): ?>
                    <img src="../uploads/profiles/<?php echo htmlspecialchars($thread['profile_photo_path']); ?>" 
                         class="profile-img" alt="Profile" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                <?php else: ?>
                    <div class="profile-img bg-primary text-white d-flex align-items-center justify-content-center">
                        <?php echo strtoupper(substr($thread['user_name'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="flex-grow-1">
                <div class="thread-meta">
                    <span class="me-3">
                        <i class="fas fa-user"></i>
                        <?php echo htmlspecialchars($thread['user_name']); ?>
                        <?php if ($thread['role'] == '1'): ?>
                            <span class="user-badge admin">Admin</span>
                        <?php endif; ?>
                    </span>
                    <?php if ($thread['designation']): ?>
                        <span class="me-3">
                            <i class="fas fa-id-badge"></i>
                            <?php echo htmlspecialchars($thread['designation']); ?>
                        </span>
                    <?php endif; ?>
                    <span class="me-3">
                        <i class="fas fa-building"></i>
                        <?php echo htmlspecialchars($thread['department']); ?>
                    </span>
                    <span>
                        <i class="fas fa-clock"></i>
                        <?php echo date('M j, Y g:i A', strtotime($thread['created_at'])); ?>
                    </span>
                </div>
            </div>
        </div>
        
        <div class="thread-content mb-3">
            <?php echo nl2br(htmlspecialchars($thread['question'])); ?>
        </div>
        
        <?php if (!empty($thread_images)): ?>
            <div class="image-gallery">
                <?php foreach ($thread_images as $image): ?>
                    <img src="../uploads/threads/<?php echo htmlspecialchars($image['image_path']); ?>" 
                         alt="Thread image" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Replies Section -->
    <div class="mt-4">
        <h3>
            <i class="fas fa-comments"></i> 
            Replies (<?php echo count($replies); ?>)
        </h3>
        
        <?php if (!empty($replies)): ?>
            <?php foreach ($replies as $reply): ?>
                <div class="reply-card" id="reply-<?php echo $reply['reply_id']; ?>">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="d-flex align-items-start flex-grow-1">
                            <div class="me-3">
                                <?php if ($reply['profile_photo_path']): ?>
                                    <img src="../uploads/profiles/<?php echo htmlspecialchars($reply['profile_photo_path']); ?>" 
                                         class="profile-img" alt="Profile" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                                <?php else: ?>
                                    <div class="profile-img bg-secondary text-white d-flex align-items-center justify-content-center">
                                        <?php echo strtoupper(substr($reply['user_name'], 0, 1)); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-grow-1">
                                <div class="reply-content">
                                    <div class="thread-meta mb-2">
                                        <span class="me-3">
                                            <i class="fas fa-user"></i>
                                            <?php echo htmlspecialchars($reply['user_name']); ?>
                                            <?php if ($reply['role'] == '1'): ?>
                                                <span class="user-badge admin">Admin</span>
                                            <?php endif; ?>
                                        </span>
                                        <?php if ($reply['designation']): ?>
                                            <span class="me-3">
                                                <i class="fas fa-id-badge"></i>
                                                <?php echo htmlspecialchars($reply['designation']); ?>
                                            </span>
                                        <?php endif; ?>
                                        <span class="me-3">
                                            <i class="fas fa-building"></i>
                                            <?php echo htmlspecialchars($reply['department']); ?>
                                        </span>
                                        <span>
                                            <i class="fas fa-clock"></i>
                                            <?php echo date('M j, Y g:i A', strtotime($reply['created_at'])); ?>
                                        </span>
                                    </div>
                                    <div class="mb-2">
                                        <?php echo nl2br(htmlspecialchars($reply['content'])); ?>
                                    </div>
                                    
                                    <?php
                                    // Get reply images
                                    $reply_img_sql = "SELECT image_path FROM reply_images WHERE reply_id = ?";
                                    $reply_img_stmt = $pdo->prepare($reply_img_sql);
                                    $reply_img_stmt->execute([$reply['reply_id']]);
                                    $reply_images = $reply_img_stmt->fetchAll();
                                    
                                    if (!empty($reply_images)): ?>
                                        <div class="image-gallery">
                                            <?php foreach ($reply_images as $image): ?>
                                                <img src="../uploads/replies/<?php echo htmlspecialchars($image['image_path']); ?>" 
                                                     alt="Reply image" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <?php if (isset($_SESSION['user_cpf']) && 
                                 ($reply['cpf_no'] == $_SESSION['user_cpf'] || $_SESSION['user_role'] == '1')): ?>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" 
                                        data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <form method="POST" class="d-inline" action="executeViewThread.php"
                                              onsubmit="return confirm('Are you sure you want to delete this reply?')">
                                            <input type="hidden" name="item_type" value="reply">
                                            <input type="hidden" name="item_id" value="<?php echo $reply['reply_id']; ?>">
                                            <button type="submit" name="delete_item" class="dropdown-item text-danger">
                                                <i class="fas fa-trash"></i> Delete Reply
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">No replies yet. Be the first to reply!</p>
        <?php endif; ?>
    </div>
    
    <!-- Reply Form -->
    <?php if (isset($_SESSION['user_cpf'])): ?>
        <div class="mt-4">
            <h4><i class="fas fa-reply"></i> Post a Reply</h4>
            <form method="POST" enctype="multipart/form-data" class="reply-form" action="executeViewThread.php">
                <input type="hidden" name="thread_id" value="<?= $thread['thread_id'] ?>">
                <div class="mb-3">
                    <label for="reply_content" class="form-label">Your Reply</label>
                    <textarea class="form-control" id="reply_content" name="reply_content" 
                              rows="4" placeholder="Share your thoughts..." 
                              required minlength="5" maxlength="2000"></textarea>
                    <div class="form-text">Minimum 5 characters, maximum 2000 characters</div>
                </div>
                
                <div class="mb-3">
                    <label for="reply_images" class="form-label">Attach Images (Optional)</label>
                    <input type="file" class="form-control" id="reply_images" name="reply_images[]" 
                           accept="image/*" multiple>
                    <div class="form-text">Up to 3 images, max 5MB each</div>
                    <div id="replyImagePreview" class="file-preview"></div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="../index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Forum
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-reply"></i> Post Reply
                    </button>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div class="mt-4 text-center">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <a href="../Login-Logout/viewLogin.php" class="alert-link">Login</a> to post a reply to this thread.
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.highlight {
    background-color: #fff3cd !important;
    transition: background-color 3s ease;
}
</style>
<script src="validateViewThread.js"></script>

<!-- <?php require_once 'includes/footer.php'; ?> -->
