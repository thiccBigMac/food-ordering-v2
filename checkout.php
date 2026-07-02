<?php
session_start();
require_once('db/connection.php');
require_once('includes/haversine.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart items and calculate total
$query = "
    SELECT c.menu_id, m.name, m.price, m.image, m.restaurant_id, c.quantity
    FROM cart c
    JOIN menu_items m ON c.menu_id = m.id
    WHERE c.user_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
$total = 0;
$restaurant_id = null;
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
    $total += $row['price'] * $row['quantity'];
    $restaurant_id = $row['restaurant_id'];
}

if (empty($items)) {
    echo "Your cart is empty.";
    exit();
}

// Get the restaurant's location
$restLat = null;
$restLng = null;
if ($restaurant_id) {
    $restStmt = $conn->prepare("SELECT latitude, longitude, username FROM restaurants WHERE id = ?");
    $restStmt->bind_param("i", $restaurant_id);
    $restStmt->execute();
    $restData = $restStmt->get_result()->fetch_assoc();
    $restLat = $restData['latitude'] ?? null;
    $restLng = $restData['longitude'] ?? null;
    $restName = $restData['username'] ?? '';
}

// Initialize discount variables
$discountPercent = 0;
$discountCode = '';
$discountAmount = 0;
$message = '';

// Handle promo code application
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_code'])) {
    $inputCode = strtoupper(trim($_POST['promo_code']));
    $now = date("Y-m-d H:i:s");

    $stmt2 = $conn->prepare("SELECT id, discount, uses, expiry_date FROM codes WHERE UPPER(code) = ? AND uses > 0 AND expiry_date > ?");
    $stmt2->bind_param("ss", $inputCode, $now);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    if ($code = $result2->fetch_assoc()) {
        $code_id = $code['id'];
        $usageCheck = $conn->prepare("SELECT id FROM code_usages WHERE user_id = ? AND code_id = ?");
        $usageCheck->bind_param("ii", $user_id, $code_id);
        $usageCheck->execute();
        $usageResult = $usageCheck->get_result();

        if ($usageResult->num_rows > 0) {
            $message = "You have already used this promo code.";
        } else {
            $discountPercent = $code['discount'];
            $discountCode = $inputCode;

            $_SESSION['applied_code'] = [
                'id' => $code_id,
                'code' => $discountCode,
                'discount' => $discountPercent
            ];

            $message = "Promo code applied!";
        }
    } else {
        $message = "Invalid or expired promo code.";
    }
} elseif (isset($_SESSION['applied_code'])) {
    $code_id = $_SESSION['applied_code']['id'];
    $now = date("Y-m-d H:i:s");

    $recheck = $conn->prepare("SELECT discount, uses, expiry_date FROM codes WHERE id = ? AND uses > 0 AND expiry_date > ?");
    $recheck->bind_param("is", $code_id, $now);
    $recheck->execute();
    $recheckResult = $recheck->get_result();

    if ($validCode = $recheckResult->fetch_assoc()) {
        $discountPercent = $validCode['discount'];
        $discountCode = $_SESSION['applied_code']['code'];
    } else {
        unset($_SESSION['applied_code']);
        $message = "Your promo code has expired and was removed.";
    }
}

// Apply manual promo code discount first
$discountAmount = ($total * $discountPercent) / 100;
$afterPromoTotal = $total - $discountAmount;

// Check for an active loyalty auto-discount from this restaurant
$loyaltyDiscountPercent = 0;
$loyaltyDiscountAmount = 0;
$loyaltyApplied = false;

if ($restaurant_id) {
    $loyaltyStmt = $conn->prepare("SELECT threshold, discount FROM loyalty_rules WHERE restaurant_id = ? AND type = 'amount' AND active = 1");
    $loyaltyStmt->bind_param("i", $restaurant_id);
    $loyaltyStmt->execute();
    $loyaltyRule = $loyaltyStmt->get_result()->fetch_assoc();

    if ($loyaltyRule && $total >= $loyaltyRule['threshold']) {
        $loyaltyDiscountPercent = $loyaltyRule['discount'];
        $loyaltyDiscountAmount = ($afterPromoTotal * $loyaltyDiscountPercent) / 100;
        $loyaltyApplied = true;
    }
}

// Final total after both discounts stacked
$finalTotal = $afterPromoTotal - $loyaltyDiscountAmount;

// Store checkout info in session for eSewa
$_SESSION['checkout_total'] = $finalTotal;
$_SESSION['checkout_shipping'] = $_POST['shipping_address'] ?? '';

// Generate eSewa transaction ID
$uid = uniqid('TXN-', true);
$_SESSION['esewa_txn_id'] = $uid;

// eSewa signature
$merchant_id = "EPAYTEST";
$secret = "8gBm/:&EnhH.1/q";
$data = "total_amount={$finalTotal},transaction_uuid={$uid},product_code={$merchant_id}";
$signature = base64_encode(hash_hmac('sha256', $data, $secret, true));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Checkout</title>
    <link rel="stylesheet" href="styles/style.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        html, body {
            overflow-y: auto !important;
            height: auto !important;
        }
        #delivery-info {
            display: none;
            background: #e6f4f1;
            color: #2E4E50;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .container {
            padding-bottom: 80px !important;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container mt-4 mb-5" style="max-width: 700px;">
        <h2>Checkout Summary</h2>

        <!-- Order Items -->
        <ul class="list-group mb-3">
            <?php foreach ($items as $item): ?>
                <li class="list-group-item d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <img src="assets/menu/<?php echo htmlspecialchars($item['image']); ?>" 
                             style="height:50px; width:auto; margin-right:15px; border-radius:6px;">
                        <span><?php echo htmlspecialchars($item['name']); ?> x <?php echo $item['quantity']; ?></span>
                    </div>
                    <span>Rs. <?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                </li>
            <?php endforeach; ?>

            <li class="list-group-item d-flex justify-content-between">
                <strong>Subtotal</strong>
                <strong>Rs. <?php echo number_format($total, 2); ?></strong>
            </li>

            <?php if ($discountPercent > 0): ?>
                <li class="list-group-item d-flex justify-content-between text-success">
                    Promo (<?php echo htmlspecialchars($discountCode); ?>) - <?php echo $discountPercent; ?>% Off
                    <span>- Rs. <?php echo number_format($discountAmount, 2); ?></span>
                </li>
            <?php endif; ?>

            <?php if ($loyaltyApplied): ?>
                <li class="list-group-item d-flex justify-content-between text-success">
                    🎉 Loyalty Discount - <?php echo $loyaltyDiscountPercent; ?>% Off
                    <span>- Rs. <?php echo number_format($loyaltyDiscountAmount, 2); ?></span>
                </li>
            <?php endif; ?>

            <li class="list-group-item d-flex justify-content-between">
                <strong>Total</strong>
                <strong>Rs. <?php echo number_format($finalTotal, 2); ?></strong>
            </li>
        </ul>

        <!-- Promo Code -->
        <form method="POST" class="mb-4 d-flex gap-2">
            <input type="text" name="promo_code" class="form-control" 
                   placeholder="Enter Promo Code" 
                   value="<?php echo htmlspecialchars($discountCode); ?>">
            <button type="submit" name="apply_code" class="btn btn-primary">Apply</button>
        </form>

        <?php if ($message): ?>
            <div class="alert <?php echo ($discountPercent > 0) ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Delivery Location Map -->
        <h4 class="mb-2">Delivery Location</h4>

        <?php if (!$restLat || !$restLng): ?>
            <div class="alert alert-warning">
                ⚠️ This restaurant hasn't set their location yet, so we can't calculate delivery distance/time. You can still place your order normally.
            </div>
        <?php else: ?>
            <div id="map-picker" style="height: 350px; border-radius: 8px; margin-bottom: 10px;"></div>
            <p style="font-size: 13px; color: #666;">Click on the map to set your delivery location.</p>

            <div id="delivery-info"></div>
        <?php endif; ?>

        <!-- Single shared form for both COD and eSewa -->
        <form action="place_order.php" method="POST" id="main-form">
            <input type="hidden" name="total_amount" value="<?php echo $finalTotal; ?>">
            <input type="hidden" name="promo_code" value="<?php echo htmlspecialchars($discountCode); ?>">
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            <input type="hidden" name="distance_km" id="distance_km">
            <input type="hidden" name="estimated_minutes" id="estimated_minutes">

            <div class="mb-3">
                <label for="shipping_address" class="form-label">Shipping Address (landmark/note)</label>
                <textarea id="shipping_address" name="shipping_address" 
                          class="form-control" rows="3" required placeholder="e.g. Near XYZ Chowk, blue gate"></textarea>
            </div>

            <div class="mb-3">
                <label for="payment_method" class="form-label">Payment Method</label>
                <select id="payment_method" name="payment_method" class="form-select" onchange="togglePaymentBtn(this.value)">
                    <option value="cash_on_delivery">Cash on Delivery</option>
                    <option value="esewa">eSewa</option>
                </select>
            </div>

            <!-- COD Button -->
            <div id="cod-section">
                <button type="submit" class="btn btn-success">Place Order</button>
            </div>

            <!-- eSewa Button -->
            <div id="esewa-section" style="display:none;">
                <button type="button" class="btn btn-success" onclick="submitEsewa()">
                    Pay with eSewa
                </button>
            </div>
        </form>

        <!-- eSewa Hidden Form -->
        <form id="esewa-form" 
              action="https://rc-epay.esewa.com.np/api/epay/main/v2/form" 
              method="POST">
            <input type="hidden" name="amount" value="<?php echo $finalTotal; ?>">
            <input type="hidden" name="tax_amount" value="0">
            <input type="hidden" name="total_amount" value="<?php echo $finalTotal; ?>">
            <input type="hidden" name="transaction_uuid" value="<?php echo $uid; ?>">
            <input type="hidden" name="product_code" value="EPAYTEST">
            <input type="hidden" name="product_service_charge" value="0">
            <input type="hidden" name="product_delivery_charge" value="0">
            <input type="hidden" name="success_url" value="http://localhost/food-ordering/esewa_success.php">
            <input type="hidden" name="failure_url" value="http://localhost/food-ordering/esewa_failure.php">
            <input type="hidden" name="signed_field_names" value="total_amount,transaction_uuid,product_code">
            <input type="hidden" name="signature" value="<?php echo $signature; ?>">
        </form>
    </div>

    <script>
    function togglePaymentBtn(value) {
        if (value === 'esewa') {
            document.getElementById('cod-section').style.display = 'none';
            document.getElementById('esewa-section').style.display = 'block';
        } else {
            document.getElementById('cod-section').style.display = 'block';
            document.getElementById('esewa-section').style.display = 'none';
        }
    }

    function submitEsewa() {
        const shipping = document.getElementById('shipping_address').value;
        if (!shipping.trim()) {
            alert('Please enter your shipping address first!');
            return;
        }

        fetch('save_shipping.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'shipping_address=' + encodeURIComponent(shipping)
        }).then(() => {
            document.getElementById('esewa-form').submit();
        });
    }

    <?php if ($restLat && $restLng): ?>
    // Restaurant's fixed coordinates (from PHP)
    const restLat = <?php echo $restLat; ?>;
    const restLng = <?php echo $restLng; ?>;

    const map = L.map('map-picker').setView([restLat, restLng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Mark the restaurant's location
    L.marker([restLat, restLng], {
        icon: L.icon({
            iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41]
        })
    }).addTo(map).bindPopup("Restaurant location").openPopup();

    let customerMarker;

    // Haversine formula in JavaScript (mirrors the PHP version for instant feedback)
    function haversineKm(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat / 2) ** 2 +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon / 2) ** 2;
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    map.on('click', function (e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;

        if (customerMarker) {
            customerMarker.setLatLng(e.latlng);
        } else {
            customerMarker = L.marker(e.latlng).addTo(map);
        }

        document.getElementById('latitude').value = lat.toFixed(8);
        document.getElementById('longitude').value = lng.toFixed(8);

        const distance = haversineKm(restLat, restLng, lat, lng);
        const minutes = Math.ceil((distance / 20) * 60 + 10); // 20 km/h avg + 10 min prep buffer

        document.getElementById('distance_km').value = distance.toFixed(2);
        document.getElementById('estimated_minutes').value = minutes;

        const infoBox = document.getElementById('delivery-info');
        infoBox.style.display = 'block';
        infoBox.innerHTML = ` Distance: ${distance.toFixed(2)} km —  Estimated delivery: ~${minutes} minutes`;
    });
    <?php endif; ?>
    </script>

    <?php include 'includes/footer.php'; ?>
</body>
</html>