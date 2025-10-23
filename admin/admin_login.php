<?php
session_start();
include("../config/dbconfig.php");

// If already logged in as admin, redirect to manage users
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: manage_users.php");
    exit();
}

// Admin configuration
define('ADMIN_EMAIL_DOMAIN', '@celesticare.admin.com');
define('ADMIN_SECRET_KEY', 'CelestiCare2025!');

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $message = "<div class='alert alert-danger text-center'>Both fields are required.</div>";
    } else {
        // Check if it's an admin email
        $is_admin_email = (strpos($email, ADMIN_EMAIL_DOMAIN) !== false);
        
        if ($is_admin_email && $password === ADMIN_SECRET_KEY) {
            // Verify admin email exists in database
            $query = "SELECT * FROM users WHERE email = ? AND email LIKE '%" . ADMIN_EMAIL_DOMAIN . "'";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($user = mysqli_fetch_assoc($result)) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['is_admin'] = true;
                $_SESSION['admin_email'] = $email;
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                
                header("Location: manage_users.php");
                exit();
            } else {
                $message = "<div class='alert alert-danger text-center'>Admin email not found in system.</div>";
            }
        } else {
            $message = "<div class='alert alert-danger text-center'>Invalid admin credentials.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - CelestiCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow-y: scroll;
            scrollbar-width: none;
            -ms-overflow-style: none;
            background: linear-gradient(135deg, #d3cce3 0%, #e9e4f0 100%);
            font-family: 'Poppins', sans-serif;
        }

        body::-webkit-scrollbar {
            display: none;
        }

        .login-container {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
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
            margin-bottom: 5px;
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

        .admin-badge {
            background: #6b5b95;
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 500;
            margin-left: 8px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-box">
        <div class="brand-title">CELESTICARE <span class="admin-badge">ADMIN</span></div>
        <h2>Admin Access - CelestiCare</h2>
        <?php if (!empty($message)) echo $message; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Admin Email" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Admin Key" required>
            </div>
            <button type="submit" class="btn btn-login">Access Admin Panel</button>
            <p class="text-center text-muted mt-3">
                <a href="../auth/login.php">← Back to User Login</a>
            </p>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>