<?php
session_start();
if (!isset($_SESSION['system_admin_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
require_once('../db/connection.php');

$totalRestaurants = $conn->query("SELECT COUNT(*) as c FROM restaurants")->fetch_assoc()['c'];
$totalUsers       = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$totalOrders      = $conn->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c'];
$pendingOrders    = $conn->query("SELECT COUNT(*) as c FROM orders WHERE status = 'pending'")->fetch_assoc()['c'];
$totalRevenue     = $conn->query("SELECT COALESCE(SUM(total_amount), 0) as c FROM orders WHERE status = 'completed'")->fetch_assoc()['c'];
$totalFeedback    = $conn->query("SELECT COUNT(*) as c FROM contacts")->fetch_assoc()['c'];

// ---- Chart Data 1: Platform-wide revenue trend, last 7 days ----
$revenueLabels = [];
$revenueData = [];

for ($i = 6; $i >= 0; $i--) {
    $day = date('Y-m-d', strtotime("-$i days"));
    $revenueLabels[] = date('M d', strtotime($day));

    $stmt = $conn->prepare("
        SELECT COALESCE(SUM(total_amount), 0) as total
        FROM orders
        WHERE status = 'completed'
          AND DATE(created_at) = ?
    ");
    $stmt->bind_param("s", $day);
    $stmt->execute();
    $dayTotal = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $revenueData[] = (float) $dayTotal;
    $stmt->close();
}

// ---- Chart Data 2: Orders by status (all-time, platform-wide) ----
$statusLabels = ['Pending', 'Processing', 'Completed', 'Cancelled'];
$statusKeys = ['pending', 'processing', 'completed', 'cancelled'];
$statusData = [];

foreach ($statusKeys as $key) {
    $stmt = $conn->prepare("SELECT COUNT(*) as c FROM orders WHERE status = ?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $statusData[] = (int) ($stmt->get_result()->fetch_assoc()['c'] ?? 0);
    $stmt->close();
}

// ---- Chart Data 3: Top 5 restaurants by revenue ----
$topRestLabels = [];
$topRestData = [];

$result = $conn->query("
    SELECT r.username, COALESCE(SUM(o.total_amount), 0) as revenue
    FROM restaurants r
    LEFT JOIN orders o ON o.restaurant_id = r.id AND o.status = 'completed'
    GROUP BY r.id
    ORDER BY revenue DESC
    LIMIT 5
");
while ($row = $result->fetch_assoc()) {
    $topRestLabels[] = $row['username'];
    $topRestData[] = (float) $row['revenue'];
}

// ---- Chart Data 4: New orders per day, last 7 days (all statuses) ----
$orderVolumeLabels = [];
$orderVolumeData = [];

for ($i = 6; $i >= 0; $i--) {
    $day = date('Y-m-d', strtotime("-$i days"));
    $orderVolumeLabels[] = date('M d', strtotime($day));

    $stmt = $conn->prepare("SELECT COUNT(*) as c FROM orders WHERE DATE(created_at) = ?");
    $stmt->bind_param("s", $day);
    $stmt->execute();
    $orderVolumeData[] = (int) ($stmt->get_result()->fetch_assoc()['c'] ?? 0);
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>System Admin Dashboard</title>
    <link href="/food-ordering/restaurant/styles/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        .wrapper {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .dashboard-card {
            background: white;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(43,38,32,0.12);
            padding: 30px 20px;
            text-align: center;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            text-decoration: none;
            color: inherit;
            user-select: none;
        }
        .dashboard-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 14px 34px rgba(43,38,32,0.18);
        }
        .dashboard-card h2 {
            font-family: 'Fraunces', Georgia, serif;
            font-size: 3rem;
            margin-bottom: 8px;
            color: #2E4E50;
        }
        .dashboard-card p {
            font-size: 1.1rem;
            font-weight: 600;
            color: #6B6355;
        }

        .charts-wrapper {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
            padding: 0 20px 20px;
        }
        .chart-card {
            background: white;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(43,38,32,0.12);
            padding: 22px;
        }
        .chart-card h3 {
            font-family: 'Fraunces', Georgia, serif;
            color: #2E4E50;
            font-size: 1.15rem;
            margin-bottom: 15px;
        }
        .chart-card canvas {
            max-height: 300px;
        }
        .no-data {
            text-align: center;
            color: #999;
            padding: 40px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <a href="dashboard.php" class="logo">HamroKhaja</a>
    <a href="dashboard.php">Dashboard</a>
    <a href="restaurants.php">Restaurants</a>
    <a href="users.php">Users</a>
    <a href="orders.php">Orders</a>
    <a href="feedback.php">Feedback</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main-content">
    <div class="topbar">Welcome, <?php echo htmlspecialchars($_SESSION['system_admin_user']); ?></div>
    <div class="content">
        <div class="wrapper">
            <a href="restaurants.php" class="dashboard-card">
                <h2><?php echo $totalRestaurants; ?></h2>
                <p>Restaurants</p>
            </a>
            <a href="users.php" class="dashboard-card">
                <h2><?php echo $totalUsers; ?></h2>
                <p>Users</p>
            </a>
            <a href="orders.php" class="dashboard-card">
                <h2><?php echo $totalOrders; ?></h2>
                <p>Total Orders</p>
            </a>
            <a href="orders.php" class="dashboard-card">
                <h2><?php echo $pendingOrders; ?></h2>
                <p>Pending Orders</p>
            </a>
            <div class="dashboard-card">
                <h2>Rs. <?php echo number_format($totalRevenue, 0); ?></h2>
                <p>Revenue</p>
            </div>
            <a href="feedback.php" class="dashboard-card">
                <h2><?php echo $totalFeedback; ?></h2>
                <p>Feedback</p>
            </a>
        </div>

        <div class="charts-wrapper">

            <div class="chart-card">
                <h3>Platform Revenue — Last 7 Days</h3>
                <canvas id="revenueChart"></canvas>
            </div>

            <div class="chart-card">
                <h3>Orders by Status</h3>
                <?php if (array_sum($statusData) > 0): ?>
                    <canvas id="statusChart"></canvas>
                <?php else: ?>
                    <p class="no-data">No orders yet.</p>
                <?php endif; ?>
            </div>

            <div class="chart-card">
                <h3>Top 5 Restaurants by Revenue</h3>
                <?php if (count($topRestLabels) > 0 && array_sum($topRestData) > 0): ?>
                    <canvas id="topRestChart"></canvas>
                <?php else: ?>
                    <p class="no-data">No revenue data yet.</p>
                <?php endif; ?>
            </div>

            <div class="chart-card">
                <h3>Order Volume — Last 7 Days</h3>
                <canvas id="orderVolumeChart"></canvas>
            </div>

        </div>
    </div>
</div>

<script>
    // Platform Revenue Trend
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: <?php echo json_encode($revenueLabels); ?>,
            datasets: [{
                label: 'Revenue (Rs.)',
                data: <?php echo json_encode($revenueData); ?>,
                borderColor: '#2E4E50',
                backgroundColor: 'rgba(46, 78, 80, 0.1)',
                fill: true,
                tension: 0.3,
                pointBackgroundColor: '#D9A441',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: function(value) { return 'Rs. ' + value; } }
                }
            }
        }
    });

    <?php if (array_sum($statusData) > 0): ?>
    // Orders by Status
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($statusLabels); ?>,
            datasets: [{
                data: <?php echo json_encode($statusData); ?>,
                backgroundColor: ['#D9A441', '#5bc0de', '#5C8A66', '#C0605A']
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });
    <?php endif; ?>

    <?php if (count($topRestLabels) > 0 && array_sum($topRestData) > 0): ?>
    // Top Restaurants by Revenue
    new Chart(document.getElementById('topRestChart'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($topRestLabels); ?>,
            datasets: [{
                label: 'Revenue (Rs.)',
                data: <?php echo json_encode($topRestData); ?>,
                backgroundColor: '#2E4E50'
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, ticks: { callback: function(value) { return 'Rs. ' + value; } } }
            }
        }
    });
    <?php endif; ?>

    // Order Volume - Last 7 Days
    new Chart(document.getElementById('orderVolumeChart'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($orderVolumeLabels); ?>,
            datasets: [{
                label: 'Orders',
                data: <?php echo json_encode($orderVolumeData); ?>,
                backgroundColor: '#D9A441'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
</script>

</body>
</html>