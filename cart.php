<?php include "auth/session.php" ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Food ordering system</title>
    <link rel="stylesheet" href="/food-ordering/styles/style.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        /* Heading styles */
        #cart-title {
            font-weight: 500 !important;
            color: #333333 !important;
            margin-bottom: 1rem !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
        }

        /* Container overrides */
        #cart-container {
            overflow-y: auto !important;
            width: auto !important;
            height: auto !important;
            padding-top: 0 !important;
            padding-bottom: 1.5rem !important;
        }

        /* Table heading minimalistic black */
        table.table thead th {
            color: #000000 !important;
            font-weight: 600 !important;
            border-bottom: 2px solid #ddd !important;
            background-color: transparent !important;
        }

        /* Softer green button instead of bright bootstrap-success */
        .btn-success {
            background-color: #78ac7aff !important; /* softer green */
            border-color: #4caf50 !important;
            color: #fff !important;
            transition: background-color 0.3s ease;
        }

        .btn-success:hover {
            background-color: #45a049 !important;
            border-color: #45a049 !important;
        }

        /* Softer red button instead of bright bootstrap-danger */
        .btn-danger {
            background-color: #e57373 !important; /* softer red */
            border-color: #e57373 !important;
            color: #fff !important;
            transition: background-color 0.3s ease;
        }

        .btn-danger:hover {
            background-color: #d45d5d !important;
            border-color: #d45d5d !important;
        }

        /* Optional: softer secondary buttons for quantity controls */
        .btn-secondary {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            color: #fff !important;
        }

        .btn-secondary:hover {
            background-color: #5a6268 !important;
            border-color: #5a6268 !important;
        }
    </style>
</head>

<body>

    <?php include 'includes/header.php'; ?>
    <div id="cart-container" class="container pt-0 pb-4">
        <?php
        require_once('db/connection.php');

        if (!isset($_SESSION['user_id'])) {
            header("Location:auth/login.php");
            exit();
        }

        $user_id = $_SESSION['user_id'];

        $query = "
            SELECT c.id AS cart_id, m.name, m.description, m.price, m.image, c.quantity
            FROM cart c
            JOIN menu_items m ON c.menu_id = m.id
            WHERE c.user_id = ?
        ";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        ?>

        <h2 id="cart-title" class="custom-heading">Your Cart</h2>

        <?php if ($result->num_rows > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $grandTotal = 0; ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <img src="assets/menu/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" style="height: 60px;" />
                            </td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td>Rs. <?php echo number_format($row['price'], 2); ?></td>

                            <td>
                                <form action="update_quantity.php" method="POST" style="display:flex; gap:5px; align-items:center;">
                                    <input type="hidden" name="cart_id" value="<?php echo $row['cart_id']; ?>" />
                                    <button type="submit" name="action" value="decrease" class="btn btn-sm btn-secondary">−</button>
                                    <span><?php echo $row['quantity']; ?></span>
                                    <button type="submit" name="action" value="increase" class="btn btn-sm btn-secondary">+</button>
                                </form>
                            </td>

                            <td>Rs. <?php echo number_format($row['price'] * $row['quantity'], 2); ?></td>
                            <td>
                                <form method="POST" action="remove_from_cart.php" onsubmit="return confirm('Remove item from cart?');">
                                    <input type="hidden" name="cart_id" value="<?php echo $row['cart_id']; ?>" />
                                    <button class="btn btn-danger btn-sm">Remove</button>
                                </form>
                            </td>
                        </tr>
                        <?php $grandTotal += $row['price'] * $row['quantity']; ?>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4">Grand Total</th>
                        <th colspan="2">Rs. <?php echo number_format($grandTotal, 2); ?></th>
                    </tr>
                </tfoot>
            </table>

            <?php if ($grandTotal > 0): ?>
                <form action="checkout.php" method="POST">
                    <button type="submit" class="btn btn-success">Proceed to Checkout (Rs. <?php echo number_format($grandTotal, 2); ?>)</button>
                </form>
            <?php endif; ?>

        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>

</body>

</html>
