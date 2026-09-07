<?php
session_start();

if (isset($_SESSION['system_admin_id'])) {
    header("Location: /food-ordering/system-admin/index.php");
    exit;
}

require_once('../db/connection.php');

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM system_admins WHERE username = ? OR email = ? LIMIT 1");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            // Support both plain text (legacy) and hashed passwords
            if ($password === $admin['password'] || password_verify($password, $admin['password'])) {
                $_SESSION['system_admin_id'] = $admin['id'];
                $_SESSION['system_admin_user'] = $admin['username'];
                header("Location: /food-ordering/system-admin/index.php");
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Admin Login — HamroKhaja</title>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Work+Sans:wght@400;500;600&display=swap');
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Work Sans', sans-serif; }
    body {
        background-color: #FBF7EF;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    header {
        background: linear-gradient(135deg, #1E3A3A, #2E4E50);
        padding: 18px 40px;
    }
    header h1 {
        font-family: 'Fraunces', serif;
        color: #F0C674;
        font-size: 26px;
        font-weight: 600;
    }
    .wrapper { flex: 1; display: flex; justify-content: center; align-items: center; padding: 20px; }
    .login-card {
        background: #fff;
        padding: 44px 38px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(43,38,32,0.12);
        width: 100%;
        max-width: 400px;
        text-align: center;
    }
    .login-card h2 { font-family: 'Fraunces', serif; font-size: 28px; margin-bottom: 8px; color: #2E4E50; }
    .login-card p.subtitle { font-size: 13px; color: #6B6355; margin-bottom: 28px; }
    .form-group { margin-bottom: 16px; text-align: left; }
    input {
        width: 100%; padding: 13px 16px; border: 1.5px solid #e6ddc9;
        border-radius: 8px; font-size: 15px; color: #2B2620;
        background: #FBF7EF; transition: border-color 0.2s;
    }
    input:focus { border-color: #D9A441; outline: none; }
    input::placeholder { color: #b0a690; }
    .btn {
        width: 100%; padding: 14px; background: #2E4E50; border: none;
        border-radius: 50px; color: white; font-size: 15px; font-weight: 600;
        cursor: pointer; margin-top: 6px; transition: background 0.2s;
    }
    .btn:hover { background: #D9A441; color: #2B2620; }
    .error-msg { color: #C0605A; background: #fbeceb; padding: 10px 14px; border-radius: 8px; margin-bottom: 18px; font-size: 14px; border: 1px solid #eac2c0; }
    .badge { display: inline-block; background: #D9A441; color: #2B2620; font-size: 11px; padding: 4px 12px; border-radius: 20px; margin-bottom: 20px; letter-spacing: 0.5px; font-weight: 600; }
    footer { background-color: #1E3A3A; color: #ecf0f1; text-align: center; padding: 20px; font-size: 13px; }
</style>
</head>
<body>

<header>
    <h1>HamroKhaja</h1>
</header>

<div class="wrapper">
    <div class="login-card">
        <span class="badge">SYSTEM ADMIN</span>
        <h2>Admin Login</h2>
        <p class="subtitle">Access the master control panel</p>

        <?php if (!empty($error)): ?>
            <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <input type="text" name="username" placeholder="Username or Email" required autofocus>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
    </div>
</div>

<footer>
    <p>© <?php echo date('Y'); ?> HamroKhaja. System Administration.</p>
</footer>

</body>
</html>
