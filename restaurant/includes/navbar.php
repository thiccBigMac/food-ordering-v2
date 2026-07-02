<nav class = "my-navbar">
    <div class="logo">
        <a href="/food-ordering/restaurant/index.php">HamroKhaja</a>
    </div>
    <ul class = "nav-links">  
        <li><a href = "/food-ordering/restaurant/menu/menu.php"><i class="bi bi-house"></i> Menu</a></li>
        <li><a href = "/food-ordering/restaurant/cart.php"><i class="bi bi-cart"></i> Orders</a></li>         
    </ul>
    
    <ul class="nav-links-right">
         <?php if (!empty($_SESSION['user_name'])): ?>
        <li class = "user-dropdown">
            <span class = "nav-username" onclick = "toggleDropdown()">
                <?php echo htmlspecialchars($_SESSION['user_name']);?>
            </span>
            <div id = "dropdown-menu" class="dropdown-content">
                <a href ="/food-ordering/cart.php"><i class = "bi bi-cart"></i>Cart</a>
                <a href = "/food-ordering/restaurant/auth/logout.php"><i class ="bi bi-box-arrow-right"></i>Logout</a>
            </div>
        </li>
    <?php else: ?>
        <li><a href="/food-ordering/restaurant/auth/login.php"><i class="bi bi-door-open"></i> Login</a></li>
    <?php endif; ?>

    </ul>
    </nav>