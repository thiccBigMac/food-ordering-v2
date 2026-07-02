<?php
require_once('../../db/connection.php');
include '../auth/session.php';

$restaurant_id = $_SESSION['admin_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = intval($_POST['order_id']);

    // Verify this order belongs to this restaurant
    $verify = $conn->prepare("
        SELECT DISTINCT o.id 
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN menu_items m ON oi.menu_item_id = m.id
        WHERE o.id = ? AND m.restaurant_id = ?
    ");
    $verify->bind_param("ii", $order_id, $restaurant_id);
    $verify->execute();

    if ($verify->get_result()->num_rows === 0) {
        die("Unauthorized: This order does not belong to your restaurant.");
    }

    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->bind_param("i", $order_id);
        if ($stmt->execute()) {
            header("Location: orders.php?deleted=1");
            exit();
        } else {
            echo "Failed to delete order.";
        }

    } elseif (isset($_POST['status'])) {
        $status = $_POST['status'];
        $allowed = ['pending', 'processing', 'completed', 'cancelled'];

        if (!in_array($status, $allowed)) {
            die("Invalid status.");
        }

        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);
        if ($stmt->execute()) {
            header("Location: orders.php?updated=1");
            exit();
        } else {
            echo "Failed to update status.";
        }

    } else {
        echo "Invalid request.";
    }
} else {
    echo "Invalid method.";
}
?>