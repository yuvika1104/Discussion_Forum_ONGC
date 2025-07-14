<?php
session_start();
// require_once 'includes/header.php';
require_once '../Database/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_cpf'])) {
    $_SESSION['message'] = 'Please login to create a thread.';
    $_SESSION['message_type'] = 'warning';
    header('Location: ../Login-Logout/viewLogin.php');
    exit;
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="form-container">
                <h2><i class="fas fa-plus text-primary"></i> Create New Thread</h2>
                
                <form method="POST" action="executeCreateThread.php" enctype="multipart/form-data" id="threadForm">
                    <div class="mb-3">
                        <!-- <label for="title" class="form-label">Thread Title *</label>
                        <input type="text" class="form-control" id="title" name="title" 
                               placeholder="Enter a descriptive title for your thread" 
                               required minlength="5" maxlength="255"
                               value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
                        <div class="form-text">Minimum 5 characters, maximum 255 characters</div>
                    </div> -->
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Thread Content *</label>
                        <textarea class="form-control" id="content" name="content" rows="8" 
                                  placeholder="Share your thoughts, ask questions, or start a discussion..." 
                                  required minlength="10" maxlength="5000"><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                        <div class="form-text">Minimum 10 characters, maximum 5000 characters</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="images" class="form-label">Attach Images (Optional)</label>
                        <input type="file" class="form-control" id="images" name="images[]" 
                               accept="image/*" multiple>
                        <div class="form-text">
                            You can upload up to 5 images. Max 5MB per image. 
                            Supported formats: JPEG, PNG, GIF
                        </div>
                        <div id="imagePreview" class="file-preview"></div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="../index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Forum
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-plus"></i> Create Thread
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>