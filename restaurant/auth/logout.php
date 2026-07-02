<?php
session_start();
session_unset();
session_destroy();
header("Location: /food-ordering/auth/login.php");
exit;
?>