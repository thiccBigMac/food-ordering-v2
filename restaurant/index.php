<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include "auth/session.php";
include 'sidebar.php';
require_once('../db/connection.php');

$restaurant_id = $_SESSION['admin_id'];

$pendingOrdersCount = 0;
$feedbackCount = 0;
$menuCount = 0;
$revenueTotal = 0;

// Pending orders count
if ($stmt = $conn->prepare("SELECT COUNT(*) as count FROM orders WHERE status = 'pending' AND restaurant_id = ?")) {
    $stmt->bind_param("i", $restaurant_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $pendingOrdersCount = $res->fetch_assoc()['count'] ?? 0;
    $stmt->close();
}

// Feedback count
if ($stmt = $conn->prepare("SELECT COUNT(*) as count FROM contacts WHERE restaurant_id = ?")) {
    $stmt->bind_param("i", $restaurant_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $feedbackCount = $res->fetch_assoc()['count'] ?? 0;
    $stmt->close();
}

// Menu items count
if ($stmt = $conn->prepare("SELECT COUNT(*) as count FROM menu_items WHERE restaurant_id = ?")) {
    $stmt->bind_param("i", $restaurant_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $menuCount = $res->fetch_assoc()['count'] ?? 0;
    $stmt->close();
}

// Revenue
if ($stmt = $conn->prepare("SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE restaurant_id = ? AND status = 'completed'")) {
    $stmt->bind_param("i", $restaurant_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $revenueTotal = $res->fetch_assoc()['total'] ?? 0;
    $stmt->close();
}

// ---- Chart Data 1: Revenue trend for the last 7 days ----
$revenueLabels = [];
$revenueData = [];

for ($i = 6; $i >= 0; $i--) {
    $day = date('Y-m-d', strtotime("-$i days"));
    $revenueLabels[] = date('M d', strtotime($day));

    $stmt = $conn->prepare("
        SELECT COALESCE(SUM(total_amount), 0) as total
        FROM orders
        WHERE restaurant_id = ?
          AND status = 'completed'
          AND DATE(created_at) = ?
    ");
    $stmt->bind_param("is", $restaurant_id, $day);
    $stmt->execute();
    $dayTotal = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $revenueData[] = (float) $dayTotal;
    $stmt->close();
}

// ---- Chart Data 2: Orders by status (all-time) ----
$statusLabels = ['Pending', 'Processing', 'Completed', 'Cancelled'];
$statusKeys = ['pending', 'processing', 'completed', 'cancelled'];
$statusData = [];

foreach ($statusKeys as $key) {
    $stmt = $conn->prepare("SELECT COUNT(*) as c FROM orders WHERE restaurant_id = ? AND status = ?");
    $stmt->bind_param("is", $restaurant_id, $key);
    $stmt->execute();
    $statusData[] = (int) ($stmt->get_result()->fetch_assoc()['c'] ?? 0);
    $stmt->close();
}

// ---- Chart Data 3: Top 5 selling menu items (by quantity) ----
$topItemLabels = [];
$topItemData = [];

$stmt = $conn->prepare("
    SELECT m.name, SUM(oi.quantity) as total_qty
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    JOIN menu_items m ON oi.menu_item_id = m.id
    WHERE o.restaurant_id = ?
    GROUP BY oi.menu_item_id
    ORDER BY total_qty DESC
    LIMIT 5
");
$stmt->bind_param("i", $restaurant_id);
$stmt->execute();
$topResult = $stmt->get_result();
while ($row = $topResult->fetch_assoc()) {
    $topItemLabels[] = $row['name'];
    $topItemData[] = (int) $row['total_qty'];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard</title>
  <link href="/food-ordering/restaurant/styles/style.css" rel="stylesheet" />
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

  <div class="main-content">
    <div class="topbar">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></div>
    <div class="content">
      <div class="wrapper">
        <a href="orders/orders.php" class="dashboard-card">
          <h2><?php echo $pendingOrdersCount; ?></h2>
          <p>Pending Orders</p>
        </a>
        <a href="support/inbox.php" class="dashboard-card">
          <h2><?php echo $feedbackCount; ?></h2>
          <p>Inbox Feedback</p>
        </a>
        <a href="menu/menu.php" class="dashboard-card">
          <h2><?php echo $menuCount; ?></h2>
          <p>Menu Items</p>
        </a>
        <div class="dashboard-card">
          <h2>Rs. <?php echo number_format($revenueTotal, 0); ?></h2>
          <p>Revenue</p>
        </div>
      </div>

      <div class="charts-wrapper">

        <!-- Revenue Trend -->
        <div class="chart-card">
          <h3>Revenue — Last 7 Days</h3>
          <canvas id="revenueChart"></canvas>
        </div>

        <!-- Orders by Status -->
        <div class="chart-card">
          <h3>Orders by Status</h3>
          <?php if (array_sum($statusData) > 0): ?>
            <canvas id="statusChart"></canvas>
          <?php else: ?>
            <p class="no-data">No orders yet.</p>
          <?php endif; ?>
        </div>

        <!-- Top Selling Items -->
        <div class="chart-card">
          <h3>Top 5 Selling Items</h3>
          <?php if (count($topItemLabels) > 0): ?>
            <canvas id="topItemsChart"></canvas>
          <?php else: ?>
            <p class="no-data">No sales data yet.</p>
          <?php endif; ?>
        </div>

      </div>
    </div>
  </div>

  <script>
    // Revenue Trend Chart
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
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) { return 'Rs. ' + value; }
                    }
                }
            }
        }
    });

    <?php if (array_sum($statusData) > 0): ?>
    // Orders by Status Chart
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($statusLabels); ?>,
            datasets: [{
                data: <?php echo json_encode($statusData); ?>,
                backgroundColor: [
                    '#f0ad4e', // pending - amber
                    '#5bc0de', // processing - blue
                    '#5cb85c', // completed - green
                    '#d9534f'  // cancelled - red
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
    <?php endif; ?>

    <?php if (count($topItemLabels) > 0): ?>
    // Top Selling Items Chart
    new Chart(document.getElementById('topItemsChart'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($topItemLabels); ?>,
            datasets: [{
                label: 'Quantity Sold',
                data: <?php echo json_encode($topItemData); ?>,
                backgroundColor: '#2E4E50'
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
    <?php endif; ?>
  </script>

</body>
</html>