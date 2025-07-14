<?php
require_once '../Database/db_connect.php';
require_once '../Auth/admin_session_check.php'; // Ensure this checks for admin privileges


// Fetch all users from database
$sql = "SELECT * FROM user ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
        }
        .action-btns {
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2 class="mb-4">User Management</h2>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= $_SESSION['message_type'] ?>">
                <?= $_SESSION['message'] ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($users) > 0): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($user['profile_photo_path'])): ?>
                                        <img src="../uploads/profiles/<?= htmlspecialchars($user['profile_photo_path']) ?>" 
                                             alt="Profile" class="profile-img" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;" >
                                    <?php else: ?>
                                        <div class="profile-img bg-secondary text-white d-flex align-items-center justify-content-center">
                                            <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($user['name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['department']) ?></td>
                                <td><?= htmlspecialchars($user['role']) ?></td>
                                <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                                <td class="action-btns">
                
                                    <form action="executeDeleteUser.php" method="POST" class="d-inline">
                                        <input type="hidden" name="user_cpf" value="<?= $user['cpf_no'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Are you sure you want to delete this user?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No users found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>