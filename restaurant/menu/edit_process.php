<?php
require_once('../../db/connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize inputs
    $menu_item_id = intval($_POST['menu_item_id']);
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);

    if (empty($name) || empty($description)) {
        die("Name and Description cannot be empty.");
    }

    if (!is_numeric($price) || floatval($price) <= 0) {
        die("Price must be a valid number and greater than 0.");
    }

    // Check for duplicate name in other records
    $checkQuery = "SELECT id FROM menu_items WHERE name = ? AND id != ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("si", $name, $menu_item_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        die("Another menu item with this name already exists.");
    }

    // Check if a new image was uploaded
    $newFileName = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['image']['type'];

        if (!in_array($fileType, $allowedTypes)) {
            die("Only JPG, PNG, and GIF image types are allowed.");
        }

        $uploadDir = '../../assets/menu/';
        $fileExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $newFileName = uniqid('menu_', true) . '.' . $fileExt;
        $uploadFile = $uploadDir . $newFileName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            die("Error moving the uploaded file.");
        }
    }

    // Update query
    if ($newFileName) {
        $query = "UPDATE menu_items SET name = ?, description = ?, price = ?, image = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssdsi", $name, $description, $price, $newFileName, $menu_item_id);
    } else {
        $query = "UPDATE menu_items SET name = ?, description = ?, price = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssdi", $name, $description, $price, $menu_item_id);
    }

    if ($stmt->execute()) {
        header("Location: menu.php?updated=1");
        exit;
    } else {
        die("Error updating record: " . $stmt->error);
    }
}
?>
