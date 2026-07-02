<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>View Menu Item</title>
  <link href="/food-ordering/restaurant/styles/style.css" rel="stylesheet" />
  <style>
    /* Hide sidebar */
    .sidebar {
      display: none !important;
    }

    /* Reset body layout */
    body {
      display: block !important;
      margin: 0;
      padding: 0;
      background-color: #f9f9f9;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Center main content */
    .main-content {
      max-width: 480px;
      margin: 40px auto;
      padding: 30px 40px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    /* Image styling */
    .main-content img {
      max-width: 100%;
      border-radius: 6px;
      margin-top: 15px;
      display: block;
    }

    /* Text styling */
    .main-content h2 {
      margin-top: 0;
      font-weight: 700;
      color: #222;
      text-align: center;
    }

    .main-content p {
      color: #555;
      font-size: 16px;
      line-height: 1.4;
      margin: 10px 0;
      text-align: center;
    }
  </style>
</head>
<body>

  <?php include "../auth/session.php"; ?>
  <?php include '../sidebar.php'; ?>

  <div class="main-content">
    <?php
      require_once('../../db/connection.php');

      if (isset($_POST['menu_item_id'])) {
        $menu_item_id = intval($_POST['menu_item_id']);

        $stmt = $conn->prepare("SELECT id, name, description, price, image FROM menu_items WHERE id = ?");
        $stmt->bind_param("i", $menu_item_id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
          $row = $result->fetch_assoc();

          echo '<h2>' . htmlspecialchars($row['name']) . '</h2>';
          echo '<p>' . htmlspecialchars($row['description']) . '</p>';
          echo '<p>Price: Rs. ' . htmlspecialchars($row['price']) . '</p>';
          echo '<img src="../../assets/menu/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '">';
        } else {
          echo '<p style="text-align:center;color:red;">Menu item not found.</p>';
        }

        $stmt->close();
      } else {
        echo '<p style="text-align:center;color:red;">Invalid request.</p>';
      }
    ?>
    <br/>
    <a href = "menu.php" style ="text-decoration : none">Go back to menu</a>
  </div>

</body>
</html>
