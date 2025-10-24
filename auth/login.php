<?php
// Start output buffering at the VERY top
ob_start();
session_start();

include("../config/dbconfig.php");
include(__DIR__ . "/../includes/navbar.php");
$message = "";

// ✅ ADMIN CONFIGURATION
define('ADMIN_EMAIL_DOMAIN', '@celesticare.admin.com');
define('ADMIN_SECRET_KEY', 'CelestiCare2025!'); // Change this to your secure admin password

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $message = "<div class='alert alert-danger text-center'>Both fields are required.</div>";
    } else {
        // ✅ Check if it's an admin login attempt
        $is_admin_email = (strpos($email, ADMIN_EMAIL_DOMAIN) !== false);
        
        if ($is_admin_email) {
            // Admin login logic
            if ($password === ADMIN_SECRET_KEY) {
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
                    
                    // Clear the buffer before redirect
                    ob_end_clean();
                    header("Location: ../admin/manage_users.php");
                    exit();
                } else {
                    $message = "<div class='alert alert-danger text-center'>Admin email not found in system.</div>";
                }
            } else {
                $message = "<div class='alert alert-danger text-center'>Invalid admin credentials.</div>";
            }
        } else {
            // Regular user login logic (your existing code)
            $query = "SELECT * FROM users WHERE email = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($user = mysqli_fetch_assoc($result)) {
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['is_admin'] = false;
                    
                    // Clear the buffer before redirect
                    ob_end_clean();
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
}

// If we reach here, output the buffered content
ob_end_flush();
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
            overflow-y: scroll;
            scrollbar-width: none;
            -ms-overflow-style: none;
            background: linear-gradient(135deg, #aaa2bcff 0%, #937db1ff 100%);
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

<div class="login-container">
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
                Don't have a CelestiCare profile? <a href="register.php">Sign up</a>
            </p>
        </form>
    </div>
</div>

</body>
</html>