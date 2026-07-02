<?php
// Include the database connection file
require_once('../../db/connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Trim inputs to avoid whitespace issues
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    
}
?>
