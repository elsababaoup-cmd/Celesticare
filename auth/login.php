<?php
include("../config/dbconfig.php");
include(__DIR__ . "/../includes/navbar.php");
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $message = "<div class='alert alert-danger text-center'>Both fields are required.</div>";
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
                $message = "<div class='alert alert-danger text-center'>Invalid password.</div>";
            }
        } else {
            $message = "<div class='alert alert-danger text-center'>No account found with that email.</div>";
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
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow-y: scroll; /* ✅ allow scrolling */
            scrollbar-width: none; /* ✅ hide scrollbar in Firefox */
            -ms-overflow-style: none; /* ✅ hide scrollbar in IE/Edge */
            background: linear-gradient(135deg, #d3cce3 0%, #e9e4f0 100%);
            font-family: 'Poppins', sans-serif;
        }

        body::-webkit-scrollbar {
            display: none; /* ✅ hide scrollbar in Chrome/Safari */
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-box {
            background-color: #2e2e2e;
            color: #ffffff;
            padding: 40px;
            border-radius: 20px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }

        .login-box h2 {
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

        .btn-login {
            background-color: #6b5b95;
            color: #fff;
            border: none;
            border-radius: 30px;
            padding: 10px 20px;
            width: 100%;
            transition: 0.3s;
        }

        .btn-login:hover {
            background-color: #8c77c5;
        }

        .brand-title {
            font-weight: 700;
            font-size: 24px;
            letter-spacing: 1px;
            text-align: center;
            margin-bottom: 4px;
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

<div class="container login-container">
    <div class="login-box">
        <div class="brand-title">CELESTICARE</div>
        <h2>Log in to your CelestiCare profile</h2>
        <?php if (!empty($message)) echo $message; ?>
        <?php if (isset($_GET['registered'])): ?>
            <div class="alert alert-success text-center">Registration successful! Please login.</div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-login">Login</button>
            <p class="text-center text-muted mt-3">
                Don’t have a CelestiCare profile? <a href="register.php">Sign up</a>
            </p>
        </form>
    </div>
</div>

</body>
</html>
