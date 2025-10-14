<?php
include("../config/dbconfig.php");
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['birthdate'])) {
    $birthdate = $_POST['birthdate'];
    $user_id = $_SESSION['user_id'];

    $query = "UPDATE users SET birthdate = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $birthdate, $user_id);
    if ($stmt->execute()) {
        echo "<script>alert('Birthdate saved successfully!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Error saving birthdate.'); window.history.back();</script>";
    }
}
?>
