<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('../../db/connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if any field is empty
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        die("Please fill in all fields.");
    }

    // Check if email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        die("Passwords do not match.");
    }

    // Check if password is at least 6 characters
    if (strlen($password) < 6) {
        die("Password must be at least 6 characters.");
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM restaurants WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        die("A restaurant with this email already exists.");
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO restaurants (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed_password);

    if ($stmt->execute()) {
        header("Location: /food-ordering/auth/login.php?registered=1");
        exit;
    } else {
        die("Error: " . $stmt->error);
    }
}
?>