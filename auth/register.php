<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<link rel="stylesheet" href="../styles/style.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<body>
<?php include "../includes/header_login.php"?>

<div class="login-wrapper">
    <div class="login-box">
        <form action="register_process.php" method="POST">
            
            <div class="input-box">
                <input type="text" name="fname" placeholder="First name" required>
            </div>

            <div class="input-box">
                <input type="text" name="lname" placeholder="Last name" required>
            </div>

            <div class="input-box">
                <input type="telephone" name="contact" placeholder="Contact" required>
            </div>
            
            <div class="input-box">
                <input type="text" name="email" placeholder="Email" required>
            </div>
    
            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit" class="btn">Register</button>
       
            <div class="register-link">
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>

          </form>
    </div>
</div>

<?php include"../includes/footer.php"?>
</body>
</html>