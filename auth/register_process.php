<?php
include("../config/dbconfig.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        echo "<script>alert('All fields are required.'); window.history.back();</script>";
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format.'); window.history.back();</script>";
        exit();
    }

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.'); window.history.back();</script>";
        exit();
    }

    // ✅ Check if email already exists
    $check_query = "SELECT id FROM users WHERE email = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        echo "<script>alert('This email is already registered.'); window.history.back();</script>";
        exit();
    }

    // ✅ Get any existing data from session (from get_to_know.php or zodiac result)
    $name = $_SESSION['name'] ?? null;
    $birthdate = $_SESSION['birthdate'] ?? null;
    $gender = $_SESSION['gender'] ?? null;
    $zodiac_sign = $_SESSION['zodiac_sign'] ?? null;
    $undertone = $_SESSION['undertone'] ?? null;
    $season = $_SESSION['season'] ?? null;

    // ✅ Insert new user with optional fields
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $query = "INSERT INTO users (username, email, password, name, birthdate, gender, zodiac_sign, undertone, season)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sssssssss", $username, $email, $hashed_password, $name, $birthdate, $gender, $zodiac_sign, $undertone, $season);

    if (mysqli_stmt_execute($stmt)) {
        // ✅ Get user ID and save to session for auto-login
        $user_id = mysqli_insert_id($conn);
        $_SESSION['user_id'] = $user_id;

        echo "<script>
            alert('Registration successful! Your details have been saved.');
            window.location.href='../dashboard/index.php';
        </script>";
        exit();
    } else {
        echo "<script>alert('Database error. Please try again later.'); window.history.back();</script>";
        exit();
    }
}
?>
