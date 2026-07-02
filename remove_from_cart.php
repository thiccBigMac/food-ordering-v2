<?php
session_start();
require_once('db/connection.php');

if (!isset($_SESSION['user_id']) || !isset($_POST['cart_id'])) {
    echo $_SESSION['user_id'];
    die("Unauthorized or invalid request.");
}

$cart_id = intval($_POST['cart_id']);
$user_id = $_SESSION['user_id'];

// Optional: make sure the cart item belongs to the user
$stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $cart_id, $user_id);
$stmt->execute();

header("Location: cart.php");
exit();
