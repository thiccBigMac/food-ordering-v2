<?php include "auth/session.php" ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food ordering system</title>
    <link rel="stylesheet" href="/food-ordering/styles/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>

<?php include 'includes/header.php';?>

//Cart div

<div class="cart-container">
    <h2 class="cart-title">Your Shopping Cart</h2>

    <div class="cart-item">
      <img src="https://via.placeholder.com/80" alt="Product Image" class="product-image">
      <div class="item-details">
        <h3 class="product-name">Product Name</h3>
        <p class="product-qty">Quantity: 1</p>
        <p class="product-price">$29.99</p>
      </div>
      <button class="remove-btn">Remove</button>
    </div>

    <div class="cart-item">
      <img src="https://via.placeholder.com/80" alt="Product Image" class="product-image">
      <div class="item-details">
        <h3 class="product-name">Another Item</h3>
        <p class="product-qty">Quantity: 2</p>
        <p class="product-price">$19.99</p>
      </div>
      <button class="remove-btn">Remove</button>
    </div>

    <div class="cart-total">
      <span>Total:</span>
      <span class="total-amount">$69.97</span>
    </div>

    <div class="checkout">
      <button class="checkout-btn">Proceed to Checkout</button>
    </div>
  </div>

<?php include 'includes/footer.php';?>

</body>
</html>