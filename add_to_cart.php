<?php
session_start();
require_once('db/connection.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$menu_item_id = intval($_POST['menu_item_id']);
$restaurant_id = intval($_POST['restaurant_id']);

// Check if cart has items from a DIFFERENT restaurant
$check = $conn->prepare("
    SELECT c.id, m.restaurant_id 
    FROM cart c 
    JOIN menu_items m ON c.menu_id = m.id 
    WHERE c.user_id = ?
    LIMIT 1
");
$check->bind_param("i", $user_id);
$check->execute();
$existing = $check->get_result()->fetch_assoc();

if ($existing && $existing['restaurant_id'] != $restaurant_id) {
    // Cart has items from different restaurant
    die("
        <div style='font-family:Arial; text-align:center; margin-top:50px;'>
            <h3>Your cart has items from another restaurant!</h3>
            <p>Do you want to clear your cart and add this item instead?</p>
            <form method='POST' action='clear_and_add.php'>
                <input type='hidden' name='menu_item_id' value='$menu_item_id'>
                <input type='hidden' name='restaurant_id' value='$restaurant_id'>
                <button type='submit' style='padding:10px 20px; background:#2E4E50; color:white; border:none; border-radius:6px; cursor:pointer; margin-right:10px;'>
                    Yes, Clear Cart
                </button>
            </form>
            <br>
            <a href='javascript:history.back()' style='color:#2E4E50;'>No, Go Back</a>
        </div>
    ");
}

// Check if item already in cart
$check2 = $conn->prepare("SELECT id FROM cart WHERE user_id = ? AND menu_id = ?");
$check2->bind_param("ii", $user_id, $menu_item_id);
$check2->execute();

if ($check2->get_result()->num_rows == 0) {
    $stmt = $conn->prepare("INSERT INTO cart (user_id, menu_id, quantity) VALUES (?, ?, 1)");
    $stmt->bind_param("ii", $user_id, $menu_item_id);
    $stmt->execute();
}

header("Location: restaurant_menu.php?id=" . $restaurant_id);
exit();
?>