<?php
session_start();
require_once('../db/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['feedback_message'], $_POST['restaurant_id'])) {
    $message = trim($_POST['feedback_message']);
    $restaurant_id = intval($_POST['restaurant_id']);

    if (empty($message)) {
        die("Feedback cannot be empty.");
    }

    if (!$restaurant_id) {
        die("Please select a restaurant.");
    }

    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : die("Please login to send feedback.");

    $stmt = $conn->prepare("INSERT INTO contacts (user_id, message, restaurant_id) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $user_id, $message, $restaurant_id);

    if ($stmt->execute()) {
        header("Location: ../contact.php?success=1");
        exit();
    } else {
        die("Error saving feedback: " . $stmt->error);
    }
} else {
    die("Invalid request.");
}