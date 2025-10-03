<?php
session_start(); // start session

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect back to login page
header("Location: login.php");
exit();
?>
