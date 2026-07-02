<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once('../db/connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        die("Please fill in all fields.");
    }

    // 1. Check system_admins (plain text password)
    $stmt = $conn->prepare("SELECT * FROM system_admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if ($password === $user['password']) {
            $_SESSION['system_admin_id'] = $user['id'];
            $_SESSION['system_admin_user'] = $user['username'];
            header("Location: ../system-admin/dashboard.php");
            exit;
        } else {
            die("Incorrect password.");
        }
    }

    // 2. Check restaurants (hashed password)
    $stmt = $conn->prepare("SELECT * FROM restaurants WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['username'];
            header("Location: ../restaurant/index.php");
            exit;
        } else {
            die("Incorrect password.");
        }
    }

    // 3. Check users (hashed password)
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: ../index.php");
            exit;
        } else {
            die("Incorrect password.");
        }
    }

    die("No account found with this email.");
}
?>