<?php
session_start();
require_once('../db/connection.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: /food-ordering/auth/login.php");
    exit;
}

$restaurant_id = $_SESSION['admin_id'];
$message = '';

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($_FILES['image']['type'], $allowedTypes)) {
        $message = "Only JPG, PNG, and GIF images are allowed.";
    } else {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/food-ordering/assets/restaurants/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $newFileName = uniqid('restaurant_', true) . '.' . $fileExt;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $newFileName)) {
            $stmt = $conn->prepare("UPDATE restaurants SET image = ? WHERE id = ?");
            $stmt->bind_param("si", $newFileName, $restaurant_id);
            $stmt->execute();
            $message = "Image updated successfully!";
        } else {
            $message = "Error uploading image.";
        }
    }
}

// Handle location save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_location'])) {
    $lat = $_POST['latitude'];
    $lng = $_POST['longitude'];

    if (!empty($lat) && !empty($lng)) {
        $stmt = $conn->prepare("UPDATE restaurants SET latitude = ?, longitude = ? WHERE id = ?");
        $stmt->bind_param("ddi", $lat, $lng, $restaurant_id);
        $stmt->execute();
        $message = "Restaurant location saved successfully!";
    } else {
        $message = "Please click on the map to select a location first.";
    }
}

// Get current restaurant info
$stmt = $conn->prepare("SELECT username, email, image, latitude, longitude FROM restaurants WHERE id = ?");
$stmt->bind_param("i", $restaurant_id);
$stmt->execute();
$restaurant = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Profile</title>
    <link href="/food-ordering/restaurant/styles/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="topbar">Profile</div>
        <div class="content">

            <?php if ($message): ?>
                <p style="color: green; margin-bottom: 15px;"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <!-- Restaurant Info -->
            <div class="card" style="margin-bottom: 20px;">
                <h3 style="margin-bottom: 15px;">Restaurant Info</h3>
                <p style="margin-bottom: 8px;">
                    <strong>Name:</strong> <?php echo htmlspecialchars($restaurant['username']); ?>
                </p>
                <p>
                    <strong>Email:</strong> <?php echo htmlspecialchars($restaurant['email']); ?>
                </p>
            </div>

            <!-- Current Image -->
            <div class="card" style="margin-bottom: 20px;">
                <h3 style="margin-bottom: 15px;">Restaurant Image</h3>
                <?php if ($restaurant['image']): ?>
                    <img src="/food-ordering/assets/restaurants/<?php echo htmlspecialchars($restaurant['image']); ?>" 
                         alt="Restaurant Image"
                         style="height: 200px; border-radius: 10px; margin-bottom: 15px; display: block;">
                <?php else: ?>
                    <p style="color: #888; margin-bottom: 15px;">No image uploaded yet.</p>
                <?php endif; ?>

                <h3 style="margin-bottom: 10px;">
                    <?php echo $restaurant['image'] ? 'Update Image' : 'Upload Image'; ?>
                </h3>
                <form method="POST" enctype="multipart/form-data">
                    <input type="file" name="image" accept="image/*" required 
                           style="margin-bottom: 10px; display: block;">
                    <button type="submit" class="menu-btn">Upload</button>
                </form>
            </div>

            <!-- Restaurant Location -->
            <div class="card" style="margin-bottom: 20px;">
                <h3 style="margin-bottom: 15px;">Restaurant Location</h3>

                <?php if ($restaurant['latitude'] && $restaurant['longitude']): ?>
                    <p style="color: green; margin-bottom: 10px;">
                        ✅ Location set: <?php echo $restaurant['latitude']; ?>, <?php echo $restaurant['longitude']; ?>
                    </p>
                <?php else: ?>
                    <p style="color: #d9534f; margin-bottom: 10px;">
                        ⚠️ No location set yet. Customers won't see delivery distance/time until you set this.
                    </p>
                <?php endif; ?>

                <form method="POST" id="location-form">
                    <div id="map-picker" style="height: 350px; border-radius: 8px; margin-bottom: 10px;"></div>
                    <p style="font-size: 13px; color: #666; margin-bottom: 10px;">Click on the map to set your restaurant's location.</p>

                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">

                    <button type="submit" name="save_location" class="menu-btn">Save Location</button>
                </form>
            </div>

        </div>
    </div>

    <script>
        // Default center (Kathmandu, Nepal) — adjust if needed
        const startLat = <?php echo $restaurant['latitude'] ? $restaurant['latitude'] : 27.7172; ?>;
        const startLng = <?php echo $restaurant['longitude'] ? $restaurant['longitude'] : 85.3240; ?>;

        const map = L.map('map-picker').setView([startLat, startLng], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        let marker;

        <?php if ($restaurant['latitude'] && $restaurant['longitude']): ?>
            marker = L.marker([startLat, startLng]).addTo(map);
            document.getElementById('latitude').value = startLat;
            document.getElementById('longitude').value = startLng;
        <?php endif; ?>

        map.on('click', function (e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;

            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng).addTo(map);
            }

            document.getElementById('latitude').value = lat.toFixed(8);
            document.getElementById('longitude').value = lng.toFixed(8);
        });
    </script>

</body>
</html>