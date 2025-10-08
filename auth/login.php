<?php
include("../config/dbconfig.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $message = "<div class='alert alert-danger'>Both fields are required.</div>";
    } else {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: ../dashboard/index.php");
                exit();
            } else {
                $message = "<div class='alert alert-danger'>Invalid password.</div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>No account found with that email.</div>";
        }
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
        <?php if (!empty($message)) echo $message; ?>
        <?php if (isset($_GET['registered'])): ?>
            <div class="alert alert-success">Registration successful! Please login.</div>
        <?php endif; ?>
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
            <a href="register.php" class="btn btn-link">Don't have an account?</a>
        </form>
    </div>
</div>

</body>
</html>
