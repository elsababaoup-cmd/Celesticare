<?php
session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Clear any relevant cookies
setcookie('zodiac_sign', '', time() - 3600, "/");
setcookie('undertone', '', time() - 3600, "/");

// Redirect to login page
header("Location: ../index.php");
exit();
?>