<?php
session_start();
require_once('db/connection.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$total_amount = $_SESSION['checkout_total'] ?? 0;
$shipping_address = $_SESSION['checkout_shipping'] ?? '';
$payment_method = 'esewa';

if (empty($shipping_address)) {
    die("Missing shipping address.");
}

$conn->begin_transaction();

try {
    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, payment_method, status) VALUES (?, ?, ?, ?, 'completed')");
    $stmt->bind_param("idss", $user_id, $total_amount, $shipping_address, $payment_method);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // Insert order items
    $cart = $conn->prepare("SELECT menu_id, quantity FROM cart WHERE user_id = ?");
    $cart->bind_param("i", $user_id);
    $cart->execute();
    $cartResult = $cart->get_result();

    while ($row = $cartResult->fetch_assoc()) {
        $menu_item_id = $row['menu_id'];
        $qty = $row['quantity'];

        $priceStmt = $conn->prepare("SELECT price FROM menu_items WHERE id = ?");
        $priceStmt->bind_param("i", $menu_item_id);
        $priceStmt->execute();
        $price = $priceStmt->get_result()->fetch_assoc()['price'];

        $itemInsert = $conn->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)");
        $itemInsert->bind_param("iiid", $order_id, $menu_item_id, $qty, $price);
        $itemInsert->execute();
    }

    // Clear cart
    $clear = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $clear->bind_param("i", $user_id);
    $clear->execute();

    // Clear session
    unset($_SESSION['checkout_total']);
    unset($_SESSION['checkout_shipping']);
    unset($_SESSION['esewa_txn_id']);
    unset($_SESSION['applied_code']);

    $conn->commit();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Successful</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>
<div style="max-width:400px; margin:40px auto; padding:20px; border-radius:10px; 
            background:#e6f4f1; color:#2E4E50; font-family:Arial; text-align:center;
            box-shadow:0 0 10px rgba(46,78,80,0.2);">
    <h2 style="margin-bottom:15px;">✅ Payment Successful!</h2>
    <p style="margin-bottom:20px;">Your order has been placed successfully via eSewa.</p>
    <a href="index.php" style="display:inline-block; padding:12px 24px; background:#2E4E50; 
            color:white; border-radius:8px; text-decoration:none; font-weight:700;">
        Back to Home
    </a>
</div>
<?php include 'includes/footer.php'; ?>
</body>
</html>

<?php
} catch (Exception $e) {
    $conn->rollback();
    echo "<p style='color:red; text-align:center;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>