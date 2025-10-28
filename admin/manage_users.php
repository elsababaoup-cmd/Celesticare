<?php
session_start();
include("../config/dbconfig.php");

// ✅ Enhanced admin check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Handle admin logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin_login.php");
    exit();
}

// Handle user deletion
if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    
    // Prevent admin from deleting themselves
    if ($delete_id != $_SESSION['admin_id']) {
        $delete_query = "DELETE FROM users WHERE id = '$delete_id'";
        if (mysqli_query($conn, $delete_query)) {
            $_SESSION['message'] = "User deleted successfully!";
        } else {
            $_SESSION['message'] = "Error deleting user: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['message'] = "You cannot delete your own admin account!";
    }
    header("Location: manage_users.php");
    exit();
}

// Fetch all users ordered by registration date (oldest first)
$result = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - CelestiCare Admin</title>
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

        .btn-logout {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            padding: 8px 20px;
            transition: all 0.3s ease;
        }

        .btn-logout:hover {
            background-color: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
        }

        /* Card styling */
        .card {
            background: linear-gradient(135deg, rgba(232, 228, 242, 0.9) 0%, rgba(198, 185, 232, 0.9) 100%);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            color: #2e2e2e;
            transition: transform 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card.bg-primary .card-body { background: linear-gradient(135deg, #4b3f77 0%, #6b5b95 100%); color: white; }
        .card.bg-success .card-body { background: linear-gradient(135deg, #3a7d44 0%, #4caf50 100%); color: white; }
        .card.bg-warning .card-body { background: linear-gradient(135deg, #ff9800 0%, #ffc107 100%); color: white; }

        .card-title {
            font-weight: 600;
            font-size: 1.8rem;
        }

        /* Table styling */
        .table-container {
            background: linear-gradient(135deg, rgba(232, 228, 242, 0.9) 0%, rgba(198, 185, 232, 0.9) 100%);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            position: relative;
            z-index: 2;
        }

        .table {
            margin-bottom: 0;
            color: #0b0909ff;
        }

        .table thead th {
            background: linear-gradient(135deg, #4b3f77 0%, #6b5b95 100%);
            color: white;
            border: none;
            padding: 15px;
            font-weight: 600;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.7);
            transform: translateY(-2px);
        }

        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid rgba(75, 63, 119, 0.1);
        }

        /* Badge styling */
        .badge {
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 12px;
        }

        .badge.bg-info {
            background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%) !important;
        }

        .badge.bg-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #868e96 100%) !important;
        }

        .badge.bg-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
        }

        /* Button styling */
        .btn {
            border-radius: 15px;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 8px 16px;
        }

        .btn-warning {
            background: linear-gradient(135deg, #ff9800 0%, #ffc107 100%);
            border: none;
            color: #2e2e2e;
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 152, 0, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
            border: none;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #868e96 100%);
            border: none;
        }

        /* Alert styling */
        .alert {
            border-radius: 15px;
            border: none;
            position: relative;
            z-index: 2;
        }

        .alert-info {
            background: linear-gradient(135deg, rgba(23, 162, 184, 0.9) 0%, rgba(32, 201, 151, 0.9) 100%);
            color: white;
        }

        /* Empty state styling */
        .empty-state {
            background: linear-gradient(135deg, rgba(232, 228, 242, 0.9) 0%, rgba(198, 185, 232, 0.9) 100%);
            border-radius: 20px;
            padding: 60px 30px;
            text-align: center;
            color: #2e2e2e;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }

        .empty-state i {
            color: #4b3f77;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: #191919ff; /* Changed to black text */
        }

        .empty-state p {
            color: #2e2e2e; /* Changed to black text */
        }

        /* Additional styling for quiz results and account info */
        .table strong {
            color: #2e2e2e; /* Changed to black text */
        }

        .user-actions .btn {
            color: #2e2e2e; /* Ensure button text is black where appropriate */
        }

        /* User number styling */
        .user-number {
            font-weight: 600;
            color: #4b3f77;
            background: rgba(75, 63, 119, 0.1);
            padding: 4px 8px;
            border-radius: 8px;
            font-size: 0.9rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .admin-header {
                padding: 15px 0;
            }
            
            .brand-title {
                font-size: 1.8rem;
            }
            
            .table-responsive {
                font-size: 0.9rem;
            }
            
            .btn {
                padding: 6px 12px;
                font-size: 0.85rem;
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
                        <h1 class="brand-title mb-1"><i class="fas fa-users-cog me-3"></i>CELESTICARE</h1>
                        <p class="mb-0 opacity-75">Admin Panel - User Management</p>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="admin-info d-inline-block">
                            <strong>Welcome, <?= htmlspecialchars($_SESSION['admin_email']); ?></strong>
                            <a href="?logout=true" class="btn btn-logout btn-sm ms-3">
                                <i class="fas fa-sign-out-alt me-1"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success/Error Messages -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
                    <?php 
                        echo $_SESSION['message']; 
                        unset($_SESSION['message']); 
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- User Statistics -->
            <div class="row mb-5">
                <div class="col-md-4 mb-3">
                    <div class="card bg-primary h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">
                                        <?php 
                                            $count_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
                                            $count_row = mysqli_fetch_assoc($count_result);
                                            echo $count_row['total'];
                                        ?>
                                    </h4>
                                    <p class="card-text mb-0">Total Users</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card bg-success h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">
                                        <?php 
                                            $admin_count = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE is_admin = TRUE");
                                            $admin_row = mysqli_fetch_assoc($admin_count);
                                            echo $admin_row['total'];
                                        ?>
                                    </h4>
                                    <p class="card-text mb-0">Admin Users</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-user-shield fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card bg-warning h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">
                                        <?php 
                                            $regular_count = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE is_admin = FALSE");
                                            $regular_row = mysqli_fetch_assoc($regular_count);
                                            echo $regular_row['total'];
                                        ?>
                                    </h4>
                                    <p class="card-text mb-0">Regular Users</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-user fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="table-container mb-5">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th>User #</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>User Type</th>
                                <th>Registration Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                        $user_number = 1;
                        while ($row = mysqli_fetch_assoc($result)): 
                        ?>
                            <tr>
                                <td>
                                    <span class="user-number">#<?= $user_number; ?></span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($row['username']); ?></strong>
                                    <?php if ($row['id'] == $_SESSION['admin_id']): ?>
                                        <span class="badge bg-success ms-1">You</span>
                                    <?php elseif ($row['is_admin']): ?>
                                        <span class="badge bg-info ms-1">Admin</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['email']); ?></td>
                                <td>
                                    <?php if ($row['is_admin']): ?>
                                        <span class="badge bg-info">Administrator</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Regular User</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('M j, Y g:i A', strtotime($row['created_at'])); ?></td>
                                <td class="user-actions">
                                    <?php if (!$row['is_admin']): ?>
                                        <!-- Show Edit button only for regular users -->
                                        <a href="edit_user.php?id=<?= $row['id']; ?>" 
                                           class="btn btn-sm btn-warning" 
                                           title="Edit User">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    <?php else: ?>
                                        <!-- Disabled button for admin accounts -->
                                        <button class="btn btn-sm btn-secondary" disabled title="Cannot edit admin accounts">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    <?php endif; ?>
                                    
                                    <?php if ($row['id'] != $_SESSION['admin_id'] && !$row['is_admin']): ?>
                                        <a href="?delete_id=<?= $row['id']; ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Are you sure you want to delete user: <?= htmlspecialchars($row['username']); ?>? This action cannot be undone.');"
                                           title="Delete User">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-secondary" disabled title="Cannot delete admin accounts">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php 
                        $user_number++;
                        endwhile; 
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php if (mysqli_num_rows($result) === 0): ?>
                <div class="empty-state">
                    <i class="fas fa-users fa-3x mb-3"></i>
                    <h3 class="text-dark">No Users Found</h3>
                    <p class="text-muted">There are no users registered in the system yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>