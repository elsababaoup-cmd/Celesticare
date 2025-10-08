<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/dbconfig.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - CelestiCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include("../includes/navbar.php"); ?>

<div class="container mt-5">
    <div class="card p-4 shadow-sm">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
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
