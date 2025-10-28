<?php
session_start();
include("../config/dbconfig.php");

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
    
    // ✅ CRITICAL: Save pre-login session data BEFORE registration
    $pre_login_data = [
        'name' => $_SESSION['name'] ?? null,
        'birthdate' => $_SESSION['birthdate'] ?? null,
        'gender' => $_SESSION['gender'] ?? null,
        'zodiac_sign' => $_SESSION['zodiac_sign'] ?? null,
        'undertone' => $_SESSION['undertone'] ?? null
    ];
    
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
        echo "error:Username and email are required.";
        exit();
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "error:Invalid email format.";
        exit();
    } else {
        if ($is_admin_email) {
            // ✅ Admin email registration - require admin passwords
            if (empty($password) || empty($confirm_password)) {
                echo "error:Admin password fields are required.";
                exit();
            } elseif ($password !== $confirm_password) {
                echo "error:Admin passwords do not match!";
                exit();
            } elseif ($password !== ADMIN_SECRET_KEY) {
                echo "error:Invalid admin password for admin email registration.";
                exit();
            } else {
                // Admin email validation passed
                $valid_admin_email = true;
            }
        } else {
            // ✅ Regular user email - check if domain is allowed
            $email_domain = strtolower(substr(strrchr($email, "@"), 1));
            if (!in_array($email_domain, $allowed_domains)) {
                echo "error:Only Gmail, Yahoo, Hotmail, and Outlook emails are allowed.";
                exit();
            } else {
                $valid_regular_email = true;
            }
        }

        // If email validation passed, continue with password checks
        if (isset($valid_admin_email) || isset($valid_regular_email)) {
            if ($password !== $confirm_password) {
                echo "error:Passwords do not match!";
                exit();
            } else {
                // Validate password: no spaces, minimum 8 characters, at least one uppercase letter
                // For admin, skip regular password validation since they use the admin secret key
                if (!$is_admin_email) {
                    if (strpos($password, ' ') !== false) {
                        echo "error:Password cannot contain spaces.";
                        exit();
                    } elseif (strlen($password) < 8) {
                        echo "error:Password must be at least 8 characters long.";
                        exit();
                    } elseif (!preg_match('/[A-Z]/', $password)) {
                        echo "error:Password must contain at least one uppercase letter.";
                        exit();
                    }
                }
                
                // If no password errors, proceed
                $check_query = "SELECT id FROM users WHERE email='$email' LIMIT 1";
                $check_result = mysqli_query($conn, $check_query);

                if (mysqli_num_rows($check_result) > 0) {
                    echo "error:This email is already registered.";
                    exit();
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $is_admin_value = $is_admin_email ? 1 : 0;
                    
                    // ✅ MODIFIED: Insert user WITH pre-login quiz data
                    $query = "INSERT INTO users (username, email, password, is_admin, name, birthdate, gender, zodiac_sign, undertone) 
                             VALUES ('$username', '$email', '$hashed_password', '$is_admin_value', 
                                    '" . mysqli_real_escape_string($conn, $pre_login_data['name']) . "', 
                                    '" . mysqli_real_escape_string($conn, $pre_login_data['birthdate']) . "', 
                                    '" . mysqli_real_escape_string($conn, $pre_login_data['gender']) . "', 
                                    '" . mysqli_real_escape_string($conn, $pre_login_data['zodiac_sign']) . "', 
                                    '" . mysqli_real_escape_string($conn, $pre_login_data['undertone']) . "')";
                    
                    if (mysqli_query($conn, $query)) {
                        // Get the new user ID
                        $user_id = mysqli_insert_id($conn);
                        
                        // ✅ CRITICAL: Regenerate session ID for security
                        session_regenerate_id(true);
                        
                        // Start session for the new user
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['username'] = $username;
                        $_SESSION['is_admin'] = $is_admin_email;
                        
                        // ✅ CRITICAL: Keep pre-login data in the new session
                        foreach ($pre_login_data as $key => $value) {
                            if ($value) $_SESSION[$key] = $value;
                        }
                        
                        if ($is_admin_email) {
                            $_SESSION['admin_email'] = $email;
                            $_SESSION['admin_logged_in'] = true;
                            $_SESSION['admin_id'] = $user_id;
                        }
                        
                        // AJAX response - same format as regular register
                        if ($is_admin_email) {
                            echo "success:../admin/manage_users.php";
                        } else {
                            echo "success:../dashboard/index.php";
                        }
                    } else {
                        echo "error:Database error. Please try again.";
                    }
                }
            }
        }
    }
} else {
    echo "error:Invalid request method.";
}
?>