<?php
include("../config/dbconfig.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate inputs
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $message = "<div class='alert alert-danger'>All fields are required.</div>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<div class='alert alert-danger'>Invalid email format.</div>";
    } elseif ($password !== $confirm_password) {
        $message = "<div class='alert alert-danger'>Passwords do not match!</div>";
    } else {
        // Check if email already exists
        $check_query = "SELECT id FROM users WHERE email='$email' LIMIT 1";
        $check_result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            $message = "<div class='alert alert-danger'>This email is already registered.</div>";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert into DB
            $query = "INSERT INTO users (username, email, password) 
                      VALUES ('$username', '$email', '$hashed_password')";
            if (mysqli_query($conn, $query)) {
                header("Location: login.php?registered=1");
                exit();
            } else {
                $message = "<div class='alert alert-danger'>Database Error: " . mysqli_error($conn) . "</div>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - CelestiCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card p-4 shadow-sm">
        <h2 class="mb-3 text-center">Create Your CelestiCare Account</h2>
        <?php if (!empty($message)) echo $message; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" placeholder="Enter a unique username" required>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" placeholder="Enter your email address" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter a strong password" required>
            </div>
            <div class="mb-3">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="Re-type your password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
            <div class="text-center mt-3">
                <a href="login.php">Already have an account? Log in</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
