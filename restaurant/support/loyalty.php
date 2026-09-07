<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Loyalty Discount</title>
    <link href="/food-ordering/restaurant/styles/style.css" rel="stylesheet" />
        <style>
        @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Work+Sans:wght@400;500;600;700&display=swap');

        .content h3 {
            font-family: 'Fraunces', Georgia, serif;
            color: #2E4E50;
            margin-bottom: 14px;
        }

        form {
            font-size: 14px;
            max-width: 400px;
        }
        form label {
            font-weight: 600;
            margin-bottom: 6px;
            display: block;
            color: #6B6355;
        }
        form input[type="number"] {
            font-size: 14px;
            padding: 10px 12px;
            margin-bottom: 14px;
            color: #2B2620;
            background-color: #FBF7EF;
            border: 1.5px solid #e6ddc9;
            border-radius: 8px;
            outline: none;
            width: 100%;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        form input[type="number"]:focus {
            border-color: #D9A441;
            box-shadow: 0 0 6px rgba(217, 164, 65, 0.35);
        }
        form button[type="submit"] {
            font-size: 15px;
            font-weight: 700;
            padding: 12px 0;
            background-color: #D9A441;
            color: #2B2620;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.25s ease, color 0.25s ease;
        }
        form button[type="submit"]:hover {
            background-color: #2E4E50;
            color: white;
        }
        .message {
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 14px;
            padding: 10px 14px;
            border-radius: 8px;
            display: inline-block;
        }
        .message.success { color: #3d6b47; background: #eaf4ec; }
        .message.error { color: #a94a45; background: #fbeceb; }
        .current-rule {
            background: #F3ECDD;
            padding: 18px;
            border-radius: 12px;
            margin-bottom: 22px;
            font-size: 14px;
            color: #2B2620;
            border-left: 4px solid #D9A441;
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
                    <button type="submit" name="disable_rule" style="background-color:#C0605A;" class="btn">Disable Rule</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>