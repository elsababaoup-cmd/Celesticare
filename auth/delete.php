<?php
session_start();
include("../config/dbconfig.php");

// ✅ Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit();
}

// ✅ Check if an ID is passed in the URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // sanitize input

    // Delete query
    $query = "DELETE FROM users WHERE id = $id LIMIT 1";

    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = "Record deleted successfully.";
    } else {
        $_SESSION['message'] = "Error deleting record: " . mysqli_error($conn);
    }

    header("Location: manage_users.php"); // redirect back to table
    exit();
} else {
    $_SESSION['message'] = "Invalid request.";
    header("Location: manage_users.php");
    exit();
}
?>
