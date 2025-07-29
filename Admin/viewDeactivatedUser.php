<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../Database/db_connect.php';
require_once '../Auth/admin_session_check.php'; // Ensure this checks for admin privileges

$sql = "SELECT * FROM user where active=0 ORDER BY created_at DESC";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../includes/styles.css">

</head>
<body>
    <!-- Admin Header -->
    <header class="admin-header">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-cream">
                <i class="fas fa-users-cog me-2"></i> User Management
            </h1>
            <div>
                <a href="../index.php" class="btn btn-outline-light btn-sm me-2 rounded-pill">
                    <i class="fas fa-home me-1"></i> Forum
                </a>
                <a href="viewAddUser.php" class="btn btn-outline-light btn-sm me-2 rounded-pill">
                    <i class="fas fa-user-plus me-1"></i> Add User
                </a>
                <a href="viewAllUsers.php" class="btn btn-outline-light btn-sm me-2 rounded-pill">
                    <i class="fas fa-user-plus me-1"></i> Active Users
                </a>
                
                
            </div>
        </div>
    </header>

    <main class="container">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type'] ?? 'info'; ?> alert-maroon fade show" role="alert">
                <?= htmlspecialchars($_SESSION['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <div class="table-container mt-4">
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
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($users) > 0): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($user['profile_photo_path'])): ?>
                                            <img src="../uploads/profiles/<?= htmlspecialchars($user['profile_photo_path']) ?>" 
                                                 alt="Profile" class="profile-img">
                                        <?php else: ?>
                                            <div class="profile-img-initial">
                                                <img src="../uploads/profiles/default_user.png" 
                                                 alt="Profile" class="profile-img">
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($user['name']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= htmlspecialchars($user['department']) ?></td>
                                    <td>
                                        <?php 
                                            // Assuming role 1 is Admin, 0 is User
                                            echo ($user['role'] == '1') ? '<span class="user-badge admin">Admin</span>' : 'User'; 
                                        ?>
                                    </td>
                                    <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                                    <td class="action-btns text-center">
                                        
                                        <!-- Activate Button - Triggers Modal -->
                                        <button type="button" class="btn btn-sm btn-delete" 
                                                data-bs-toggle="modal" data-bs-target="#deleteConfirmModal"
                                                data-user-name="<?= htmlspecialchars($user['name']) ?>"
                                                data-user-cpf="<?= htmlspecialchars($user['cpf_no']) ?>">
                                            <i class="fas fa-trash-alt"></i> Activate
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No users found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-3">
                <div class="custom-modal-header">
                    <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Activation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="custom-modal-body">
                    Are you sure you want to activate user "<strong id="modalUserName"></strong>" (CPF: <strong id="modalUserCpf"></strong>)? This action cannot be undone.
                </div>
                <div class="custom-modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="executeActivateUser.php" method="POST" class="d-inline" id="deleteUserForm">
                        <input type="hidden" name="user_cpf" id="deleteUserCpfInput">
                        <button type="submit" class="btn btn-danger btn-delete">Activate</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript to pass data to the modal
        var deleteConfirmModal = document.getElementById('deleteConfirmModal');
        deleteConfirmModal.addEventListener('show.bs.modal', function (event) {
            // Button that triggered the modal
            var button = event.relatedTarget; 
            // Extract info from data-bs-* attributes
            var userName = button.getAttribute('data-user-name');
            var userCpf = button.getAttribute('data-user-cpf');

            // Update the modal's content.
            var modalUserName = deleteConfirmModal.querySelector('#modalUserName');
            var modalUserCpf = deleteConfirmModal.querySelector('#modalUserCpf');
            var deleteUserCpfInput = deleteConfirmModal.querySelector('#deleteUserCpfInput');

            modalUserName.textContent = userName;
            modalUserCpf.textContent = userCpf;
            deleteUserCpfInput.value = userCpf;
        });
    </script>
</body>
</html>
