<?php
session_start();
if (!isset($_SESSION['system_admin_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
require_once('../db/connection.php');

// Handle delete
if (isset($_POST['delete_restaurant'])) {
    $id = intval($_POST['restaurant_id']);
    $conn->prepare("DELETE FROM restaurants WHERE id = ?")->bind_param("i", $id);
    $stmt = $conn->prepare("DELETE FROM restaurants WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: restaurants.php?deleted=1");
    exit;
}
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
    <div class="topbar">Restaurants</div>
    <div class="content">

        <?php if (isset($_GET['deleted'])): ?>
            <p class="success">Restaurant deleted successfully.</p>
        <?php endif; ?>

        <table class="menu-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT id, username, email FROM restaurants ORDER BY id DESC");
                while ($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['email'] ?? 'N/A'); ?></td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Delete this restaurant?');">
                            <input type="hidden" name="restaurant_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete_restaurant" class="delete-btn">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>