<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox</title>
    <link href="/food-ordering/restaurant/styles/style.css" rel="stylesheet">
</head>
<body>
<?php include "../auth/session.php"; ?>
<?php include '../sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">Inbox</div>
    <div class="content">
        <?php
        require_once('../../db/connection.php');
        $restaurant_id = $_SESSION['admin_id'];

        $stmt = $conn->prepare("
            SELECT c.id, c.message, u.name
            FROM contacts c
            LEFT JOIN users u ON c.user_id = u.id
            WHERE c.restaurant_id = ?
            ORDER BY c.id DESC
        ");
        $stmt->bind_param("i", $restaurant_id);
        $stmt->execute();
        $result = $stmt->get_result();
        ?>

        <table class="menu-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name'] ?? 'Guest'); ?></td>
                        <td><?php echo htmlspecialchars($row['message']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" style="text-align:center; color:#999; padding:20px;">
                            No feedback yet.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>