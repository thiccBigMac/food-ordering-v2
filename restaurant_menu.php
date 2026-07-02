<?php include "auth/session.php"; ?>
<?php
require_once('db/connection.php');

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$restaurant_id = intval($_GET['id']);

// Get restaurant info
$stmt = $conn->prepare("SELECT id, username, image FROM restaurants WHERE id = ?");
$stmt->bind_param("i", $restaurant_id);
$stmt->execute();
$restaurant = $stmt->get_result()->fetch_assoc();

if (!$restaurant) {
    die("Restaurant not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($restaurant['username']); ?> - Menu</title>
    <link rel="stylesheet" href="styles/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            overflow-y: auto !important;
            height: auto !important;
        }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="container pt-4 pb-5">

    <!-- Restaurant Header -->
    <div class="text-center mb-4">
        <?php if ($restaurant['image']): ?>
            <img src="assets/restaurants/<?php echo htmlspecialchars($restaurant['image']); ?>" 
                 alt="<?php echo htmlspecialchars($restaurant['username']); ?>"
                 style="height: 200px; width: 100%; object-fit: cover; border-radius: 12px; margin-bottom: 15px;">
        <?php endif; ?>
        <h2><?php echo htmlspecialchars($restaurant['username']); ?></h2>
    </div>

    <!-- Menu Items -->
    <div class="row g-4 justify-content-center">
        <?php
        $stmt = $conn->prepare("SELECT id, name, description, price, image FROM menu_items WHERE restaurant_id = ?");
        $stmt->bind_param("i", $restaurant_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="col-md-4 col-sm-6">';
                echo '  <div class="card h-100 shadow-sm">';
                echo '      <img src="assets/menu/' . htmlspecialchars($row['image']) . '" class="card-img-top" style="height:180px; object-fit:cover;">';
                echo '      <div class="card-body d-flex flex-column">';
                echo '          <h5 class="card-title">' . htmlspecialchars($row['name']) . '</h5>';
                echo '          <p class="card-text">' . htmlspecialchars($row['description']) . '</p>';
                echo '          <div class="mt-auto d-flex justify-content-between align-items-center">';
                echo '              <span class="fw-bold text-success">Rs. ' . htmlspecialchars($row['price']) . '</span>';
                echo '              <form method="POST" action="add_to_cart.php">';
                echo '                  <input type="hidden" name="menu_item_id" value="' . $row['id'] . '">';
                echo '                  <input type="hidden" name="restaurant_id" value="' . $restaurant_id . '">';
                echo '                  <button type="submit" class="btn btn-primary btn-sm">Add to Cart</button>';
                echo '              </form>';
                echo '          </div>';
                echo '      </div>';
                echo '  </div>';
                echo '</div>';
            }
        } else {
            echo '<div class="col-12 text-center"><p class="text-muted">No menu items available yet.</p></div>';
        }
        ?>
    </div>

    <!-- Back Button -->
    <div class="text-center mt-4">
        <a href="index.php" class="btn btn-secondary">← Back to Restaurants</a>
    </div>

</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>