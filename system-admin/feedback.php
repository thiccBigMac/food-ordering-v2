<?php
session_start();
if (!isset($_SESSION['system_admin_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
require_once '../db/connection.php';

$result = $conn->query("
    SELECT c.id, c.message, u.name AS user_name, r.username AS restaurant_name
    FROM contacts c
    LEFT JOIN users u ON c.user_id = u.id
    LEFT JOIN restaurants r ON c.restaurant_id = r.id
    ORDER BY c.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Feedback — System Admin</title>
    <link href="/food-ordering/restaurant/styles/style.css" rel="stylesheet">
    <style>
        .data-table { width: 100%; border-collapse: collapse; background: white;
            border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.07); }
        .data-table th { background: #2E4E50; color: white; padding: 13px 15px; text-align: left; font-size: 14px; }
        .data-table td { padding: 12px 15px; border-bottom: 1px solid #f0f0f0; font-size: 14px; color: #333; }
        .data-table tr:last-child td { border-bottom: none; }
        .data-table tr:hover td { background: #f9f9f9; }
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
    <div class="topbar">All Feedback</div>
    <div class="content">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Restaurant</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['user_name'] ?? 'Guest'); ?></td>
                        <td><?php echo htmlspecialchars($row['restaurant_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['message']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align:center; color:#999; padding:20px;">No feedback yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>