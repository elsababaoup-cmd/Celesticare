<?php
session_start();
include("../config/dbconfig.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['email'] = $row['email'];
            header("Location: ../dashboard/index.php");
            exit();
        } else {
            $message = "<div class='alert alert-danger'>Invalid password.</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>No account found with that email.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - CelestiCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card p-4 shadow-sm">
        <h2 class="mb-3">Login</h2>
        <?php 
            if (isset($_GET['registered'])) echo "<div class='alert alert-success'>Registration successful! Please log in.</div>";
            if (isset($_GET['loggedout'])) echo "<div class='alert alert-info'>You have logged out.</div>";
            if (!empty($message)) echo $message; 
        ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
            <a href="register.php" class="btn btn-link">Create an account</a>
        </form>
    </div>
</div>

</body>
</html>
