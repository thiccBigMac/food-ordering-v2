<?php
include "auth/session.php";
require_once('db/connection.php');

// Handle mark as read - before any HTML output
if (isset($_GET['mark_read']) && isset($_SESSION['user_id'])) {
    $notif_id = intval($_GET['mark_read']);
    $user_id = $_SESSION['user_id'];
    $markStmt = $conn->prepare("UPDATE user_notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
    $markStmt->bind_param("ii", $notif_id, $user_id);
    $markStmt->execute();
    header("Location: notifications.php");
    exit();
}

// Handle delete/dismiss
if (isset($_GET['dismiss']) && isset($_SESSION['user_id'])) {
    $notif_id = intval($_GET['dismiss']);
    $user_id = $_SESSION['user_id'];
    $delStmt = $conn->prepare("DELETE FROM user_notifications WHERE id = ? AND user_id = ?");
    $delStmt->bind_param("ii", $notif_id, $user_id);
    $delStmt->execute();
    header("Location: notifications.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="styles/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        html, body { overflow-y: auto !important; height: auto !important; }

        .notif-container {
            max-width: 650px;
            margin: 40px auto 100px;
            padding: 0 20px;
        }

        .notif-container h2 {
            font-size: 22px;
            color: #2E4E50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #2E4E50;
        }

        .notif-card {
            background: white;
            border-radius: 8px;
            padding: 18px 20px;
            margin-bottom: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.07);
        }

        .notif-card.unread {
            background: #f9f9f9;
        }

        .notif-message {
            font-size: 14px;
            color: #333;
            margin-bottom: 10px;
            line-height: 1.5;
        }

        .notif-code {
            display: inline-block;
            background: #2E4E50;
            color: white;
            padding: 5px 12px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .notif-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
            flex-wrap: wrap;
            gap: 8px;
        }

        .notif-date {
            font-size: 12px;
            color: #999;
        }

        .btn-notif {
            background: transparent;
            border: 1px solid #2E4E50;
            color: #2E4E50;
            padding: 4px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-notif:hover {
            background: #2E4E50;
            color: white;
        }

        .btn-dismiss {
            border-color: #c0392b;
            color: #c0392b;
        }

        .btn-dismiss:hover {
            background: #c0392b;
            color: white;
        }

        .btn-group {
            display: flex;
            gap: 8px;
        }

        .notif-empty {
            text-align: center;
            color: #999;
            font-size: 15px;
            margin-top: 60px;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="notif-container">
        <h2>Notifications</h2>

        <?php
        if (!isset($_SESSION['user_id'])) {
            echo '<p class="notif-empty">Please login to view your notifications.</p>';
        } else {
            $user_id = $_SESSION['user_id'];

            $stmt = $conn->prepare("SELECT * FROM user_notifications WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $unreadClass = $row['is_read'] ? '' : 'unread';
                    echo '<div class="notif-card ' . $unreadClass . '">';
                    echo '<p class="notif-message">' . htmlspecialchars($row['message']) . '</p>';

                    if ($row['code']) {
                        echo '<div><span class="notif-code">' . htmlspecialchars($row['code']) . '</span></div>';
                    }

                    echo '<div class="notif-footer">';
                    echo '<span class="notif-date">' . date('d M Y, h:i A', strtotime($row['created_at'])) . '</span>';

                    echo '<div class="btn-group">';
                    if (!$row['is_read']) {
                        echo '<a href="notifications.php?mark_read=' . $row['id'] . '" class="btn-notif">Mark as read</a>';
                    }
                    echo '<a href="notifications.php?dismiss=' . $row['id'] . '" class="btn-notif btn-dismiss" onclick="return confirm(\'Remove this notification?\')">Remove</a>';
                    echo '</div>';

                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p class="notif-empty">No notifications yet.</p>';
            }
        }
        ?>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>