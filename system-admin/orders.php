<?php
session_start();
if (!isset($_SESSION['system_admin_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
require_once('../db/connection.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>...</title>
    <link href="/food-ordering/restaurant/styles/style.css" rel="stylesheet">
    <style>
        .delete-btn { background-color: #C0605A; color: white; border: none; padding: 7px 14px; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 600; transition: background-color 0.2s ease; }
        .delete-btn:hover { background-color: #a94a45; }
        .success { color: #5C8A66; margin-bottom: 15px; font-weight: 600; background: #eef4ec; padding: 10px 14px; border-radius: 8px; display: inline-block; }
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
    <div class="topbar">All Orders</div>
    <div class="content">
        <table class="menu-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("
                    SELECT o.id, u.name, o.total_amount, o.payment_method, o.status, o.created_at
                    FROM orders o
                    JOIN users u ON o.user_id = u.id
                    ORDER BY o.created_at DESC
                ");
                while ($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td>#<?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td>Rs. <?php echo number_format($row['total_amount'], 2); ?></td>
                    <td><?php echo ucwords(str_replace('_', ' ', $row['payment_method'])); ?></td>
                    <td>
                        <span class="badge <?php echo $row['status']; ?>">
                            <?php echo ucfirst($row['status']); ?>
                        </span>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>