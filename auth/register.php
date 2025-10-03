<?php
session_start();
include __DIR__ . "/../config/dbconfig.php";

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $zodiac   = trim($_POST['zodiac'] ?? null);

    // basic validation
    if ($username === '' || strlen($username) < 3) $errors[] = "Enter a username (min 3 chars).";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Enter a valid email.";
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($username === '' || strlen($username) < 3) $errors[] = "Enter a username (min 3 chars).";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Enter a valid email.";
    
        if ($password === '' || $password2 === '') {
            $errors[] = "Enter both password fields.";
        } elseif (strlen($password) < 6) {
            $errors[] = "Password must be at least 6 chars.";
        } elseif ($password !== $password2) {
            $errors[] = "Passwords do not match.";
        }
    }

    if (empty($errors)) {
        // check duplicate
        $sql = "SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $username, $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = "Username or email already taken.";
            mysqli_stmt_close($stmt);
        } else {
            mysqli_stmt_close($stmt);
            // insert
            $pw_hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = "INSERT INTO users (username, email, password, zodiac_sign) VALUES (?, ?, ?, ?)";
            $ins = mysqli_prepare($conn, $insert);
            mysqli_stmt_bind_param($ins, "ssss", $username, $email, $pw_hash, $zodiac);
            if (mysqli_stmt_execute($ins)) {
                mysqli_stmt_close($ins);
                header("Location: login.php?registered=1");
                exit;
            } else {
                $errors[] = "Database error: could not create account.";
                mysqli_stmt_close($ins);
            }
        }
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Register</title></head>
<body>
  <h2>Register</h2>
  <?php if (!empty($errors)): ?>
    <div style="color:#900;">
      <ul><?php foreach($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?></ul>
    </div>
  <?php endif; ?>
  <form method="post" action="">
    <label>Username<br><input name="username" required></label><br>
    <label>Email<br><input name="email" type="email" required></label><br>
    <label>Zodiac (optional)<br><input name="zodiac"></label><br>
    <label>Password<br><input name="password" type="password" required></label><br>
    <label>Confirm Password<br><input name="password2" type="password" required></label><br>
    <button type="submit">Register</button>
  </form>
  <p><a href="login.php">Already have an account? Login</a></p>
</body></html>
