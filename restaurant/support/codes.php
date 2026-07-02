<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Codes</title>
    <link href="/food-ordering/restaurant/styles/style.css" rel="stylesheet" />
    <style>
        form { font-size: 14px; }
        form label { font-weight: 600; margin-bottom: 6px; display: block; color: #444; }
        form input[type="text"],
        form input[type="number"],
        form input[type="datetime-local"] {
            font-size: 14px; padding: 8px 10px; margin-bottom: 12px;
            border: 1.5px solid #ccc; border-radius: 6px; outline: none;
            width: 100%; transition: border-color 0.3s ease;
        }
        form input:focus { border-color: #2E4E50; box-shadow: 0 0 6px rgba(46,78,80,0.3); }
        form button[type="submit"] {
            font-size: 14px; font-weight: 700; padding: 10px 0;
            background-color: #2E4E50; color: white; border: none;
            border-radius: 8px; cursor: pointer; width: 100%;
            transition: background-color 0.3s ease;
        }
        form button[type="submit"]:hover { background-color: #1f3a3b; }
        table.menu-table { font-size: 13px; }
        .delete-btn {
            background-color: rgba(50,100,0,0.5); color: white; border: none;
            padding: 4px 8px; cursor: pointer; border-radius: 4px; font-size: 13px;
        }
        .delete-btn:hover { background-color: rgba(0,0,0,0.4); }
        .message { font-weight: 600; margin-bottom: 15px; font-size: 14px; }
        .message.success { color: green; }
        .message.error { color: #d9534f; }
        .section-box {
            background: white; border-radius: 10px; padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 25px;
        }
        .section-box h3 { margin-bottom: 15px; color: #2E4E50; font-size: 16px; }
        hr { margin: 25px 0; border: none; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <?php include "../auth/session.php"; ?>
    <?php include '../sidebar.php'; ?>
    <div class="main-content">
        <div class="topbar">Codes</div>
        <div class="content">
            <?php
            require_once('../../db/connection.php');
            $restaurant_id = $_SESSION['admin_id'];

            function generateCode($prefix = 'PROMO-', $length = 6) {
                $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                $randomPart = '';
                for ($i = 0; $i < $length; $i++) {
                    $randomPart .= $characters[rand(0, strlen($characters) - 1)];
                }
                return $prefix . $randomPart;
            }

            // Handle add code
            if (isset($_POST['add_code'])) {
                $code = !empty($_POST['code']) ? $_POST['code'] : generateCode();
                $discount = (int) $_POST['discount'];
                $uses = (int) $_POST['uses'];
                $expiry_date = $_POST['expiry_date'];

                $now = date("Y-m-d\TH:i");
                if ($expiry_date <= $now) {
                    $message = "Expiry date must be in the future.";
                } else {
                    $stmt = $conn->prepare("INSERT INTO codes (code, discount, uses, expiry_date, restaurant_id) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("siisi", $code, $discount, $uses, $expiry_date, $restaurant_id);
                    $message = $stmt->execute() ? "Code added successfully." : "Error adding code: " . $stmt->error;
                    $stmt->close();
                }
            }

            // Handle delete code
            if (isset($_POST['delete_code'], $_POST['code_id'])) {
                $code_id = (int) $_POST['code_id'];
                $conn->query("DELETE FROM code_usages WHERE code_id = $code_id");
                $stmt = $conn->prepare("DELETE FROM codes WHERE id = ?");
                $stmt->bind_param("i", $code_id);
                $message = $stmt->execute() ? "Code deleted successfully." : "Error deleting code.";
                $stmt->close();
            }

            // Handle save notification threshold
            if (isset($_POST['save_threshold'])) {
                $threshold = (int) $_POST['threshold'];
                $notif_discount = (int) $_POST['notif_discount'];
                $notif_expiry = $_POST['notif_expiry'];

                if ($threshold <= 0 || $notif_discount <= 0 || $notif_discount > 100) {
                    $threshold_message = "Please enter valid threshold and discount values.";
                } else {
                    // Check if threshold already exists for this restaurant
                    $check = $conn->prepare("SELECT id FROM notification_thresholds WHERE restaurant_id = ?");
                    $check->bind_param("i", $restaurant_id);
                    $check->execute();
                    $existing = $check->get_result()->fetch_assoc();

                    if ($existing) {
                        $upd = $conn->prepare("UPDATE notification_thresholds SET threshold = ?, discount = ?, expiry_days = ? WHERE restaurant_id = ?");
                        $upd->bind_param("iiii", $threshold, $notif_discount, $notif_expiry, $restaurant_id);
                        $upd->execute();
                    } else {
                        $ins = $conn->prepare("INSERT INTO notification_thresholds (restaurant_id, threshold, discount, expiry_days) VALUES (?, ?, ?, ?)");
                        $ins->bind_param("iiii", $restaurant_id, $threshold, $notif_discount, $notif_expiry);
                        $ins->execute();
                    }
                    $threshold_message = "Notification threshold saved!";
                }
            }

            // Get current threshold
            $threshStmt = $conn->prepare("SELECT * FROM notification_thresholds WHERE restaurant_id = ?");
            $threshStmt->bind_param("i", $restaurant_id);
            $threshStmt->execute();
            $currentThreshold = $threshStmt->get_result()->fetch_assoc();
            ?>

            <?php if (isset($message)): ?>
                <p class="message <?= strpos($message, 'Error') === false ? 'success' : 'error' ?>">
                    <?= htmlspecialchars($message) ?>
                </p>
            <?php endif; ?>

            <!-- NOTIFICATION THRESHOLD SECTION -->
            <div class="section-box">
                <h3>🔔 Auto Promo Code Notification</h3>
                <p style="font-size:13px; color:#666; margin-bottom:15px;">
                    When a user spends a certain amount at your restaurant, they will automatically receive a promo code in their notifications.
                </p>

                <?php if (isset($threshold_message)): ?>
                    <p class="message success"><?= htmlspecialchars($threshold_message) ?></p>
                <?php endif; ?>

                <form method="POST">
                    <label>Minimum Spend (Rs.)</label>
                    <input type="number" name="threshold" min="1" required
                           value="<?= $currentThreshold['threshold'] ?? '' ?>"
                           placeholder="e.g. 5000">

                    <label>Discount to Give (%)</label>
                    <input type="number" name="notif_discount" min="1" max="100" required
                           value="<?= $currentThreshold['discount'] ?? '' ?>"
                           placeholder="e.g. 10">

                    <label>Code Valid For (days)</label>
                    <input type="number" name="notif_expiry" min="1" required
                           value="<?= $currentThreshold['expiry_days'] ?? '' ?>"
                           placeholder="e.g. 30">

                    <button type="submit" name="save_threshold">Save Threshold</button>
                </form>
            </div>

            <hr />

            <!-- ADD CODE SECTION -->
            <div class="section-box">
                <h3>Add New Discount Code</h3>
                <form method="POST">
                    <label>Code (optional — leave empty to autogenerate)</label>
                    <input type="text" name="code" />

                    <label>Discount (%)</label>
                    <input type="number" name="discount" min="1" max="100" required />

                    <label>Uses</label>
                    <input type="number" name="uses" min="1" required />

                    <label>Expiry Date</label>
                    <input type="datetime-local" name="expiry_date" required />

                    <button type="submit" name="add_code">Add Code</button>
                </form>
            </div>

            <hr />

            <!-- CODES TABLE -->
            <div class="section-box">
                <h3>Existing Codes</h3>
                <table class="menu-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Discount</th>
                            <th>Uses Left</th>
                            <th>Expiry Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $conn->query("SELECT * FROM codes WHERE restaurant_id = $restaurant_id ORDER BY id DESC");
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['code']) . "</td>";
                                echo "<td>" . intval($row['discount']) . "%</td>";
                                echo "<td>" . intval($row['uses']) . "</td>";
                                echo "<td>" . date("Y-m-d H:i", strtotime($row['expiry_date'])) . "</td>";
                                echo "<td>
                                        <form method='POST' onsubmit=\"return confirm('Delete this code?');\">
                                            <input type='hidden' name='code_id' value='{$row['id']}'>
                                            <button type='submit' name='delete_code' class='delete-btn'>Remove</button>
                                        </form>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' style='text-align:center;'>No codes found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</body>
</html>