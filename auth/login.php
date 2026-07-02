<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HamroKhaja</title>
    <link rel="stylesheet" href="/food-ordering/styles/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<nav class="my-navbar">
    <div class="logo">
        <a href="/food-ordering/index.php">HamroKhaja</a>
    </div>
    <ul class="nav-links">
        <li style="visibility: hidden">Login</li>
    </ul>
</nav>

<div class="login-wrapper">
    <div class="login-box">
        <form action="login_process.php" method="POST">
            <h1>Login</h1>
            
            <div class="input-box">
                <input type="text" name="email" placeholder="Email" required>
            </div>
    
            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit" class="btn">Login</button>

            <div class="register-link">
                <p>Don't have an account? <a href="register.php">Register</a></p>
                <p>Are you a restaurant? <a href="/food-ordering/restaurant/auth/register.php">Register here</a></p>
            </div>
        </form>
    </div>
</div>

</body>
</html>