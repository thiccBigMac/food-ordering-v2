<?php
require_once('../../db/connection.php');
include '../auth/session.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $restaurant_id = $_SESSION['admin_id'];

    if (empty($name) || empty($description)) {
        die("Name and Description cannot be empty.");
    }

    if (!is_numeric($price) || floatval($price) <= 0) {
        die("Price must be a valid number greater than 0.");
    }

    // Check for duplicate menu name for this restaurant only
    $checkStmt = $conn->prepare("SELECT id FROM menu_items WHERE name = ? AND restaurant_id = ?");
    $checkStmt->bind_param("si", $name, $restaurant_id);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows > 0) {
        die("A menu item with this name already exists.");
    }

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['image']['type'], $allowedTypes)) {
            die("Only JPG, PNG, and GIF images are allowed.");
        }

        // Absolute path to make sure folder is found
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/food-ordering/assets/menu/';

        // Create folder if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $newFileName = uniqid('menu_', true) . '.' . $fileExt;
        $uploadPath = $uploadDir . $newFileName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
            die("Error uploading image. Please try again.");
        }
    } else {
        die("Please upload an image.");
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO menu_items (restaurant_id, name, description, price, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issds", $restaurant_id, $name, $description, $price, $newFileName);

    if ($stmt->execute()) {
        header("Location: menu.php");
        exit;
    } else {
        die("Error: " . $stmt->error);
    }
}
?>