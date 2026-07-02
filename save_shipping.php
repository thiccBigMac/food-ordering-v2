<?php
session_start();
if (isset($_POST['shipping_address'])) {
    $_SESSION['checkout_shipping'] = trim($_POST['shipping_address']);
}
?>