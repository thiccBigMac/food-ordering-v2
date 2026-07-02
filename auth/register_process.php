<?php
// Include the database connection file
require_once('../db/connection.php');

// Check if the form was submitted (POST request)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get the form data
    $name = $_POST['fname'] . ' ' . $_POST['lname']; // Combine first and last name
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $password = $_POST['password'];

    // Input validation
    if (empty($name) || empty($email) || empty($contact) || empty($password)) {
        die("Please fill in all fields.");
    }

    // Check if email is valid
    //If email is fine then no Filter_VALIDATE_EMAIL which gives !true 
    //If email is invalid, i.e the format is wrong then it does !false
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // Validate contact number (optional: you can add more validation for phone numbers if needed)
    if (!preg_match("/^98[0-9]{8}$/", $contact)) {
        die("Invalid contact number. Please enter a 10-digit phone number.");
    }

     // Validate password: minimum 6 characters, at least one special character
    if (strlen($password) < 6) {
        die("Password must be at least 6 characters long.");
    }

// Check if the email is already in the database
$query = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($query);       // Prepare the SQL query safely
$stmt->bind_param("s", $email);       // Bind the email to the query (s = string)
$stmt->execute();                     // Run the query
$result = $stmt->get_result();        // Get the result

if ($result->num_rows > 0) {          // If a user with this email exists
    die("User with this email already exists."); // Stop and show message
}

// Hash (encrypt) the password before saving it
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Now insert the new user into the database
$query = "INSERT INTO users (name, email, password, contact) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($query);                           // Prepare insert query
$stmt->bind_param("ssss", $name, $email, $hashed_password, $contact); // Bind all 4 values
if ($stmt->execute()) {                                   // Run the query
    header("Location: login.php");                        // Go to login page
    exit;
} else {
    die("Error: " . $stmt->error);                        // Show error if something went wrong
}
}
?>