<?php include "auth/session.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurants — HamroKhaja</title>
    <link rel="stylesheet" href="/food-ordering/styles/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .restaurant-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .restaurant-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            color: inherit;
        }
        .restaurant-card .card-banner {
            height: 120px;
            background: linear-gradient(135deg, #2E4E50, #4a7c7e);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
        }
        .restaurant-card .card-body {
            padding: 16px;
        }
        .restaurant-card h5 {
            font-weight: 700;
            color: #2E4E50;
            margin-bottom: 6px;
        }
        .restaurant-card p {
            font-size: 13px;
            color: #888;
            margin: 0;
        }
        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #2E4E50;
            margin: 30px 0 20px;
            text-align: center;
        }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="container py-4" style="margin-bottom: 80px;">
    <h2 class="page-title"> Available Restaurants</h2>

    <div class="row g-4 justify-content-center">
        <?php
        require_once('db/connection.php');
        $result = $conn->query("SELECT id, username, email, image FROM restaurants ORDER BY id ASC");

        if ($result && $result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
                // Count menu items for this restaurant
                $countStmt = $conn->prepare("SELECT COUNT(*) as c FROM menu_items WHERE restaurant_id = ?");
                $countStmt->bind_param("i", $row['id']);
                $countStmt->execute();
                $menuCount = $countStmt->get_result()->fetch_assoc()['c'];
        ?>
        <div class="col-md-4 col-sm-6">
            <a href="restaurant_menu.php?id=<?php echo $row['id']; ?>" class="restaurant-card">
                <div class="card-banner" style="height:120px; overflow:hidden;">
    <img src="assets/restaurants/<?php echo htmlspecialchars($row['image'] ?? ''); ?>" 
         alt="<?php echo htmlspecialchars($row['username']); ?>"
         style="width:100%; height:100%; object-fit:cover;">
</div>
                <div class="card-body">
                    <h5><?php echo htmlspecialchars($row['username']); ?></h5>
                    <p><i class="bi bi-grid"></i> <?php echo $menuCount; ?> items available</p>
                </div>
            </a>
        </div>
        <?php
            endwhile;
        else:
            echo '<p class="text-center text-muted">No restaurants available.</p>';
        endif;
        ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>