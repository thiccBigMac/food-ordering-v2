<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Failed</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>
<div style="max-width:400px; margin:40px auto; padding:20px; border-radius:10px; 
            background:#fde8e8; color:#842029; font-family:Arial; text-align:center;
            box-shadow:0 0 10px rgba(132,32,41,0.2);">
    <h2 style="margin-bottom:15px;">❌ Payment Failed!</h2>
    <p style="margin-bottom:20px;">Something went wrong with your eSewa payment. Please try again.</p>
    <a href="checkout.php" style="display:inline-block; padding:12px 24px; background:#842029; 
            color:white; border-radius:8px; text-decoration:none; font-weight:700;">
        Try Again
    </a>
</div>
<?php include 'includes/footer.php'; ?>
</body>
</html>