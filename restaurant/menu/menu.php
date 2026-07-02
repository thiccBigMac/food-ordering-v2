<?php include "../auth/session.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link href="/food-ordering/restaurant/styles/style.css" rel="stylesheet">
</head>
<body>
    <?php include '../sidebar.php'; ?>

    <div class="main-content">
        <div class="topbar">Menu</div>
        <div class="content">
            <a class="menu-btn" href="add.php">Add</a>
            <table class="menu-table">
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>

                <?php
                require_once('../../db/connection.php');
                $restaurant_id = $_SESSION['admin_id'];
                

                $stmt = $conn->prepare("SELECT id, name, description, price, image FROM menu_items WHERE restaurant_id = ?");
                $stmt->bind_param("i", $restaurant_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['description']) . '</td>';
                        echo '<td>Rs. ' . htmlspecialchars($row['price']) . '</td>';

                        echo '<td>';
                        echo '<form method="POST" action="view.php">
                                <input type="hidden" name="menu_item_id" value="' . $row['id'] . '">
                                <button type="submit" class="btn btn-sm">View</button>
                              </form>';

                        echo '<form method="POST" action="edit.php">
                                <input type="hidden" name="menu_item_id" value="' . $row['id'] . '">
                                <button type="submit" class="btn btn-sm">Edit</button>
                              </form>';

                        echo '<form method="POST" action="delete.php" onsubmit="return confirm(\'Delete this item?\');">
                                <input type="hidden" name="menu_item_id" value="' . $row['id'] . '">
                                <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                              </form>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="4">No menu items found.</td></tr>';
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>