<?php
session_start();
include("../includes/navbar.php");
include("../config/dbconfig.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Fetch user's quiz results safely
$stmt = $conn->prepare("SELECT aesthetic_result, style_result FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// ✅ Debugging step (temporary, remove later)
if (!$user) {
    die("⚠️ No user found with ID: $user_id");
}

$aesthetic_result = trim($user['aesthetic_result'] ?? '');
$style_result = trim($user['style_result'] ?? '');

// ✅ Only redirect if NULL (not empty string)
if (is_null($aesthetic_result) || $aesthetic_result === '') {
    header("Location: ../quizzes/aesthetic_welcome.php");
    exit();
}
var_dump($style_result);
exit;


//if (is_null($style_result) || $style_result === '') {
//    echo "<script>console.log('Redirecting because style_result is empty');</script>";
//    header("Location: ../quizzes/style_welcome.php");
//    exit();
//}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Moodboard | CelestiCare</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2 class="text-center">🌸 Welcome to Your Moodboard</h2>
  <p class="text-center">
    Aesthetic: <strong><?= htmlspecialchars($aesthetic_result) ?></strong><br>
    Style: <strong><?= htmlspecialchars($style_result) ?></strong>
  </p>
</div>
</body>
</html>
