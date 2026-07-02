<?php include "auth/session.php" ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HamroKhaja</title>
    <link rel="stylesheet" href="styles/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Override to allow scrolling on home page */
        html, body {
            overflow-y: auto !important;
            height: auto !important;
        }

        .restaurants-section {
            padding: 40px;
            background-color: #f9f9f9;
        }

        .restaurants-section h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2E4E50;
            font-size: 28px;
        }

        .restaurant-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            padding-bottom: 100px;
        }

        .restaurant-card {
            background-color: #fff;
            border-radius: 12px;
            width: 250px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            overflow: hidden;
            text-decoration: none;
            color: #222;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .restaurant-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .restaurant-card img {
            width: 100%;
            height: 160px;
            object-fit: cover;
        }

        .restaurant-card .no-image {
            width: 100%;
            height: 160px;
            background-color: #2E4E50;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 40px;
        }

        .restaurant-card .card-info {
            padding: 15px;
            text-align: center;
        }

        .restaurant-card .card-info h3 {
            font-size: 18px;
            color: #2E4E50;
        }

        /* Scroll down arrow */
        .scroll-down {
            position: absolute;
            bottom: 80px;
            left: 50%;
            transform: translateX(-50%);
            color: white;
            font-size: 14px;
            text-align: center;
            animation: bounce 2s infinite;
            z-index: 3;
            cursor: pointer;
        }

        .scroll-down i {
            font-size: 30px;
            display: block;
        }

        @keyframes bounce {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            50% { transform: translateX(-50%) translateY(-10px); }
        }

        /* Make carousel not full viewport so page is scrollable */
        .carousel {
            height: 90vh !important;
            position: relative;
        }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<!-- CAROUSEL -->
<div class="carousel">
    <div class="carousel-track">
        <div class="carousel-slide"><img src="assets/1.jpg" alt="Image 1"></div>
        <div class="carousel-slide"><img src="assets/2.jpg" alt="Image 2"></div>
        <div class="carousel-slide"><img src="assets/3.jpg" alt="Image 3"></div>
        <div class="carousel-slide"><img src="assets/4.jpg" alt="Image 4"></div>
        <div class="carousel-slide"><img src="assets/5.jpg" alt="Image 5"></div>
        <div class="carousel-slide"><img src="assets/6.jpg" alt="Image 6"></div>
    </div>

    <!-- Scroll down arrow -->
    <div class="scroll-down" onclick="document.getElementById('restaurants').scrollIntoView({behavior: 'smooth'})">
        <span>Scroll Down</span>
        <i class="bi bi-chevron-double-down"></i>
    </div>
</div>

<!-- RESTAURANTS SECTION -->
<div class="restaurants-section" id="restaurants">
    <h2>Our Restaurants</h2>
    <div class="restaurant-grid">
        <?php
        require_once('db/connection.php');
        $result = $conn->query("SELECT id, username, image FROM restaurants");

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<a href="restaurant_menu.php?id=' . $row['id'] . '" class="restaurant-card">';

                if ($row['image']) {
                    echo '<img src="assets/restaurants/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['username']) . '">';
                } else {
                    echo '<div class="no-image"><i class="bi bi-shop"></i></div>';
                }

                echo '<div class="card-info">';
                echo '<h3>' . htmlspecialchars($row['username']) . '</h3>';
                echo '</div>';
                echo '</a>';
            }
        } else {
            echo '<p style="text-align:center;">No restaurants available yet.</p>';
        }
        ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="/food-ordering/js/carousel.js"></script>
</body>
</html>