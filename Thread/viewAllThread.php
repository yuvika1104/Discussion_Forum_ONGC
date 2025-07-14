<?php
session_start();
require_once '../Database/db_connect.php';
// Fetch all threads with user information
$sql = "SELECT t.*, u.name as user_name, u.department, u.profile_photo_path, u.role, u.designation
        FROM threads t 
        LEFT JOIN user u ON t.cpf_no = u.cpf_no
        ORDER BY t.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$threads = $stmt->fetchAll();

if (count($threads) > 0): ?>
    <div class="threads-list">
        <?php foreach ($threads as $thread): ?>
            <a href="viewThread.php?id=<?= $thread['thread_id'] ?>" class="thread-link">
                <div class="thread">
                    <div class="thread-header">
                        <?php if (!empty($thread['profile_photo_path'])): ?>
                            <img src="../uploads/profiles/<?= htmlspecialchars($thread['profile_photo_path']) ?>" 
                                 alt="Profile Photo" 
                                 class="profile-photo"
                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                        <?php endif; ?>
                        <div class="user-info">
                            <span class="user-name"><?= htmlspecialchars($thread['user_name']) ?></span>
                            <span class="designation"><?= htmlspecialchars($thread['designation']) ?></span>
                            <span class="department"><?= htmlspecialchars($thread['department']) ?></span>
                        </div>
                        <div class="thread-date"><?= date('M j, Y g:i a', strtotime($thread['created_at'])) ?></div>
                    </div>
                    <div class="thread-content">
                        <p><?= htmlspecialchars($thread['question']) ?></p>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="no-threads">
        <p>No threads found.</p>
    </div>
<?php endif; ?>