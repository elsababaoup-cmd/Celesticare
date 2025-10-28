<?php
session_start();
include("../config/dbconfig.php");

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

// ✅ ADMIN CONFIGURATION
define('ADMIN_EMAIL_DOMAIN', '@celesticare.admin.com');
define('ADMIN_SECRET_KEY', 'CelestiCare2025!');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        echo "error:Both fields are required.";
        exit();
    }

    // ✅ Check if it's an admin login attempt
    $is_admin_email = (strpos($email, ADMIN_EMAIL_DOMAIN) !== false);
    
    if (!$is_admin_email) {
        // ✅ Validate email domain for regular users
        $email_domain = strtolower(substr(strrchr($email, "@"), 1));
        if (!in_array($email_domain, $allowed_domains)) {
            echo "error:Only Gmail, Yahoo, Hotmail, and Outlook emails are allowed.";
            exit();
        }
        
        // Validate password for regular users
        if (strpos($password, ' ') !== false) {
            echo "error:Password cannot contain spaces.";
            exit();
        }
        
        // Regular user login
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
                
                echo "success:../dashboard/index.php";
            } else {
                echo "error:Invalid password.";
            }
        } else {
            echo "error:No account found with that email.";
        }
    } else {
        // Admin login logic
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
                
                echo "success:../admin/manage_users.php";
            } else {
                echo "error:Admin email not found in system.";
            }
        } else {
            echo "error:Invalid admin credentials.";
        }
    }
} else {
    echo "error:Invalid request method.";
}
?>