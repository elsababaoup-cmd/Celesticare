<?php
session_start();
require_once "config/config.php";
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css"> <!-- optional -->
</head>
<body>
    <header>
        <h1>Welcome to <?php echo SITE_NAME; ?>!</h1>
        <p>Your zodiac-inspired styling app</p>
    </header>

    <main>
        <p>
            <a href="auth/login.php">Login</a> | 
            <a href="auth/register.php">Register</a>
        </p>
        <p>Explore your aesthetic, color analysis, and style recommendations.</p>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> <?php echo SITE_NAME; ?></p>
    </footer>
</body>
</html>
