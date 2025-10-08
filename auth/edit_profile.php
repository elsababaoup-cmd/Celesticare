<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("../config/dbconfig.php");

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$message = "";

// Fetch user info
$sql = "SELECT * FROM users WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];

    if (!empty($_POST["password"])) {
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
        $sql = "UPDATE users SET username=?, email=?, password=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $username, $email, $password, $user_id);
    } else {
        $sql = "UPDATE users SET username=?, email=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $username, $email, $user_id);
    }

    if ($stmt->execute()) {
        // Update session so dashboard shows the new username/email
        $_SESSION['username'] = $username;

        $message = "<div class='alert alert-success'>Profile updated successfully.</div>";
        $user['username'] = $username;
        $user['email'] = $email;
    } else {
        $message = "<div class='alert alert-danger'>Error updating profile: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile - CelestiCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include("../includes/navbar.php"); ?>

<div class="container mt-5">
    <div class="card p-4 shadow-sm">
        <h2>Edit Profile</h2>
        <?php if (!empty($message)) echo $message; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" 
                       value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" 
                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label>New Password (leave blank to keep current)</label>
                <input type="password" name="password" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="../dashboard/index.php" class="btn btn-secondary">Back to Dashboard</a>
        </form>
    </div>
</div>

</body>
</html>
