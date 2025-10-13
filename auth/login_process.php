<?php
include("../config/dbconfig.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        echo "<script>alert('Both fields are required.'); window.history.back();</script>";
        exit();
    }

    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: ../dashboard/index.php");
            exit();
        } else {
            echo "<script>alert('Invalid password.'); window.history.back();</script>";
            exit();
        }
    } else {
        echo "<script>alert('No account found with that email.'); window.history.back();</script>";
        exit();
    }
}
?>
