<?php
session_start();
include("../config/dbconfig.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Get existing user data
$query = "SELECT name, email, username, gender, birthdate, zodiac_sign, undertone, season 
          FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found.");
}

// ✅ Helper: compute zodiac from birthdate
function getZodiacSign($month, $day) {
    $zodiacs = [
        ["Capricorn", 1222, 119],
        ["Aquarius", 120, 218],
        ["Pisces", 219, 320],
        ["Aries", 321, 419],
        ["Taurus", 420, 520],
        ["Gemini", 521, 620],
        ["Cancer", 621, 722],
        ["Leo", 723, 822],
        ["Virgo", 823, 922],
        ["Libra", 923, 1022],
        ["Scorpio", 1023, 1121],
        ["Sagittarius", 1122, 1221],
    ];
    $md = intval(sprintf("%02d%02d", $month, $day));
    foreach ($zodiacs as [$sign, $start, $end]) {
        if (($md >= $start && $md <= $end) || ($start > $end && ($md >= $start || $md <= $end))) {
            return $sign;
        }
    }
    return "Capricorn";
}

// ✅ Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name'] ?? '');
    $birthdate = trim($_POST['birthdate'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $zodiac_sign = trim($_POST['zodiac_sign'] ?? '');

    // If birthdate is set but no zodiac provided, compute it
    if (!empty($birthdate) && empty($zodiac_sign)) {
        $date = date_create($birthdate);
        $zodiac_sign = getZodiacSign(date_format($date, "m"), date_format($date, "d"));
    }

    // ✅ Update only editable fields
    $update = $conn->prepare("UPDATE users 
        SET name = ?, birthdate = ?, gender = ?, zodiac_sign = ? 
        WHERE id = ?");
    $update->bind_param("ssssi", $name, $birthdate, $gender, $zodiac_sign, $user_id);

    if ($update->execute()) {
        $_SESSION['message'] = "Profile updated successfully!";
        header("Location: index.php");
        exit();
    } else {
        echo "<div class='alert alert-danger text-center'>Error updating profile: " . htmlspecialchars($update->error) . "</div>";
    }
}
?>
