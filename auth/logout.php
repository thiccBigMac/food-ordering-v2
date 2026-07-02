<?php
// Start the session to access session variables
session_start();

// Destroy the session to log the user out
session_unset();
session_destroy();

// Redirect to the homepage after logout
header("Location: /food-ordering/index.php");
exit();
?>
