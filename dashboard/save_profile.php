<?php
session_start();
include("../config/dbconfig.php");

if (!isset($_SESSION['user_id'])) {
    echo "Not logged in.";
    exit;
}

$user_id = $_SESSION['user_id'];
$name = $_POST['name'] ?? '';
$birthdate = $_POST['birthdate'] ?? '';
$gender = $_POST['gender'] ?? '';

$sql = "UPDATE users SET name=?, birthdate=?, gender=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $name, $birthdate, $gender, $user_id);

if ($stmt->execute()) {
    echo "Profile updated successfully!";
} else {
    echo "Error updating profile.";
}
?>
