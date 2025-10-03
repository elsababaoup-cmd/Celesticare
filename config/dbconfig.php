<?php
$host = "localhost";
$user = "root";         // your XAMPP MySQL username
$pass = "";             // your XAMPP MySQL password
$dbname = "celesticare"; // your database name

// Create connection
$conn = mysqli_connect($host, $user, $pass, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
