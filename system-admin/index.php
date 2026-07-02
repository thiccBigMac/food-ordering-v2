<?php
session_start();
if (!isset($_SESSION['system_admin_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
header("Location: dashboard.php");
exit;
?>