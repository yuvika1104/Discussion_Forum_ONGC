
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Check if user is admin

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== '1') {
    header("Location: ../Auth/unauthorized.php");
    exit();
}

?>
