<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include "auth/session.php";
include 'sidebar.php';
require_once('../db/connection.php');

$pendingOrdersCount = 0;
$feedbackCount = 0;
$menuCount = 0;
$revenueTotal = 0;

// Pending orders count
if ($stmt = $conn->prepare("SELECT COUNT(*) as count FROM orders WHERE status = 'pending' AND restaurant_id = ?")) {
    $stmt->bind_param("i", $_SESSION['admin_id']);
    $stmt->execute();
    $res = $stmt->get_result();
    $pendingOrdersCount = $res->fetch_assoc()['count'] ?? 0;
    $stmt->close();
}

// Feedback count
if ($stmt = $conn->prepare("SELECT COUNT(*) as count FROM contacts WHERE restaurant_id = ?")) {
    $stmt->bind_param("i", $_SESSION['admin_id']);
    $stmt->execute();
    $res = $stmt->get_result();
    $feedbackCount = $res->fetch_assoc()['count'] ?? 0;
    $stmt->close();
}

// Menu items count
if ($stmt = $conn->prepare("SELECT COUNT(*) as count FROM menu_items WHERE restaurant_id = ?")) {
    $stmt->bind_param("i", $_SESSION['admin_id']);
    $stmt->execute();
    $res = $stmt->get_result();
    $menuCount = $res->fetch_assoc()['count'] ?? 0;
    $stmt->close();
}

// Revenue
if ($stmt = $conn->prepare("SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE restaurant_id = ? AND status = 'completed'")) {
    $stmt->bind_param("i", $_SESSION['admin_id']);
    $stmt->execute();
    $res = $stmt->get_result();
    $revenueTotal = $res->fetch_assoc()['total'] ?? 0;
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard</title>
  <link href="/food-ordering/restaurant/styles/style.css" rel="stylesheet" />
  <style>
    .wrapper {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
      padding: 20px;
    }
    .dashboard-card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 6px 18px rgba(0,0,0,0.1);
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
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    .dashboard-card h2 {
      font-size: 3rem;
      margin-bottom: 8px;
      color: #2E4E50;
    }
    .dashboard-card p {
      font-size: 1.2rem;
      font-weight: 600;
      color: #333;
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
    </div>
  </div>

</body>
</html>