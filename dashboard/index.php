<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/dbconfig.php");
include(__DIR__ . "/../includes/navbar.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - CelestiCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow-y: scroll; /* ✅ enable scrolling */
            scrollbar-width: none; /* ✅ hide scrollbar in Firefox */
            -ms-overflow-style: none; /* ✅ hide scrollbar in IE/Edge */
            background: linear-gradient(135deg, #d3cce3 0%, #e9e4f0 100%);
            font-family: 'Poppins', sans-serif;
        }

        body::-webkit-scrollbar {
            display: none; /* ✅ hide scrollbar in Chrome/Safari */
        }

        .card {
            border-radius: 15px;
            background-color: #fff;
        }

        h2 {
            color: #6a1b9a;
            font-weight: 600;
        }

        .btn-warning {
            background-color: #f7b731;
            border: none;
            transition: background 0.3s ease;
        }

        .btn-warning:hover {
            background-color: #f9ca4f;
        }

        .btn-danger {
            background-color: #e74c3c;
            border: none;
            transition: background 0.3s ease;
        }

        .btn-danger:hover {
            background-color: #ff6b5a;
        }
    </style>
</head>

<body>
<div class="container mt-5 mb-5">
    <div class="card p-4 shadow-sm">
        <h2>The Universe Welcomes Your Presence, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p>Email:
            <?php 
                // Fetch email directly from DB in case it was updated
                $user_id = $_SESSION['user_id'];
                $query = "SELECT email FROM users WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                echo htmlspecialchars($user['email']);
            ?>
        </p>
        <p>This is your dashboard where your zodiac style journey begins.</p>
        <div class="d-flex gap-2">
            <a href="../auth/edit_profile.php" class="btn btn-warning">Edit Profile</a>
            <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
</div>

</body>
</html>
