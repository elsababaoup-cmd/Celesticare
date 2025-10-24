<?php
session_start();
include("../config/dbconfig.php");

// ✅ Enhanced admin check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Check if user ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "No user ID provided!";
    header("Location: manage_users.php");
    exit();
}

$user_id = mysqli_real_escape_string($conn, $_GET['id']);

// Fetch user data
$query = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Check if user exists
if (!$user) {
    $_SESSION['message'] = "User not found!";
    header("Location: manage_users.php");
    exit();
}

// Prevent editing admin accounts
if ($user['is_admin']) {
    $_SESSION['message'] = "Cannot edit admin accounts!";
    header("Location: manage_users.php");
    exit();
}

// Initialize feedback variables
$feedback_result = null;
$feedback_count = 0;

// Check if feedback table exists and fetch feedback
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'user_feedback'");
if (mysqli_num_rows($table_check) > 0) {
    // Feedback table exists, fetch user feedback
    $feedback_query = "SELECT * FROM user_feedback WHERE user_id = '$user_id' ORDER BY created_at DESC";
    $feedback_result = mysqli_query($conn, $feedback_query);
    $feedback_count = $feedback_result ? mysqli_num_rows($feedback_result) : 0;
    
    // Store all feedback in an array so we can use it multiple times
    $all_feedback = [];
    if ($feedback_result && $feedback_count > 0) {
        while ($row = mysqli_fetch_assoc($feedback_result)) {
            $all_feedback[] = $row;
        }
    }
} else {
    $all_feedback = [];
    $feedback_count = 0;
}

// Handle form submission
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['reset_password'])) {
        // Handle password reset
        $new_password = "CelestiCare123!";
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $reset_query = "UPDATE users SET password = '$hashed_password' WHERE id = '$user_id'";
        if (mysqli_query($conn, $reset_query)) {
            $_SESSION['message'] = "Password reset successfully! New password: CelestiCare123!";
            header("Location: manage_users.php");
            exit();
        } else {
            $error = "Error resetting password: " . mysqli_error($conn);
        }
        
    } else {
        // Handle regular form update
        $username = mysqli_real_escape_string($conn, trim($_POST['username']));
        $email = mysqli_real_escape_string($conn, trim($_POST['email']));
        $name = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ''));
        $gender = mysqli_real_escape_string($conn, trim($_POST['gender'] ?? ''));

        // Basic validation
        if (empty($username) || empty($email)) {
            $error = "Username and email are required!";
        } else {
            // Check if username or email already exists
            $check_query = "SELECT id FROM users WHERE (username = '$username' OR email = '$email') AND id != '$user_id'";
            $check_result = mysqli_query($conn, $check_query);
            
            if (mysqli_num_rows($check_result) > 0) {
                $error = "Username or email already exists!";
            } else {
                // Update user data
                $update_query = "UPDATE users SET 
                                username = '$username', 
                                email = '$email', 
                                name = '$name', 
                                gender = '$gender'
                                WHERE id = '$user_id'";
                
                if (mysqli_query($conn, $update_query)) {
                    $_SESSION['message'] = "User updated successfully!";
                    header("Location: manage_users.php");
                    exit();
                } else {
                    $error = "Error updating user: " . mysqli_error($conn);
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
    <title>Edit User - CelestiCare Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #2e2e2e 0%, #3b3b3b 100%);
            font-family: 'Poppins', sans-serif;
            color: #f0f0f0;
        }

        .admin-container {
            min-height: 100vh;
            padding: 20px 0;
            position: relative;
            overflow-x: hidden;
        }

        /* Floating light glow accents */
        .bg-accent {
            position: fixed;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.2;
            z-index: 0;
            animation: float 10s ease-in-out infinite alternate;
        }

        .bg-accent.one { background: #bba6ff; width: 400px; height: 400px; top: -80px; left: -80px; }
        .bg-accent.two { background: #d2b0ff; width: 500px; height: 500px; bottom: -100px; right: -100px; }

        @keyframes float {
            from { transform: translateY(0); }
            to { transform: translateY(25px); }
        }

        /* Header styling */
        .admin-header {
            background: linear-gradient(135deg, rgba(75, 63, 119, 0.8) 0%, rgba(107, 91, 149, 0.8) 100%);
            backdrop-filter: blur(10px);
            color: white;
            padding: 40px 30px;
            margin-bottom: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 2;
        }

        .brand-title {
            font-weight: 700;
            font-size: 2.2rem;
            letter-spacing: 2px;
            color: #fff;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
        }

        .admin-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 12px 20px;
            border-radius: 15px;
            backdrop-filter: blur(5px);
        }

        .btn-back {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            padding: 8px 20px;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background-color: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
        }

        /* Form container */
        .form-container {
            background: linear-gradient(135deg, rgba(232, 228, 242, 0.9) 0%, rgba(198, 185, 232, 0.9) 100%);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            padding: 30px;
            position: relative;
            z-index: 2;
            color: #2e2e2e;
        }

        .section-title {
            color: #4b3f77;
            font-weight: 600;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #4b3f77;
        }

        /* Form styling */
        .form-control {
            border-radius: 12px;
            border: 1px solid rgba(75, 63, 119, 0.2);
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #4b3f77;
            box-shadow: 0 0 0 0.2rem rgba(75, 63, 119, 0.25);
            background: white;
        }

        .form-label {
            font-weight: 500;
            color: #4b3f77;
            margin-bottom: 8px;
        }

        /* Button styling */
        .btn-primary {
            background: linear-gradient(135deg, #4b3f77 0%, #6b5b95 100%);
            border: none;
            border-radius: 15px;
            padding: 12px 30px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(75, 63, 119, 0.4);
        }

        .btn-warning {
            background: linear-gradient(135deg, #ff9800 0%, #ffc107 100%);
            border: none;
            border-radius: 15px;
            padding: 12px 30px;
            font-weight: 500;
            transition: all 0.3s ease;
            color: #2e2e2e;
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 152, 0, 0.4);
        }

        /* Alert styling */
        .alert {
            border-radius: 15px;
            border: none;
            position: relative;
            z-index: 2;
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.9) 0%, rgba(232, 62, 140, 0.9) 100%);
            color: white;
        }

        /* User info cards */
        .info-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            color: #000000;
        }

        .info-card h5 {
            color: #4b3f77;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .info-card p {
            color: #000000;
            margin-bottom: 8px;
        }

        .info-card strong {
            color: #000000;
        }

        .badge {
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 12px;
        }

        .badge.bg-primary {
            background: linear-gradient(135deg, #4b3f77 0%, #6b5b95 100%) !important;
        }

        .badge.bg-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #868e96 100%) !important;
        }

        /* Text muted for not completed quizzes */
        .text-muted {
            color: #6c757d !important;
        }

        /* Feedback section styling */
        .feedback-container {
            background: linear-gradient(135deg, rgba(232, 228, 242, 0.9) 0%, rgba(198, 185, 232, 0.9) 100%);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            padding: 30px;
            margin-top: 30px;
            position: relative;
            z-index: 2;
            color: #2e2e2e;
        }

        .feedback-item {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #4b3f77;
        }

        .feedback-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .feedback-rating {
            color: #ffc107;
            font-weight: 600;
        }

        .feedback-date {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .feedback-message {
            color: #2e2e2e;
            line-height: 1.5;
        }

        .empty-feedback {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }

        .empty-feedback i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #4b3f77;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .admin-header {
                padding: 15px 0;
            }
            
            .brand-title {
                font-size: 1.8rem;
            }
            
            .form-container {
                padding: 20px;
            }
            
            .feedback-container {
                padding: 20px;
            }
            
            .feedback-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="bg-accent one"></div>
        <div class="bg-accent two"></div>
        
        <div class="container">
            <!-- Admin Header -->
            <div class="admin-header mb-5">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h1 class="brand-title mb-1"><i class="fas fa-user-edit me-3"></i>EDIT USER</h1>
                        <p class="mb-0 opacity-75">Editing: <?= htmlspecialchars($user['username']); ?></p>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="admin-info d-inline-block">
                            <strong>Welcome, <?= htmlspecialchars($_SESSION['admin_email']); ?></strong>
                            <a href="manage_users.php" class="btn btn-back btn-sm ms-3">
                                <i class="fas fa-arrow-left me-1"></i> Back to Users
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error Message -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <?= $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Edit Form -->
                <div class="col-lg-8 mb-4">
                    <div class="form-container">
                        <h3 class="section-title"><i class="fas fa-edit me-2"></i>Edit User Information</h3>
                        
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">Username *</label>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?= htmlspecialchars($user['username']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= htmlspecialchars($user['email']); ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?= htmlspecialchars($user['name'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-control" id="gender" name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="Masculine" <?= ($user['gender'] ?? '') == 'Masculine' ? 'selected' : '' ?>>Masculine</option>
                                        <option value="Feminine" <?= ($user['gender'] ?? '') == 'Feminine' ? 'selected' : '' ?>>Feminine</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row mt-4">
                                <div class="col-md-6 mb-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-save me-2"></i> Update User
                                    </button>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <button type="submit" name="reset_password" class="btn btn-warning w-100" 
                                            onclick="return confirm('Reset password to default? The new password will be: CelestiCare123!')">
                                        <i class="fas fa-key me-2"></i> Reset Password
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- User Information -->
                <div class="col-lg-4">
                    <div class="info-card">
                        <h5><i class="fas fa-info-circle me-2"></i>Account Info</h5>
                        <p><strong>User ID:</strong> <?= $user['id'] ?></p>
                        <p><strong>Registration:</strong> <?= date('M j, Y', strtotime($user['created_at'])) ?></p>
                        <p><strong>Last Updated:</strong> <?= date('M j, Y', strtotime($user['updated_at'] ?? $user['created_at'])) ?></p>
                        <p><strong>Type:</strong> <span class="badge bg-secondary">Regular User</span></p>
                    </div>

                    <div class="info-card">
                        <h5><i class="fas fa-chart-bar me-2"></i>Quiz Results</h5>
                        <p><strong>Aesthetic:</strong> 
                            <?= !empty($user['aesthetic_result']) ? 
                                '<span class="badge bg-primary">' . ucfirst($user['aesthetic_result']) . '</span>' : 
                                '<span class="text-muted">Not completed</span>' ?>
                        </p>
                        <p><strong>Style:</strong> 
                            <?= !empty($user['style_result']) ? 
                                '<span class="badge bg-primary">' . ucfirst($user['style_result']) . '</span>' : 
                                '<span class="text-muted">Not completed</span>' ?>
                        </p>
                        <p><strong>Zodiac:</strong> <?= htmlspecialchars($user['zodiac_sign'] ?? 'Not set') ?></p>
                    </div>
                </div>
            </div>

            <!-- Feedback Section -->
            <div class="feedback-container">
                <h3 class="section-title"><i class="fas fa-comments me-2"></i>User Feedback</h3>
                
                <?php if ($feedback_count > 0): ?>
                    <div class="feedback-stats mb-4">
                        <p class="mb-2"><strong>Total Feedback:</strong> <?= $feedback_count ?> submission(s)</p>
                    </div>
                    
                    <?php while ($feedback = mysqli_fetch_assoc($feedback_result)): ?>
                        <div class="feedback-item">
                            <div class="feedback-header">
                                <div class="feedback-rating">
                                    <?php 
                                    // Display star rating
                                    $rating = $feedback['rating'] ?? 0;
                                    for ($i = 1; $i <= 5; $i++): 
                                        if ($i <= $rating): ?>
                                            <i class="fas fa-star text-warning"></i>
                                        <?php else: ?>
                                            <i class="far fa-star text-warning"></i>
                                        <?php endif;
                                    endfor; ?>
                                    <span class="ms-2">Rating: <?= $rating ?>/5</span>
                                </div>
                                <div class="feedback-date">
                                    <?= date('M j, Y g:i A', strtotime($feedback['created_at'])) ?>
                                </div>
                            </div>
                            <div class="feedback-message">
                                <p class="mb-0"><?= htmlspecialchars($feedback['message'] ?? 'No message provided') ?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-feedback">
                        <i class="fas fa-comment-slash"></i>
                        <h4>No Feedback Yet</h4>
                        <p class="mb-0">This user hasn't submitted any feedback yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Feedback Section -->
    <div class="feedback-container">
        <h3 class="section-title"><i class="fas fa-comments me-2"></i>User Feedback (<?= $feedback_count ?>)</h3>
        
        <?php if ($feedback_count > 0): ?>
            <?php foreach ($all_feedback as $feedback): ?>
                <div class="feedback-item">
                    <div class="feedback-header">
                        <div class="feedback-rating">
                            <?php 
                            // Display star rating for experience
                            $experience = $feedback['experience'] ?? 0;
                            for ($i = 1; $i <= 5; $i++): 
                                if ($i <= $experience): ?>
                                    <i class="fas fa-star text-warning"></i>
                                <?php else: ?>
                                    <i class="far fa-star text-warning"></i>
                                <?php endif;
                            endfor; ?>
                            <span class="ms-2">Overall Experience: <?= $experience ?>/5</span>
                        </div>
                        <div class="feedback-date">
                            <?= date('M j, Y g:i A', strtotime($feedback['created_at'])) ?>
                        </div>
                    </div>
                    
                    <div class="feedback-details mt-3">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Fashion Match:</strong> 
                                    <span class="badge bg-info"><?= htmlspecialchars($feedback['fashion_match'] ?? 'Not specified') ?></span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Favorite Feature:</strong> 
                                    <span class="badge bg-primary"><?= htmlspecialchars($feedback['favorite_feature'] ?? 'Not specified') ?></span>
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Website Vibe:</strong> 
                                    <span class="badge bg-success">"<?= htmlspecialchars($feedback['vibe'] ?? 'Not specified') ?>"</span>
                                </p>
                            </div>
                        </div>
                        
                        <?php if (!empty($feedback['suggestions'])): ?>
                            <div class="suggestions mt-3">
                                <p><strong>Suggestions & Comments:</strong></p>
                                <div class="p-3 bg-light rounded">
                                    <?= nl2br(htmlspecialchars($feedback['suggestions'])) ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mt-2"><em>No additional suggestions provided.</em></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-feedback">
                <i class="fas fa-comment-slash"></i>
                <h4>No Feedback Yet</h4>
                <p class="mb-0">This user hasn't submitted any feedback yet.</p>
            </div>
        <?php endif; ?>
    </div>

    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>