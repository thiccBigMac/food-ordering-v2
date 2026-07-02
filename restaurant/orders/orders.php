<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order</title>
  <link href="/food-ordering/restaurant/styles/style.css" rel="stylesheet">
</head>
<body>
  <?php include "../auth/session.php"; ?>
  <?php include '../sidebar.php'; ?>

  <div class="main-content">
    <div class="topbar">Order</div>
    <div class="content">
      <?php
        require_once('../../db/connection.php');

        $restaurant_id = $_SESSION['admin_id'];
        $stmt = $conn->prepare("
          SELECT o.id, o.user_id, o.total_amount, o.status, o.created_at, u.name
          FROM orders o
          JOIN users u ON o.user_id = u.id
          WHERE o.restaurant_id = ?
          ORDER BY o.created_at DESC
        ");
        $stmt->bind_param("i", $restaurant_id);
        $stmt->execute();
        $result = $stmt->get_result();
      ?>

      <table class="menu-table">
        <thead>
          <tr>
            <th>Order ID</th>
            <th>User</th>
            <th>Total</th>
            <th>Status</th>
            <th>Placed On</th>
            <th>Update Status</th>
            <th>Remove</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td>Rs. <?php echo $row['total_amount']; ?></td>
            <td><?php echo ucfirst($row['status']); ?></td>
            <td><?php echo $row['created_at']; ?></td>
            <td>
              <form method="POST" action="update_order_status.php">
                <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                <input type="hidden" name="action" value="update">
                <select name="status">
                  <option value="pending"    <?php if ($row['status'] === 'pending')    echo 'selected'; ?>>Pending</option>
                  <option value="processing" <?php if ($row['status'] === 'processing') echo 'selected'; ?>>Processing</option>
                  <option value="completed"  <?php if ($row['status'] === 'completed')  echo 'selected'; ?>>Completed</option>
                  <option value="cancelled"  <?php if ($row['status'] === 'cancelled')  echo 'selected'; ?>>Cancelled</option>
                </select>
                <button type="submit" class="btn btn-sm btn-primary">Update</button>
              </form>
            </td>
            <td>
              <form method="POST" action="update_order_status.php" onsubmit="return confirm('Are you sure you want to remove this order?');">
                <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                <input type="hidden" name="action" value="delete">
                <button type="submit" class="btn btn-sm btn-danger">Remove</button>
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