<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['system_admin_id'])) {
    header("Location: /food-ordering/system-admin/login.php");
    exit;
}
?>
