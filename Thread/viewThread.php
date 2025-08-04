<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/header.php';
require_once 'executeViewThread.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Thread</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../includes/styles.css">
</head>
<body>
    <?php require_once '../includes/header.php'; ?>
    
    <div class="container my-5">
        <!-- Thread Content -->
        <div class="thread-card card shadow-lg mb-4">
            <div class="card-body thread-card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h1 class="text-maroon mb-0"><?php echo htmlspecialchars($thread['title']); ?></h1>
                    
                    <?php if (isset($_SESSION['user_cpf']) && 
                             ($thread['cpf_no'] == $_SESSION['user_cpf'] || $_SESSION['user_role'] == '1')): ?>
                        <div class="dropdown">
                            <button class="btn btn-maroon-secondary btn-sm dropdown-toggle" type="button" 
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
                    <div class="profile-photo-wrapper me-3">
                        <?php if ($thread['profile_photo_path']): ?>
                            <img src="../Uploads/profiles/<?php echo htmlspecialchars($thread['profile_photo_path']); ?>" 
                                 class="profile-img" alt="Profile" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                        <?php else: ?>
                            <img src="../Uploads/profiles/default_user.png" 
                                 class="profile-img" alt="Profile" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                        <?php endif; ?>
                        <div class="profile-tooltip">
                            <?php if ($thread['user_name']): ?>
                                <h6 class="text-maroon mb-1"><?php echo htmlspecialchars($thread['user_name']); ?></h6>
                            <?php else: ?>
                                <h6 class="text-maroon mb-1">Unknown User</h6>
                            <?php endif; ?>
                            <?php if ($thread['designation']): ?>
                                <p class="text-muted small mb-1">
                                    <i class="fas fa-id-badge text-maroon me-1"></i>
                                    <?php echo htmlspecialchars($thread['designation']); ?>
                                </p>
                            <?php endif; ?>
                            <?php if ($thread['department']): ?>
                                <p class="text-muted small mb-1">
                                    <i class="fas fa-building text-maroon me-1"></i>
                                    <?php echo htmlspecialchars($thread['department']); ?>
                                </p>
                            <?php endif; ?>
                            <?php if ($thread['bio']): ?>
                                <p class="text-muted small mb-1">
                                    <i class="fas fa-pen text-maroon me-1"></i>
                                    <?php echo htmlspecialchars($thread['bio']); ?>
                                </p>
                            <?php endif; ?>
                            <?php if ($thread['role'] == '1'): ?>
                                <span class="user-badge admin">Admin</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="thread-meta">
                            <span class="me-3">
                                <i class="fas fa-user text-maroon"></i>
                                <?php if ($thread['user_name']): ?>
                                    <?php echo htmlspecialchars($thread['user_name']); ?>
                                <?php else: ?>
                                    Unknown User
                                <?php endif; ?>
                                <?php if ($thread['role'] == '1'): ?>
                                    <span class="user-badge admin">Admin</span>
                                <?php endif; ?>
                            </span>
                            <span>
                                <i class="fas fa-clock text-maroon"></i>
                                <?php echo date('M j, Y g:i A', strtotime($thread['created_at'])); ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="thread-content mb-3">
                    <?php echo nl2br(htmlspecialchars($thread['content'])); ?>
                </div>
                
                <?php if (!empty($thread_images)): ?>
                    <div class="image-gallery">
                        <?php foreach ($thread_images as $image): ?>
                            <img src="../Uploads/threads/<?php echo htmlspecialchars($image['image_path']); ?>" 
                                 alt="Thread image" class="img-thumbnail image-preview" 
                                 style="width: 50px; height: 50px; object-fit: cover; cursor: pointer;" 
                                 data-bs-toggle="modal" data-bs-target="#imageModal" 
                                 data-image="../Uploads/threads/<?php echo htmlspecialchars($image['image_path']); ?>">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Replies Section -->
        <div class="mt-4">
            <h3 class="text-maroon">
                <i class="fas fa-comments text-maroon me-2"></i> 
                Replies (<?php echo count($replies); ?>)
            </h3>
            
            <?php if (!empty($replies)): ?>
                <?php foreach ($replies as $reply): ?>
                    <div class="reply-card card shadow-lg mb-3" id="reply-<?php echo $reply['reply_id']; ?>">
                        <div class="card-body reply-card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="d-flex align-items-start flex-grow-1">
                                    <div class="profile-photo-wrapper me-3">
                                        <?php if ($reply['profile_photo_path']): ?>
                                            <img src="../Uploads/profiles/<?php echo htmlspecialchars($reply['profile_photo_path']); ?>" 
                                                 class="profile-img" alt="Profile" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                                        <?php else: ?>
                                            <img src="../Uploads/profiles/default_user.png" 
                                                 class="profile-img" alt="Profile" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                                        <?php endif; ?>
                                        <div class="profile-tooltip">
                                            <?php if ($reply['user_name']): ?>
                                                <h6 class="text-maroon mb-1"><?php echo htmlspecialchars($reply['user_name']); ?></h6>
                                            <?php else: ?>
                                                <h6 class="text-maroon mb-1">Unknown User</h6>
                                            <?php endif; ?>
                                            <?php if ($reply['designation']): ?>
                                                <p class="text-muted small mb-1">
                                                    <i class="fas fa-id-badge text-maroon me-1"></i>
                                                    <?php echo htmlspecialchars($reply['designation']); ?>
                                                </p>
                                            <?php endif; ?>
                                            <?php if ($reply['department']): ?>
                                                <p class="text-muted small mb-1">
                                                    <i class="fas fa-building text-maroon me-1"></i>
                                                    <?php echo htmlspecialchars($reply['department']); ?>
                                                </p>
                                            <?php endif; ?>
                                            <?php if ($reply['bio']): ?>
                                                <p class="text-muted small mb-1">
                                                    <i class="fas fa-pen text-maroon me-1"></i>
                                                    <?php echo htmlspecialchars($reply['bio']); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="reply-content">
                                            <div class="thread-meta mb-2">
                                                <span class="me-3">
                                                    <i class="fas fa-user text-maroon"></i>
                                                    <?php if ($reply['user_name']): ?>
                                                        <?php echo htmlspecialchars($reply['user_name']); ?>
                                                    <?php else: ?>
                                                        Unknown User
                                                    <?php endif; ?>
                                                    <?php if ($reply['role'] == '1'): ?>
                                                        <span class="user-badge admin">Admin</span>
                                                    <?php endif; ?>
                                                </span>
                                                <span>
                                                    <i class="fas fa-clock text-maroon"></i>
                                                    <?php echo date('M j, Y g:i A', strtotime($reply['created_at'])); ?>
                                                </span>
                                            </div>
                                            <div class="mb-2">
                                                <?php echo nl2br(htmlspecialchars($reply['content'])); ?>
                                            </div>
                                            
                                            <?php
                                            $reply_img_sql = "SELECT image_path FROM reply_images WHERE reply_id = ?";
                                            $reply_img_stmt = $pdo->prepare($reply_img_sql);
                                            $reply_img_stmt->execute([$reply['reply_id']]);
                                            $reply_images = $reply_img_stmt->fetchAll();
                                            
                                            if (!empty($reply_images)): ?>
                                                <div class="image-gallery">
                                                    <?php foreach ($reply_images as $image): ?>
                                                        <img src="../Uploads/replies/<?php echo htmlspecialchars($image['image_path']); ?>" 
                                                             alt="Reply image" class="img-thumbnail image-preview" 
                                                             style="width: 50px; height: 50px; object-fit: cover; cursor: pointer;" 
                                                             data-bs-toggle="modal" data-bs-target="#imageModal" 
                                                             data-image="../Uploads/replies/<?php echo htmlspecialchars($image['image_path']); ?>">
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php if (isset($_SESSION['user_cpf']) && 
                                         ($reply['cpf_no'] == $_SESSION['user_cpf'] || $_SESSION['user_role'] == '1')): ?>
                                    <div class="dropdown">
                                        <button class="btn btn-maroon-secondary btn-sm dropdown-toggle" type="button" 
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
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-maroon">No replies yet. Be the first to reply!</p>
            <?php endif; ?>
        </div>
        
        <!-- Reply Form -->
        <?php if (isset($_SESSION['user_cpf'])): ?>
            <div class="mt-4">
                <form method="POST" enctype="multipart/form-data" class="reply-form card shadow-lg" action="executeViewThread.php">
                    <div class="card-body">
                        <input type="hidden" name="thread_id" value="<?= $thread['thread_id'] ?>">
                        <div class="mb-3">
                            <label for="reply_content" class="form-label text-maroon">Your Reply</label>
                            <textarea class="form-control" id="reply_content" name="reply_content" 
                                      rows="4" placeholder="Share your thoughts..." 
                                      required minlength="5" maxlength="2000"></textarea>
                            <div class="form-text">Minimum 5 characters, maximum 2000 characters</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="reply_images" class="form-label text-maroon">Attach Images (Optional)</label>
                            <input type="file" class="form-control" id="reply_images" name="reply_images[]" 
                                   accept="image/*" multiple>
                            <div class="form-text">Up to 3 images, max 5MB each</div>
                            <div id="replyImagePreview" class="file-preview"></div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="../index.php" class="btn btn-maroon-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Back to Forum
                            </a>
                            <button type="submit" class="btn btn-maroon">
                                <i class="fas fa-reply me-2"></i> Post Reply
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="mt-4 text-center">
                <div class="alert alert-maroon">
                    <i class="fas fa-info-circle text-maroon me-2"></i>
                    <a href="../Login-Logout/viewLogin.php" class="alert-link text-maroon">Login</a> to post a reply to this thread.
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-body ">
            <div class="modal-content modal-body ">
                <div class="modal-header">
                    <h5 class="modal-title text-maroon" id="imageModalLabel">Image Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" id="modalImage" class="img-fluid" alt="Preview" style="max-height: 70vh; object-fit: contain;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-maroon" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php require_once '../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript to set the modal image source dynamically
        document.addEventListener('DOMContentLoaded', function () {
            const imagePreviews = document.querySelectorAll('.image-preview');
            const modalImage = document.getElementById('modalImage');

            imagePreviews.forEach(image => {
                image.addEventListener('click', function () {
                    const imageSrc = this.getAttribute('data-image');
                    modalImage.setAttribute('src', imageSrc);
                });
            });
        });
    </script>
    <script src="validateViewThread.js"></script>
</body>
</html>