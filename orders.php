<?php include "auth/session.php" ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>My Orders</title>
    <link rel="stylesheet" href="/food-ordering/styles/style.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        html, body {
            overflow-y: auto !important;
            height: auto !important;
        }

        .order-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 20px;
            margin-bottom: 20px;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge.pending { background: #fff3cd; color: #856404; }
        .badge.processing { background: #cfe2ff; color: #084298; }
        .badge.completed { background: #d1e7dd; color: #0f5132; }
        .badge.cancelled { background: #f8d7da; color: #842029; }

        .order-item-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 0;
        }

        .order-item-row img {
            height: 50px;
            width: 50px;
            object-fit: cover;
            border-radius: 6px;
        }

        .delivery-info {
            background: #e6f4f1;
            color: #2E4E50;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 10px;
            display: inline-block;
        }
    </style>
</head>

<body>

    <?php include 'includes/header.php'; ?>

    <div class="container pt-4 pb-5" style="max-width: 800px;">
        <h2 class="mb-4" style="color:#2E4E50;">My Orders</h2>
        <?php if (isset($_GET['cancelled'])): ?>
    <div class="alert alert-success">Order cancelled successfully.</div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger">
        This order cannot be cancelled. It may already be processed.
    </div>
<?php endif; ?>

        <?php
        require_once('db/connection.php');

        if (!isset($_SESSION['user_id'])) {
            header("Location: auth/login.php");
            exit();
        }

        $user_id = $_SESSION['user_id'];
        // Mark completed orders as seen when user visits
$markSeen = $conn->prepare("UPDATE orders SET seen = 1 WHERE user_id = ? AND status = 'completed' AND seen = 0");
$markSeen->bind_param("i", $user_id);
$markSeen->execute();

        // Get all orders for this user, most recent first
        $orderStmt = $conn->prepare("
            SELECT id, total_amount, shipping_address, payment_method, status, created_at, distance_km, estimated_minutes
            FROM orders
            WHERE user_id = ?
            ORDER BY created_at DESC
        ");
        $orderStmt->bind_param("i", $user_id);
        $orderStmt->execute();
        $orders = $orderStmt->get_result();

        if ($orders->num_rows > 0):
            while ($order = $orders->fetch_assoc()):
                // Get items for this specific order
                $itemStmt = $conn->prepare("
                    SELECT oi.quantity, oi.price, m.name, m.image
                    FROM order_items oi
                    JOIN menu_items m ON oi.menu_item_id = m.id
                    WHERE oi.order_id = ?
                ");
                $itemStmt->bind_param("i", $order['id']);
                $itemStmt->execute();
                $orderItems = $itemStmt->get_result();
        ?>
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <strong>Order #<?php echo $order['id']; ?></strong>
                        <span class="text-muted" style="font-size: 13px;">
                            — <?php echo date('M d, Y g:i A', strtotime($order['created_at'])); ?>
                        </span>
                    </div>
                    <span class="badge <?php echo htmlspecialchars($order['status']); ?>">
                        <?php echo ucfirst($order['status']); ?>
                    </span>
                </div>

                <?php if ($order['distance_km'] !== null && $order['estimated_minutes'] !== null): ?>
                    <div class="delivery-info">
                        Distance: <?php echo number_format($order['distance_km'], 2); ?> km away
                        —  Estimated delivery: ~<?php echo $order['estimated_minutes']; ?> min
                    </div>
                <?php endif; ?>

                <?php while ($item = $orderItems->fetch_assoc()): ?>
                    <div class="order-item-row">
                        <img src="assets/menu/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <div>
                            <?php echo htmlspecialchars($item['name']); ?> x <?php echo $item['quantity']; ?>
                        </div>
                        <div class="ms-auto">
                            Rs. <?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                        </div>
                    </div>
                <?php endwhile; ?>

                <hr>

                <div class="d-flex justify-content-between" style="font-size: 14px; color:#555;">
                    <span><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($order['shipping_address']); ?></span>
                    <span><?php echo ucwords(str_replace('_', ' ', $order['payment_method'])); ?></span>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-2">
                    <?php if ($order['status'] === 'pending'): ?>
                        <form method="POST" action="cancel_order.php" onsubmit="return confirm('Cancel this order?');">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">Cancel Order</button>
                        </form>
                    <?php else: ?>
                        <span></span>
                    <?php endif; ?>
                    <strong>Total: Rs. <?php echo number_format($order['total_amount'], 2); ?></strong>
                </div>
            </div>
        <?php
            endwhile;
        else:
            echo '<p class="text-muted">You haven\'t placed any orders yet.</p>';
        endif;
        ?>
    </div>

    <?php include 'includes/footer.php'; ?>

</body>
</html>