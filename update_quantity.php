<?php
session_start();
require_once('db/connection.php');

if (!isset($_SESSION['user_id']) || !isset($_POST['cart_id'], $_POST['action'])) {
    die("Invalid request.");
}

$cart_id = intval($_POST['cart_id']);
$user_id = $_SESSION['user_id'];
$action = $_POST['action'];

if (!in_array($action, ['increase', 'decrease'])) {
    die("Invalid action.");
}

// Get current quantity
$stmt = $conn->prepare("SELECT quantity FROM cart WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $cart_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $qty = $row['quantity'];

    if ($action === 'increase') {
        $qty++;
    } elseif ($action === 'decrease') {
        $qty = max(1, $qty - 1); // Don't go below 1
    }

    $updateStmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
    $updateStmt->bind_param("iii", $qty, $cart_id, $user_id);
    $updateStmt->execute();
}

header("Location: cart.php");
exit();
