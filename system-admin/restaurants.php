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
    <title>Restaurants</title>
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
        .delete-btn { background-color: #e57373; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 13px; }
        .delete-btn:hover { background-color: #d45d5d; }
        .success { color: green; margin-bottom: 15px; font-weight: 600; }
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