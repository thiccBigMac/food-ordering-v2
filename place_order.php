<?php
session_start();
require_once('db/connection.php');

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("<p style='color:red;'>Unauthorized.</p>");
}

$user_id = $_SESSION['user_id'];
$total_amount = $_POST['total_amount'];
$shipping_address = trim($_POST['shipping_address']);
$payment_method = $_POST['payment_method'];

$latitude = !empty($_POST['latitude']) ? $_POST['latitude'] : null;
$longitude = !empty($_POST['longitude']) ? $_POST['longitude'] : null;
$distance_km = !empty($_POST['distance_km']) ? $_POST['distance_km'] : null;
$estimated_minutes = !empty($_POST['estimated_minutes']) ? $_POST['estimated_minutes'] : null;

if (empty($shipping_address) || empty($payment_method) || empty($total_amount)) {
    die("<p style='color:red;'>All fields are required.</p>");
}

$conn->begin_transaction();

try {
    // Get restaurant_id from cart
    $cartCheck = $conn->prepare("SELECT m.restaurant_id FROM cart c JOIN menu_items m ON c.menu_id = m.id WHERE c.user_id = ? LIMIT 1");
    $cartCheck->bind_param("i", $user_id);
    $cartCheck->execute();
    $cartRow = $cartCheck->get_result()->fetch_assoc();
    $restaurant_id = $cartRow['restaurant_id'];

    // Insert order
    $stmt = $conn->prepare("
        INSERT INTO orders 
            (user_id, total_amount, shipping_address, payment_method, restaurant_id, latitude, longitude, distance_km, estimated_minutes) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "idssidddi",
        $user_id,
        $total_amount,
        $shipping_address,
        $payment_method,
        $restaurant_id,
        $latitude,
        $longitude,
        $distance_km,
        $estimated_minutes
    );
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

    // Handle promo code usage
    if (isset($_SESSION['applied_code'])) {
        $promoId = $_SESSION['applied_code']['id'];

        $insertUsage = $conn->prepare("INSERT INTO code_usages (user_id, code_id) VALUES (?, ?)");
        $insertUsage->bind_param("ii", $user_id, $promoId);
        $insertUsage->execute();

        $decrement = $conn->prepare("UPDATE codes SET uses = uses - 1 WHERE id = ? AND uses > 0");
        $decrement->bind_param("i", $promoId);
        $decrement->execute();

        unset($_SESSION['applied_code']);
    }

    // Clear cart
    $clear = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $clear->bind_param("i", $user_id);
    $clear->execute();

    // NOTIFICATION THRESHOLD CHECK
    $threshStmt = $conn->prepare("SELECT * FROM notification_thresholds WHERE restaurant_id = ?");
    $threshStmt->bind_param("i", $restaurant_id);
    $threshStmt->execute();
    $threshold = $threshStmt->get_result()->fetch_assoc();

    if ($threshold) {
        // Get or create user loyalty progress
        $progressStmt = $conn->prepare("SELECT * FROM user_loyalty_progress WHERE user_id = ? AND restaurant_id = ?");
        $progressStmt->bind_param("ii", $user_id, $restaurant_id);
        $progressStmt->execute();
        $progress = $progressStmt->get_result()->fetch_assoc();

        if ($progress) {
            $newTotal = $progress['total_spent_since_last_reward'] + $total_amount;
            $updateProgress = $conn->prepare("UPDATE user_loyalty_progress SET total_spent_since_last_reward = ? WHERE user_id = ? AND restaurant_id = ?");
            $updateProgress->bind_param("dii", $newTotal, $user_id, $restaurant_id);
            $updateProgress->execute();
        } else {
            $newTotal = $total_amount;
            $insertProgress = $conn->prepare("INSERT INTO user_loyalty_progress (user_id, restaurant_id, total_spent_since_last_reward) VALUES (?, ?, ?)");
            $insertProgress->bind_param("iid", $user_id, $restaurant_id, $newTotal);
            $insertProgress->execute();
        }

        // Check if user crossed the threshold
        if ($newTotal >= $threshold['threshold']) {
            // Generate promo code
            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $randomPart = '';
            for ($i = 0; $i < 6; $i++) {
                $randomPart .= $characters[rand(0, strlen($characters) - 1)];
            }
            $promoCode = 'REWARD-' . $randomPart;

            // Set expiry date
            $expiryDate = date('Y-m-d H:i:s', strtotime('+' . $threshold['expiry_days'] . ' days'));

            // Insert promo code
            $codeInsert = $conn->prepare("INSERT INTO codes (code, discount, uses, expiry_date, restaurant_id, assigned_user_id) VALUES (?, ?, 1, ?, ?, ?)");
            $codeInsert->bind_param("sisii", $promoCode, $threshold['discount'], $expiryDate, $restaurant_id, $user_id);
            $codeInsert->execute();

            // Get restaurant name
            $restStmt = $conn->prepare("SELECT username FROM restaurants WHERE id = ?");
            $restStmt->bind_param("i", $restaurant_id);
            $restStmt->execute();
            $restName = $restStmt->get_result()->fetch_assoc()['username'];

            // Send notification
            $notifMessage = "You have earned a reward from " . $restName . "! Use code " . $promoCode . " for " . $threshold['discount'] . "% off your next order. Valid for " . $threshold['expiry_days'] . " days.";
            $notifInsert = $conn->prepare("INSERT INTO user_notifications (user_id, message, code) VALUES (?, ?, ?)");
            $notifInsert->bind_param("iss", $user_id, $notifMessage, $promoCode);
            $notifInsert->execute();

            // Reset spending tracker
            $resetProgress = $conn->prepare("UPDATE user_loyalty_progress SET total_spent_since_last_reward = 0 WHERE user_id = ? AND restaurant_id = ?");
            $resetProgress->bind_param("ii", $user_id, $restaurant_id);
            $resetProgress->execute();
        }
    }

    $conn->commit();

    $deliveryInfo = '';
    if ($distance_km && $estimated_minutes) {
        $deliveryInfo = "<p style='margin-top:10px; font-size:14px;'>" . htmlspecialchars($distance_km) . " km away - estimated delivery in ~" . htmlspecialchars($estimated_minutes) . " minutes</p>";
    }

    echo "
    <div style='max-width:400px; margin: 40px auto; padding: 20px; border-radius: 10px; background-color: #e6f4f1; 
                color: #2E4E50; font-family: Arial, sans-serif; text-align:center; box-shadow: 0 0 10px rgba(46, 78, 80, 0.2);'>
        <h2 style='margin-bottom: 20px;'>Order placed successfully!</h2>
        {$deliveryInfo}
        <a href='menu.php' style='
            display: inline-block; 
            padding: 12px 24px; 
            background-color: #2E4E50; 
            color: white; 
            border-radius: 8px; 
            text-decoration: none; 
            font-weight: 700;
            margin-top: 15px;
        ' onmouseover=\"this.style.backgroundColor='#1f3a3b'\" onmouseout=\"this.style.backgroundColor='#2E4E50'\">Back to Menu</a>
    </div>";

} catch (Exception $e) {
    $conn->rollback();
    echo "<p style='color:red; font-weight:bold; text-align:center; font-family: Arial, sans-serif; margin-top:40px;'>Error placing order: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>