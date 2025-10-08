<?php
session_start();
include("../config/dbconfig.php");

// Ensure logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$result = mysqli_query($conn, "SELECT id, username, email FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php
// ✅ ALERT BLOCK — place this RIGHT AFTER <body>
if (isset($_GET['deleted']) && $_GET['deleted'] == "success") {
    echo "<div class='alert alert-success text-center m-0 p-2'>✅ User deleted successfully.</div>";
} elseif (isset($_GET['deleted']) && $_GET['deleted'] == "failed") {
    echo "<div class='alert alert-danger text-center m-0 p-2'>❌ Failed to delete user.</div>";
} elseif (isset($_GET['error']) && $_GET['error'] == "cantdeleteyourself") {
    echo "<div class='alert alert-warning text-center m-0 p-2'>⚠️ You cannot delete your own account here.</div>";
}
?>

<div class="container mt-5">
    <h2 class="mb-3">User List</h2>

    <table class="table table-bordered table-striped shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= htmlspecialchars($row['username']); ?></td>
                    <td><?= htmlspecialchars($row['email']); ?></td>
                    <td>
                        <a href="delete_user.php?id=<?= $row['id']; ?>" 
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Are you sure you want to delete this user?');">
                           Delete
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <a href="../dashboard/index.php" class="btn btn-secondary">Back to Dashboard</a>
</div>

</body>
</html>
