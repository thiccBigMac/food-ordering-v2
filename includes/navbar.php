<nav class = "my-navbar">
    <div class="logo">
        <a href="/food-ordering/index.php">HamroKhaja</a>
    </div>
    <ul class = "nav-links">  
        <li><a href = "/food-ordering/index.php"><i class="bi bi-house"></i> Home</a></li>
        <li><a href = "/food-ordering/menu.php"><i class="bi bi-shop"></i> Restaurants</a></li>
        <li><a href = "/food-ordering/contact.php"><i class="bi bi-telephone"></i> Contact</a></li>
        <li>
    <a href="/food-ordering/cart.php" style="position:relative;">
        <i class="bi bi-cart"></i> Cart
        <?php
        require_once $_SERVER['DOCUMENT_ROOT'] . '/food-ordering/db/connection.php';
        if (isset($_SESSION['user_id'])) {
            $cartStmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
            $cartStmt->bind_param("i", $_SESSION['user_id']);
            $cartStmt->execute();
            $cartCount = $cartStmt->get_result()->fetch_assoc()['total'] ?? 0;
            if ($cartCount > 0):
        ?>
            <span style="
                position:absolute; top:-8px; right:-10px;
                background:red; color:white;
                border-radius:50%; width:18px; height:18px;
                font-size:11px; font-weight:700;
                display:flex; align-items:center; justify-content:center;
            "><?php echo $cartCount; ?></span>
        <?php endif; } ?>
    </a>
</li>        
    </ul>
    
    <ul class="nav-links-right">
    <?php if (!empty($_SESSION['user_name'])): ?>
        <li class = "user-dropdown">
            <span class="nav-username" onclick="toggleDropdown()" style="position:relative;">
    <?php echo htmlspecialchars($_SESSION['user_name']); ?>
    <?php
    $unreadStmt = $conn->prepare("SELECT COUNT(*) as c FROM user_notifications WHERE user_id = ? AND is_read = 0");
    $unreadStmt->bind_param("i", $_SESSION['user_id']);
    $unreadStmt->execute();
    $unreadCount = $unreadStmt->get_result()->fetch_assoc()['c'];
    if ($unreadCount > 0):
    ?>
        <span style="
            position:absolute; top:-5px; right:-10px;
            background:red; color:white;
            border-radius:50%; width:16px; height:16px;
            font-size:10px; font-weight:700;
            display:inline-flex; align-items:center; justify-content:center;
        "><?php echo $unreadCount; ?></span>
    <?php endif; ?>
</span>
            <div id = "dropdown-menu" class="dropdown-content">
    <a href ="/food-ordering/cart.php"><i class = "bi bi-cart"></i> Cart</a>
    <a href="/food-ordering/orders.php" style="position:relative;">
    <i class="bi bi-receipt"></i> My Orders
    <?php
    $newOrderStmt = $conn->prepare("SELECT COUNT(*) as c FROM orders WHERE user_id = ? AND status = 'completed' AND seen = 0");
    $newOrderStmt->bind_param("i", $_SESSION['user_id']);
    $newOrderStmt->execute();
    $newOrderCount = $newOrderStmt->get_result()->fetch_assoc()['c'];
    if ($newOrderCount > 0):
    ?>
        <span style="
            position:absolute; top:8px; right:10px;
            background:red; border-radius:50%;
            width:8px; height:8px; display:inline-block;
        "></span>
    <?php endif; ?>
</a>

    <a href="/food-ordering/notifications.php" style="position:relative;">
    <i class="bi bi-bell"></i> Notifications
    <?php if ($unreadCount > 0): ?>
        <span style="
            position:absolute; top:8px; right:10px;
            background:red; border-radius:50%;
            width:8px; height:8px; display:inline-block;
        "></span>
    <?php endif; ?>
</a>
    <a href = "/food-ordering/auth/logout.php"><i class ="bi bi-box-arrow-right"></i> Logout</a>           
</div>
        </li>
    <?php else: ?>
        <li><a href="/food-ordering/auth/login.php"><i class="bi bi-door-open"></i> Login</a></li>
    <?php endif; ?>

    </ul>
    </nav>


   