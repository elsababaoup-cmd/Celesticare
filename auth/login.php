<?php
ob_start();
session_start();

include("../config/dbconfig.php");
include(__DIR__ . "/../includes/navbar.php");
$message = "";

// ✅ ADMIN CONFIGURATION
define('ADMIN_EMAIL_DOMAIN', '@celesticare.admin.com');
define('ADMIN_SECRET_KEY', 'CelestiCare2025!');

// ✅ Allowed email domains for regular users
$allowed_domains = [
    'gmail.com',
    'yahoo.com', 
    'yahoo.co.uk',
    'yahoo.ca',
    'hotmail.com',
    'hotmail.co.uk',
    'outlook.com',
    'outlook.fr',
    'outlook.de'
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $message = "<div class='alert alert-danger text-center'>Both fields are required.</div>";
    } else {
        // ✅ Check if it's an admin login attempt
        $is_admin_email = (strpos($email, ADMIN_EMAIL_DOMAIN) !== false);
        
        // ✅ For regular users, validate email domain
        if (!$is_admin_email) {
            $email_domain = strtolower(substr(strrchr($email, "@"), 1));
            if (!in_array($email_domain, $allowed_domains)) {
                $message = "<div class='alert alert-danger text-center'>Only Gmail, Yahoo, Hotmail, and Outlook emails are allowed.</div>";
            } elseif (strpos($password, ' ') !== false) {
                $message = "<div class='alert alert-danger text-center'>Password cannot contain spaces.</div>";
            } else {
                // Regular user login logic
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
        } else {
            // Admin login logic (unchanged)
            if ($password === ADMIN_SECRET_KEY) {
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
                    
                    ob_end_clean();
                    header("Location: ../admin/manage_users.php");
                    exit();
                } else {
                    $message = "<div class='alert alert-danger text-center'>Admin email not found in system.</div>";
                }
            } else {
                $message = "<div class='alert alert-danger text-center'>Invalid admin credentials.</div>";
            }
        }
    }
}

ob_end_flush();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - CelestiCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #cfcfcf;
            cursor: pointer;
            z-index: 10;
        }

        .toggle-password:hover {
            color: #ffffff;
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
            <div class="mb-3 password-container">
                <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                <button type="button" class="toggle-password" id="togglePassword">
                    <i class="far fa-eye"></i>
                </button>
            </div>
            <button type="submit" class="btn btn-login">Login</button>
            <p class="text-center text-muted mt-3">
                Don't have a CelestiCare profile? <a href="register.php">Sign up</a>
            </p>
        </form>
    </div>
</div>

<script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
</script>

</body>
</html>