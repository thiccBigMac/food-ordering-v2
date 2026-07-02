<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Loyalty Discount</title>
    <link href="/food-ordering/restaurant/styles/style.css" rel="stylesheet" />
    <style>
        form {
            font-size: 14px;
            max-width: 400px;
        }
        form label {
            font-weight: 600;
            margin-bottom: 6px;
            display: block;
            color: #444;
        }
        form input[type="number"] {
            font-size: 14px;
            padding: 8px 10px;
            margin-bottom: 12px;
            border: 1.5px solid #ccc;
            border-radius: 6px;
            outline: none;
            width: 100%;
        }
        form input[type="number"]:focus {
            border-color: #2E4E50;
            box-shadow: 0 0 6px rgba(46, 78, 80, 0.3);
        }
        form button[type="submit"] {
            font-size: 14px;
            font-weight: 700;
            padding: 10px 0;
            background-color: #2E4E50;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
        }
        form button[type="submit"]:hover {
            background-color: #1f3a3b;
        }
        .message {
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 14px;
        }
        .message.success { color: green; }
        .message.error { color: #d9534f; }
        .current-rule {
            background: #f4f4f4;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include "../auth/session.php"; ?>
    <?php include '../sidebar.php'; ?>
    <div class="main-content">
        <div class="topbar">Loyalty Discount</div>
        <div class="content">
            <?php
            require_once('../../db/connection.php');
            $restaurant_id = $_SESSION['admin_id'];
            $message = '';

            // Handle save/update
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_rule'])) {
                $threshold = (int) $_POST['threshold'];
                $discount = (int) $_POST['discount'];

                if ($threshold <= 0 || $discount <= 0 || $discount > 100) {
                    $message = "Please enter a valid threshold and discount (1-100%).";
                } else {
                    // Check if a rule already exists for this restaurant
                    $check = $conn->prepare("SELECT id FROM loyalty_rules WHERE restaurant_id = ? AND type = 'amount'");
                    $check->bind_param("i", $restaurant_id);
                    $check->execute();
                    $existing = $check->get_result()->fetch_assoc();

                    if ($existing) {
                        $update = $conn->prepare("UPDATE loyalty_rules SET threshold = ?, discount = ?, active = 1 WHERE id = ?");
                        $update->bind_param("iii", $threshold, $discount, $existing['id']);
                        $update->execute();
                    } else {
                        $insert = $conn->prepare("INSERT INTO loyalty_rules (restaurant_id, type, threshold, discount, active) VALUES (?, 'amount', ?, ?, 1)");
                        $insert->bind_param("iii", $restaurant_id, $threshold, $discount);
                        $insert->execute();
                    }
                    $message = "Loyalty rule saved successfully.";
                }
            }

            // Handle disable
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['disable_rule'])) {
                $disable = $conn->prepare("UPDATE loyalty_rules SET active = 0 WHERE restaurant_id = ? AND type = 'amount'");
                $disable->bind_param("i", $restaurant_id);
                $disable->execute();
                $message = "Loyalty rule disabled.";
            }

            // Fetch current rule
            $stmt = $conn->prepare("SELECT threshold, discount, active FROM loyalty_rules WHERE restaurant_id = ? AND type = 'amount'");
            $stmt->bind_param("i", $restaurant_id);
            $stmt->execute();
            $rule = $stmt->get_result()->fetch_assoc();
            ?>

            <?php if ($message): ?>
                <p class="message <?= (strpos($message, 'disabled') !== false || strpos($message, 'valid') !== false) ? 'error' : 'success' ?>">
                    <?= htmlspecialchars($message) ?>
                </p>
            <?php endif; ?>

            <?php if ($rule): ?>
                <div class="current-rule">
                    <strong>Current Rule:</strong><br>
                    Spend Rs. <?= number_format($rule['threshold'], 0) ?> or more → get <?= $rule['discount'] ?>% off automatically.<br>
                    Status: <strong><?= $rule['active'] ? 'Active' : 'Disabled' ?></strong>
                </div>
            <?php endif; ?>

            <h3>Set Auto-Discount Rule</h3>
            <form method="POST">
                <label for="threshold">Minimum Order Amount (Rs.)</label>
                <input type="number" name="threshold" id="threshold" min="1" required value="<?= $rule ? $rule['threshold'] : '' ?>">

                <label for="discount">Discount (%)</label>
                <input type="number" name="discount" id="discount" min="1" max="100" required value="<?= $rule ? $rule['discount'] : '' ?>">

                <button type="submit" name="save_rule">Save Rule</button>
            </form>

            <?php if ($rule && $rule['active']): ?>
                <form method="POST" style="margin-top: 10px;">
                    <button type="submit" name="disable_rule" style="background-color:#e57373;" class="btn">Disable Rule</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>