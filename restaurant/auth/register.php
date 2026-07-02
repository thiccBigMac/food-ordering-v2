<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Register</title>
    <link rel="stylesheet" href="/food-ordering/styles/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<nav class="my-navbar">
    <div class="logo">
        <a href="#">HamroKhaja</a>
    </div>
    <ul class="nav-links">  
        <li style="visibility: hidden">Login</li>
    </ul>
</nav>

<div class="login-wrapper">
    <div class="login-box">
        <form action="register_process.php" method="POST">
            <h1>Register</h1>

            <div class="input-box">
                <input type="text" name="username" placeholder="Restaurant Name" required>
            </div>

            <div class="input-box">
                <input type="text" name="email" placeholder="Email" required>
            </div>

            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <div class="input-box">
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            </div>

            <button type="submit" class="btn">Register</button>

            <div class="register-link">
                <p>Already have an account? <a href="/food-ordering/auth/login.php">Login</a></p>
            </div>

        </form>
    </div>
</div>

</body>
</html>