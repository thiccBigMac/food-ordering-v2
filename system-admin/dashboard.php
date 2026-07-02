<?php
session_start();
if (!isset($_SESSION['system_admin_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
require_once('../db/connection.php');

$totalRestaurants = $conn->query("SELECT COUNT(*) as c FROM restaurants")->fetch_assoc()['c'];
$totalUsers       = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$totalOrders      = $conn->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c'];
$pendingOrders    = $conn->query("SELECT COUNT(*) as c FROM orders WHERE status = 'pending'")->fetch_assoc()['c'];
$totalRevenue     = $conn->query("SELECT COALESCE(SUM(total_amount), 0) as c FROM orders WHERE status = 'completed'")->fetch_assoc()['c'];
$totalFeedback    = $conn->query("SELECT COUNT(*) as c FROM contacts")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>System Admin Dashboard</title>
    <link href="/food-ordering/restaurant/styles/style.css" rel="stylesheet">
    <style>
        .wrapper {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .dashboard-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
            padding: 30px 20px;
            text-align: center;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            text-decoration: none;
            color: inherit;
            user-select: none;
        }
        .dashboard-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .dashboard-card h2 {
            font-size: 3rem;
            margin-bottom: 8px;
            color: #2E4E50;
        }
        .dashboard-card p {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <a href="dashboard.php" class="logo">HamroKhaja</a>
    <a href="dashboard.php">Dashboard</a>
    <a href="restaurants.php">Restaurants</a>
    <a href="users.php">Users</a>
    <a href="orders.php">Orders</a>
    <a href="feedback.php">Feedback</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main-content">
    <div class="topbar">Welcome, <?php echo htmlspecialchars($_SESSION['system_admin_user']); ?></div>
    <div class="content">
        <div class="wrapper">
            <a href="restaurants.php" class="dashboard-card">
                <h2><?php echo $totalRestaurants; ?></h2>
                <p>Restaurants</p>
            </a>
            <a href="users.php" class="dashboard-card">
                <h2><?php echo $totalUsers; ?></h2>
                <p>Users</p>
            </a>
            <a href="orders.php" class="dashboard-card">
                <h2><?php echo $totalOrders; ?></h2>
                <p>Total Orders</p>
            </a>
            <a href="orders.php" class="dashboard-card">
                <h2><?php echo $pendingOrders; ?></h2>
                <p>Pending Orders</p>
            </a>
            <div class="dashboard-card">
                <h2>Rs. <?php echo number_format($totalRevenue, 0); ?></h2>
                <p>Revenue</p>
            </div>
            <a href="feedback.php" class="dashboard-card">
                <h2><?php echo $totalFeedback; ?></h2>
                <p>Feedback</p>
            </a>
        </div>
    </div>
</div>

</body>
</html>