<?php
session_start();
require_once('db/connection.php');

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: orders.php");
    exit();
}

$order_id = intval($_POST['order_id']);
$user_id = $_SESSION['user_id'];

// Make sure this order belongs to this user and is still pending
$stmt = $conn->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status = 'pending'");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();

header("Location: orders.php");
exit();
?>