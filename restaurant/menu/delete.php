<?php
require_once('../../db/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['menu_item_id'])) {
    $menu_item_id = intval($_POST['menu_item_id']);

    // Optional: delete image file from server
    $stmt = $conn->prepare("SELECT image FROM menu_items WHERE id = ?");
    $stmt->bind_param("i", $menu_item_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $imagePath = '../../assets/menu/' . $row['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath); // delete image file
        }
    }

    // Delete the menu item from the database
    $deleteStmt = $conn->prepare("DELETE FROM menu_items WHERE id = ?");
    $deleteStmt->bind_param("i", $menu_item_id);
    
    if ($deleteStmt->execute()) {
        header("Location: menu.php?deleted=1");
        exit;
    } else {
        echo "Error deleting menu item.";
    }
} else {
    echo "Invalid request.";
}
?>
