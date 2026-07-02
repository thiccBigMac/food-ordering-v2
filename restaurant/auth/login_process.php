<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include the database connection file
require_once('../../db/connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Input validation
    if (empty($email) || empty($password)) {
        die("Please fill in all fields.");
    }

    // Query to fetch the user based on email
    $query = "SELECT * FROM restaurants WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc(); // Get the user data

        // Verify the password using password_verify()
        if (password_verify($password, $user['password'])) {
            // If the password is correct, start the session
            session_start();
            $_SESSION['admin_id'] = $user['id'];  // Store restaurant ID in session
            $_SESSION['admin_name'] = $user['username'];  // Store restaurant name
            header("Location: /food-ordering/restaurant/index.php"); // Redirect to restaurant dashboard
            exit;
        } else {
            die("Incorrect password.");
        }
    } else {
        die("Restaurant with this email doesn't exist.");
    }
}
?>