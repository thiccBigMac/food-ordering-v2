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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>...</title>
        <link href="/food-ordering/restaurant/styles/style.css" rel="stylesheet">
    <style>
        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: white;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(43,38,32,0.12);
        }
        .data-table th {
            background: #2E4E50;
            color: white;
            padding: 13px 16px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }
        .data-table td {
            padding: 13px 16px;
            border-bottom: 1px solid #f0e9d8;
            font-size: 14px;
            color: #2B2620;
        }
        .data-table tr:last-child td { border-bottom: none; }
        .data-table tr:hover td { background: #F3ECDD; }
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