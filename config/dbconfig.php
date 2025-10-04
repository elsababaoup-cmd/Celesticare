<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "celesticare";

// connection
$conn = mysqli_connect($host, $user, $pass, $dbname);

// Check
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
