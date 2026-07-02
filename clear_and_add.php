<?php
session_start();
require_once('db/connection.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

if (!isset($_POST['menu_item_id']) || !isset($_POST['restaurant_id'])) {
    die("Invalid request.");
}

$user_id = $_SESSION['user_id'];
$menu_item_id = intval($_POST['menu_item_id']);
$restaurant_id = intval($_POST['restaurant_id']);

// Clear the user's entire cart
$clear = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
$clear->bind_param("i", $user_id);
$clear->execute();

// Add the new item
$stmt = $conn->prepare("INSERT INTO cart (user_id, menu_id, quantity) VALUES (?, ?, 1)");
$stmt->bind_param("ii", $user_id, $menu_item_id);
$stmt->execute();

header("Location: restaurant_menu.php?id=" . $restaurant_id);
exit();
?>