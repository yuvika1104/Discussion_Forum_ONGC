<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../Database/db_connect.php';

// Get thread_id from most reliable to least reliable sources
$thread_id = (int)( $_GET['id']?? $_POST['thread_id'] ?? $_SESSION['thread_id'] ?? 0);

// Always store in session for future requests
if ($thread_id > 0) {
    $_SESSION['thread_id'] = $thread_id;
} 
else {
    header('Location: viewAllThread.php');
    // $_SESSION['message'] = 'Invalid thread ID';
    // $_SESSION['message_type'] = 'danger';
    // header('Location: ../index.php');
    // exit;
}

// Get thread details with user info
$sql = "SELECT t.*, u.name as user_name, u.department,u.bio, u.profile_photo_path, u.role, u.designation
        FROM threads t 
        LEFT JOIN user u ON t.cpf_no = u.cpf_no
        WHERE t.thread_id = ? and t.active=1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$thread_id]);
$thread = $stmt->fetch();

if (!$thread) {
    $_SESSION['message'] = 'Thread not found.';
    $_SESSION['message_type'] = 'danger';
    header('Location: ../index.php');
    exit;
}

// Get thread images
$img_sql = "SELECT image_path FROM thread_images WHERE thread_id = ? ORDER BY image_path";
$img_stmt = $pdo->prepare($img_sql);
$img_stmt->execute([$thread_id]);
$thread_images = $img_stmt->fetchAll();

// Get replies with user info
$reply_sql = "SELECT r.*, u.name as user_name, u.bio,u.department, u.profile_photo_path, u.role, u.designation
              FROM replies r 
              LEFT JOIN user u ON r.cpf_no = u.cpf_no
              WHERE r.thread_id = ? and r.active=1
              ORDER BY r.created_at ASC";
$reply_stmt = $pdo->prepare($reply_sql);
$reply_stmt->execute([$thread_id]);
$replies = $reply_stmt->fetchAll();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle reply submission
    if (isset($_POST['reply_content'])) {
        if (!isset($_SESSION['user_cpf'])) {
            $_SESSION['message'] = 'Please login to reply.';
            $_SESSION['message_type'] = 'warning';
            header('Location: ../Login-Logout/viewLogin.php');
            exit;
        }
        
        $reply_content = trim($_POST['reply_content']);
        $user_cpf = $_SESSION['user_cpf'];
        
        if (empty($reply_content)) {
            $_SESSION['message'] = 'Reply content cannot be empty.';
            $_SESSION['message_type'] = 'danger';
        } elseif (strlen($reply_content) < 5) {
            $_SESSION['message'] = 'Reply must be at least 5 characters long.';
            $_SESSION['message_type'] = 'danger';
        } else {
            try {
                $pdo->beginTransaction();
                
                // Insert reply
                $reply_sql = "INSERT INTO replies (thread_id, cpf_no, content) VALUES (?, ?, ?)";
                $reply_stmt = $pdo->prepare($reply_sql);
                $reply_stmt->execute([$thread_id, $user_cpf, $reply_content]);
                $reply_id = $pdo->lastInsertId();
                
                // Handle reply images
                $uploaded_images = [];
                if (!empty($_FILES['reply_images']['name'][0])) {
                    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                    $max_size = 5 * 1024 * 1024; // 5MB
                    $max_files = 3;
                    
                    $file_count = count($_FILES['reply_images']['name']);
                    
                    if ($file_count <= $max_files) {
                        for ($i = 0; $i < $file_count; $i++) {
                            if ($_FILES['reply_images']['error'][$i] == UPLOAD_ERR_OK) {
                                if (in_array($_FILES['reply_images']['type'][$i], $allowed_types) && 
                                    $_FILES['reply_images']['size'][$i] <= $max_size) {
                                    
                                    $extension = pathinfo($_FILES['reply_images']['name'][$i], PATHINFO_EXTENSION);
                                    $filename = $reply_id . '_' . ($i + 1) . '.' . $extension;
                                    $upload_path = '../uploads/replies/' . $filename;

                                    if (move_uploaded_file($_FILES['reply_images']['tmp_name'][$i], $upload_path)) {
                                        $uploaded_images[] = $filename;
                                    }
                                }
                            }
                        }
                    }

                    // Insert reply images
                    if (!empty($uploaded_images)) {
                        $img_sql = "INSERT INTO reply_images (reply_id, image_path) VALUES (?, ?)";
                        $img_stmt = $pdo->prepare($img_sql);

                        foreach ($uploaded_images as $image) {
                            $img_stmt->execute([$reply_id, $image]);
                        }
                    }
                }
                
                $pdo->commit();
                
                $_SESSION['message'] = 'Reply posted successfully!';
                $_SESSION['message_type'] = 'success';
                header('Location: viewThread.php?id=' . $thread_id . '#reply-' . $reply_id);
                exit;
            } catch (PDOException $e) {
                $pdo->rollBack();
                // Clean up uploaded images if any
                if (!empty($uploaded_images)) {
                    foreach ($uploaded_images as $image) {
                        $file_path = '../uploads/replies/' . $image;
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                    }
                }
                
                $_SESSION['message'] = 'An error occurred while posting your reply: ' . $e->getMessage();
                $_SESSION['message_type'] = 'danger';
            }
        }
    }
    // Handle deletion
    elseif (isset($_POST['delete_item'])) {
        if (!isset($_SESSION['user_cpf'])) {
            $_SESSION['message'] = 'Please login to perform this action.';
            $_SESSION['message_type'] = 'warning';
            header('Location: ../Login-Logout/viewLogin.php');
            exit;
        }
        
        $item_type = $_POST['item_type']; // 'thread' or 'reply'
        $item_id = $_POST['item_id'];
        $user_cpf = $_SESSION['user_cpf'];
        $user_role = $_SESSION['user_role'];
        
        try {
            if ($item_type == 'thread') {
                // Check if user owns the thread or is admin
                $check_sql = "SELECT cpf_no FROM threads WHERE thread_id = ?";
                $check_stmt = $pdo->prepare($check_sql);
                $check_stmt->execute([$item_id]);
                $owner_id = $check_stmt->fetchColumn();
                
                if ($owner_id == $user_cpf || $user_role == '1') {
                    // // First delete thread images
                    // $delete_images_sql = "DELETE FROM thread_images WHERE thread_id = ?";
                    // $delete_images_stmt = $pdo->prepare($delete_images_sql);
                    // $delete_images_stmt->execute([$item_id]);
                    
                    // Then delete the thread
                    $delete_sql = "UPDATE threads SET active=0 WHERE thread_id = ?";
                    $delete_stmt = $pdo->prepare($delete_sql);
                    $delete_stmt->execute([$item_id]);
                    
                    $_SESSION['message'] = 'Thread deleted successfully.';
                    $_SESSION['message_type'] = 'success';
                    header('Location: ../index.php');
                    exit;
                }
            } elseif ($item_type == 'reply') {
                // Check if user owns the reply or is admin
                $check_sql = "SELECT cpf_no FROM replies WHERE reply_id = ?";
                $check_stmt = $pdo->prepare($check_sql);
                $check_stmt->execute([$item_id]);
                $owner_id = $check_stmt->fetchColumn();
                
                if ($owner_id == $user_cpf || $user_role == '1') {
                    // // First delete reply images
                    // $delete_images_sql = "DELETE FROM reply_images WHERE reply_id = ?";
                    // $delete_images_stmt = $pdo->prepare($delete_images_sql);
                    // $delete_images_stmt->execute([$item_id]);
                    
                    // Then delete the reply
                    $delete_sql = "UPDATE replies SET active=0 WHERE reply_id = ?";
                    $delete_stmt = $pdo->prepare($delete_sql);
                    $delete_stmt->execute([$item_id]);
                    
                    $_SESSION['message'] = 'Reply deleted successfully.';
                    $_SESSION['message_type'] = 'success';
                    header('Location: viewThread.php?id=' . $thread_id);
                    exit;
                }
            }
        } catch (PDOException $e) {
            $_SESSION['message'] = 'An error occurred while deleting: ' . $e->getMessage();
            $_SESSION['message_type'] = 'danger';
        }
    }
}