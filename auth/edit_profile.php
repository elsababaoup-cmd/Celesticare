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
        $_SESSION['username'] = $username;
        $message = "<div class='alert alert-success text-center mt-2'>Profile updated successfully.</div>";
        $user['username'] = $username;
        $user['email'] = $email;
    } else {
        $message = "<div class='alert alert-danger text-center mt-2'>Error updating profile: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Font to match your login/register -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #d3cce3 0%, #e9e4f0 100%);
            min-height: 100vh;
        }

        .card {
            max-width: 500px;
            margin: 80px auto;
            background-color: rgba(255, 255, 255, 0.95);
            border: none;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(8px);
        }

        .card h2 {
            font-weight: 600;
            color: #4c3a73;
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            font-weight: 500;
            color: #4c3a73;
        }

        .form-control {
            border-radius: 10px;
            border: 1px solid #ccc;
        }

        .btn-primary {
            background-color: #7b68ee;
            border: none;
            border-radius: 10px;
            width: 100%;
            font-weight: 500;
        }

        .btn-primary:hover {
            background-color: #6b5b95;
        }

        .btn-secondary {
            background-color: #ccc;
            border: none;
            border-radius: 10px;
            width: 100%;
            font-weight: 500;
        }

        .alert {
            border-radius: 10px;
        }
    </style>
</head>
<body>

<?php include("../includes/navbar.php"); ?>

<div class="container">
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
            <button type="submit" class="btn btn-primary mb-2">Save Changes</button>
            <a href="../dashboard/index.php" class="btn btn-secondary">Back to Dashboard</a>
        </form>
    </div>
</div>

</body>
</html>
