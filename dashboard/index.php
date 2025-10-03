<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head><title>User Dashboard</title></head>
<body>
    <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
    <p>This is your dashboard.</p>
    <p><a href="../auth/logout.php">Logout</a></p>
</body>
</html>
