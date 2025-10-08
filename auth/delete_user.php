<?php
session_start();
include("../config/dbconfig.php");

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if ID is passed
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // prevent SQL injection

    // Prevent deleting yourself accidentally
    if ($id == $_SESSION['user_id']) {
        header("Location: users_list.php?error=cantdeleteyourself");
        exit();
    }

    // Use prepared statement for security
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: users_list.php?deleted=success");
    } else {
        header("Location: users_list.php?deleted=failed");
    }

    $stmt->close();
} else {
    header("Location: users_list.php");
    exit();
}
?>
