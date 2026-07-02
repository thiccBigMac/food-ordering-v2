<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food ordering system</title>
    <link rel="stylesheet" href="/food-ordering/styles/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>

<?php include "../includes/header_login.php" ?>

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
       
          </form>
    </div>
</div>

</body>
</html>


