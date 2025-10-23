<?php
ob_start(); // Start output buffering
include("../config/dbconfig.php");
include(__DIR__ . "/../includes/navbar.php");
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $message = "<div class='alert alert-danger text-center'>All fields are required.</div>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<div class='alert alert-danger text-center'>Invalid email format.</div>";
    } elseif ($password !== $confirm_password) {
        $message = "<div class='alert alert-danger text-center'>Passwords do not match!</div>";
    } else {
        $check_query = "SELECT id FROM users WHERE email='$email' LIMIT 1";
        $check_result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            $message = "<div class='alert alert-danger text-center'>This email is already registered.</div>";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
            if (mysqli_query($conn, $query)) {
                ob_end_clean(); // Clear the buffer before redirect
                header("Location: login.php?registered=1");
                exit();
            } else {
                $message = "<div class='alert alert-danger text-center'>Database error. Please try again.</div>";
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
    <style>
        * {
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow-y: scroll; /* ✅ keep scrolling enabled */
            scrollbar-width: none; /* ✅ hide scrollbar in Firefox */
            -ms-overflow-style: none; /* ✅ hide scrollbar in IE/Edge */
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #d3cce3 0%, #e9e4f0 100%);
        }

        body::-webkit-scrollbar {
            display: none; /* ✅ hide scrollbar in Chrome, Safari */
        }

        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px; /* ✅ padding allows small scroll if content overflows */
        }

        .register-box {
            background-color: #2e2e2e;
            color: #ffffff;
            padding: 40px;
            border-radius: 20px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }

        .register-box h2 {
            font-size: 20px;
            text-align: center;
            margin-bottom: 25px;
            font-weight: 600;
        }

        .form-control {
            background-color: #4b4b4b;
            border: none;
            color: #fff;
            border-radius: 30px;
            padding: 12px 20px;
        }

        .form-control::placeholder {
            color: #cfcfcf;
        }

        .btn-register {
            background-color: #6b5b95;
            color: #fff;
            border: none;
            border-radius: 30px;
            padding: 10px 20px;
            width: 100%;
            transition: 0.3s;
        }

        .btn-register:hover {
            background-color: #8c77c5;
        }

        .brand-title {
            font-weight: 700;
            font-size: 24px;
            letter-spacing: 1px;
            text-align: center;
            margin-bottom: 3px;
        }

        .text-muted {
            color: #cfcfcf !important;
        }

        .text-muted a {
            color: #fff !important;
            text-decoration: underline;
        }

        .text-muted a:hover {
            color: #d3bfff !important;
        }
    </style>
</head>
<body>

<div class="register-container">
    <div class="register-box">
        <div class="brand-title">CELESTICARE</div>
        <h2>Sign up to create your CelestiCare Profile!</h2>
        <?php if (!empty($message)) echo $message; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <div class="mb-3">
                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
            </div>
            <button type="submit" class="btn btn-register">Continue</button>
            <p class="text-center text-muted mt-3">
                Already have an account? <a href="login.php">Login</a>
            </p>
        </form>
    </div>
</div>

</body>
</html>
