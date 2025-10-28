<?php
ob_start();
include("../config/dbconfig.php");
include(__DIR__ . "/../includes/navbar.php");
$message = "";

// ✅ Allowed email domains
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
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    
    // Check if it's an admin email registration attempt
    $is_admin_email = (strpos($email, ADMIN_EMAIL_DOMAIN) !== false);
    
    if ($is_admin_email) {
        // Admin registration - use admin password fields
        $password = $_POST['admin_password'] ?? '';
        $confirm_password = $_POST['confirm_admin_password'] ?? '';
    } else {
        // Regular registration - use regular password fields
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
    }

    if (empty($username) || empty($email)) {
        $message = "<div class='alert alert-danger text-center'>Username and email are required.</div>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<div class='alert alert-danger text-center'>Invalid email format.</div>";
    } else {
        if ($is_admin_email) {
            // ✅ Admin email registration - require admin passwords
            if (empty($password) || empty($confirm_password)) {
                $message = "<div class='alert alert-danger text-center'>Admin password fields are required.</div>";
            } elseif ($password !== $confirm_password) {
                $message = "<div class='alert alert-danger text-center'>Admin passwords do not match!</div>";
            } elseif ($password !== ADMIN_SECRET_KEY) {
                $message = "<div class='alert alert-danger text-center'>Invalid admin password for admin email registration.</div>";
            } else {
                // Admin email validation passed, proceed with registration
                $valid_admin_email = true;
            }
        } else {
            // ✅ Regular user email - check if domain is allowed
            $email_domain = strtolower(substr(strrchr($email, "@"), 1));
            if (!in_array($email_domain, $allowed_domains)) {
                $message = "<div class='alert alert-danger text-center'>Only Gmail, Yahoo, Hotmail, and Outlook emails are allowed.</div>";
            } else {
                $valid_regular_email = true;
            }
        }

        // If email validation passed, continue with password checks
        if (isset($valid_admin_email) || isset($valid_regular_email)) {
            if ($password !== $confirm_password) {
                $message = "<div class='alert alert-danger text-center'>Passwords do not match!</div>";
            } else {
                // Validate password: no spaces, minimum 8 characters, at least one uppercase letter
                // For admin, skip regular password validation since they use the admin secret key
                if (!$is_admin_email) {
                    if (strpos($password, ' ') !== false) {
                        $message = "<div class='alert alert-danger text-center'>Password cannot contain spaces.</div>";
                    } elseif (strlen($password) < 8) {
                        $message = "<div class='alert alert-danger text-center'>Password must be at least 8 characters long.</div>";
                    } elseif (!preg_match('/[A-Z]/', $password)) {
                        $message = "<div class='alert alert-danger text-center'>Password must contain at least one uppercase letter.</div>";
                    }
                }
                
                // If no password errors, proceed
                if (empty($message)) {
                    $check_query = "SELECT id FROM users WHERE email='$email' LIMIT 1";
                    $check_result = mysqli_query($conn, $check_query);

                    if (mysqli_num_rows($check_result) > 0) {
                        $message = "<div class='alert alert-danger text-center'>This email is already registered.</div>";
                    } else {
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $query = "INSERT INTO users (username, email, password, is_admin) VALUES ('$username', '$email', '$hashed_password', " . ($is_admin_email ? '1' : '0') . ")";
                        if (mysqli_query($conn, $query)) {
                            ob_end_clean();
                            if ($is_admin_email) {
                                header("Location: login.php?admin_registered=1");
                            } else {
                                header("Location: login.php?registered=1");
                            }
                            exit();
                        } else {
                            $message = "<div class='alert alert-danger text-center'>Database error. Please try again.</div>";
                        }
                    }
                }
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow-y: scroll;
            scrollbar-width: none;
            -ms-overflow-style: none;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #aaa2bcff 0%, #937db1ff 100%);
        }

        body::-webkit-scrollbar {
            display: none;
        }

        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
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

        .admin-notice {
            background: rgba(107, 91, 149, 0.3);
            border: 1px solid #6b5b95;
            border-radius: 10px;
            padding: 10px 15px;
            margin-bottom: 15px;
            font-size: 0.9rem;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="register-container">
    <div class="register-box">
        <div class="brand-title">CELESTICARE</div>
        <h2>Sign up to create your CelestiCare Profile!</h2>
        <?php if (!empty($message)) echo $message; ?>
        <form method="POST" action="" id="registerForm">
            <div class="mb-3">
                <input type="email" name="email" id="registerEmail" class="form-control" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>
            
            <!-- Regular User Password Fields -->
            <div id="regularPasswordFields">
                <div class="mb-3 password-container">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Password (min. 8 chars, 1 uppercase)" required>
                    <button type="button" class="toggle-password" id="togglePassword">
                        <i class="far fa-eye"></i>
                    </button>
                </div>
                <div class="mb-3 password-container">
                    <input type="password" name="confirm_password" id="confirmPassword" class="form-control" placeholder="Confirm Password" required>
                    <button type="button" class="toggle-password" id="toggleConfirmPassword">
                        <i class="far fa-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Admin Password Fields (initially hidden) -->
            <div id="adminPasswordFields" style="display: none;">
                <div class="admin-notice">
                    <i class="fas fa-shield-alt me-2"></i>Admin Account Registration
                </div>
                <div class="mb-3 password-container">
                    <input type="password" name="admin_password" id="adminPassword" class="form-control" placeholder="Admin Password">
                    <button type="button" class="toggle-password" id="toggleAdminPassword">
                        <i class="far fa-eye"></i>
                    </button>
                </div>
                <div class="mb-3 password-container">
                    <input type="password" name="confirm_admin_password" id="confirmAdminPassword" class="form-control" placeholder="Confirm Admin Password">
                    <button type="button" class="toggle-password" id="toggleConfirmAdminPassword">
                        <i class="far fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <button type="submit" class="btn btn-register">Continue</button>
            <p class="text-center text-muted mt-3">
                Already have an account? <a href="login.php">Login</a>
            </p>
        </form>
    </div>
</div>

<script>
    // Toggle password visibility functions
    function setupPasswordToggle(inputId, buttonId) {
        const input = document.getElementById(inputId);
        const button = document.getElementById(buttonId);
        
        if (input && button) {
            button.addEventListener('click', function() {
                const icon = this.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        }
    }

    // ✅ DYNAMIC PASSWORD FIELD SWITCHING
    function setupPasswordFieldSwitching() {
        const registerEmail = document.getElementById('registerEmail');
        const regularPasswordFields = document.getElementById('regularPasswordFields');
        const adminPasswordFields = document.getElementById('adminPasswordFields');
        const adminPasswordInput = document.getElementById('adminPassword');
        const confirmAdminPasswordInput = document.getElementById('confirmAdminPassword');
        const regularPasswordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirmPassword');

        function updateRequiredFields(isAdmin) {
            if (isAdmin) {
                // Admin mode - make admin fields required, regular fields not required
                adminPasswordInput.required = true;
                confirmAdminPasswordInput.required = true;
                regularPasswordInput.required = false;
                confirmPasswordInput.required = false;
            } else {
                // Regular mode - make regular fields required, admin fields not required
                regularPasswordInput.required = true;
                confirmPasswordInput.required = true;
                adminPasswordInput.required = false;
                confirmAdminPasswordInput.required = false;
            }
        }

        if (registerEmail && regularPasswordFields && adminPasswordFields) {
            registerEmail.addEventListener('input', function() {
                const email = this.value.toLowerCase();
                // Show admin password fields and hide regular fields for admin emails
                if (email.includes('@celesticare.admin.com')) {
                    regularPasswordFields.style.display = 'none';
                    adminPasswordFields.style.display = 'block';
                    updateRequiredFields(true);
                    
                    // Clear regular password fields
                    regularPasswordInput.value = '';
                    confirmPasswordInput.value = '';
                } else {
                    regularPasswordFields.style.display = 'block';
                    adminPasswordFields.style.display = 'none';
                    updateRequiredFields(false);
                    
                    // Clear admin password fields
                    adminPasswordInput.value = '';
                    confirmAdminPasswordInput.value = '';
                }
            });

            // Also check on page load in case of back navigation
            if (registerEmail.value.includes('@celesticare.admin.com')) {
                regularPasswordFields.style.display = 'none';
                adminPasswordFields.style.display = 'block';
                updateRequiredFields(true);
            } else {
                updateRequiredFields(false);
            }
        }

        // Setup all password toggles
        setupPasswordToggle('password', 'togglePassword');
        setupPasswordToggle('confirmPassword', 'toggleConfirmPassword');
        setupPasswordToggle('adminPassword', 'toggleAdminPassword');
        setupPasswordToggle('confirmAdminPassword', 'toggleConfirmAdminPassword');
    }

    // Initialize when page loads
    document.addEventListener('DOMContentLoaded', function() {
        setupPasswordFieldSwitching();
    });
</script>

</body>
</html>