<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../Database/db_connect.php';

// Ensure user is logged in
if (!isset($_SESSION['user_cpf'])) {
    header('Location: ../Login-Logout/viewLogin.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $user_cpf = $_SESSION['user_cpf'];
    $errors = [];
    $uploaded_images = [];

    // Validation
    if ( empty($content)) {
        $errors[] = 'Please fill in all required fields.';
    }

    if (strlen($title) < 5) {
        $errors[] = 'Thread title must be at least 5 characters long.';
    }

    if (strlen($content) < 10) {
        $errors[] = 'Thread content must be at least 10 characters long.';
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            
            // Insert thread first
            $sql = "INSERT INTO threads (cpf_no, title, content) VALUES (?, ?,?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$user_cpf,$title, $content]);
            $thread_id = $pdo->lastInsertId();

            

            // Handle image uploads (after getting thread_id)
            if (!empty($_FILES['images']['name'][0])) {
                $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                $max_size = 5 * 1024 * 1024; // 5MB
                $max_files = 5;
                $file_count = count($_FILES['images']['name']);

                if ($file_count > $max_files) {
                    $errors[] = "You can upload maximum $max_files images.";
                } else {
                    for ($i = 0; $i < $file_count; $i++) {
                        if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                            $extension = strtolower(pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION));
                            $mime = mime_content_type($_FILES['images']['tmp_name'][$i]);

                            if (!in_array($mime, $allowed_types)) {
                                $errors[] = 'Invalid image format.';
                                break;
                            }

                            if ($_FILES['images']['size'][$i] > $max_size) {
                                $errors[] = 'Each image must be less than 5MB.';
                                break;
                            }

                            // Generate filename as threadID_imgNo.extension
                            $filename = $thread_id . '_' . ($i + 1) . '.' . $extension;
                            $upload_path = '../uploads/threads/' . $filename;

                            if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $upload_path)) {
                                $uploaded_images[] = $filename;
                            } else {
                                $errors[] = 'Failed to upload one or more images.';
                                break;
                            }
                        }
                    }

                    // Insert image records if no upload errors
                    if (empty($errors) && !empty($uploaded_images)) {
                        $img_sql = "INSERT INTO thread_images (thread_id, image_path) VALUES (?, ?)";
                        $img_stmt = $pdo->prepare($img_sql);

                        foreach ($uploaded_images as $img) {
                            $img_stmt->execute([$thread_id, $img]);
                        }
                    }
                }
            }

            // Finalize
            if (empty($errors)) {
                $pdo->commit();
                $_SESSION['message'] = 'Thread created successfully!';
                $_SESSION['message_type'] = 'success';
                header('Location: viewThread.php?id=' . $thread_id);
                exit;
            } else {
                $pdo->rollBack();
            }

        } catch (PDOException $e) {
            $pdo->rollBack();

            // Delete uploaded files if rollback
            foreach ($uploaded_images as $img) {
                $file_path = '../uploads/threads/' . $img;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }

            $errors[] = 'An error occurred while creating the thread. Please try again.';
        }
    }

    // Set error message
    if (!empty($errors)) {
        $_SESSION['message'] = implode('<br>', $errors);
        $_SESSION['message_type'] = 'danger';
    }
}
?>
