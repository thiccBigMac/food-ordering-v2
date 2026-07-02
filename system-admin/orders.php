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
    <title>Orders</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: "Segoe UI", sans-serif; }
        body { display: flex; min-height: 100vh; background-color: #f4f4f4; }
        .sidebar { width: 250px; background-color: #222; color: #ecf0f1; padding: 20px; min-height: 100vh; }
        .sidebar .logo { font-size: 26px; font-family: 'Lucida Sans', Geneva, Verdana, sans-serif; color: #DFD0B8; text-decoration: none; display: block; margin-bottom: 30px; padding-bottom: 15px; border-bottom: 1px solid #333; }
        .sidebar a { display: block; color: #bdc3c7; text-decoration: none; padding: 15px 0; border-bottom: 1px solid black; }
        .sidebar a:hover { opacity: 0.5; }
        .main-content { flex: 1; display: flex; flex-direction: column; }
        .topbar { background-color: #2E4E50; color: white; padding: 15px 20px; font-size: 25px; font-weight: bold; }
        .content { padding: 20px; }
        .menu-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .menu-table th, .menu-table td { border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 14px; }
        .menu-table th { background-color: #2E4E50; color: white; }
        .menu-table tr:hover { background-color: #f5f5f5; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge.pending { background: #fff3cd; color: #856404; }
        .badge.completed { background: #d1e7dd; color: #0f5132; }
        .badge.cancelled { background: #f8d7da; color: #842029; }
        .badge.processing { background: #cfe2ff; color: #084298; }
    </style>
</head>
<body>

<div class="sidebar">
    <a href="dashboard.php" class="logo">HamroKhaja</a>
    <a href="dashboard.php">Dashboard</a>
    <a href="restaurants.php">Restaurants</a>
    <a href="users.php">Users</a>
    <a href="orders.php">Orders</a>
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