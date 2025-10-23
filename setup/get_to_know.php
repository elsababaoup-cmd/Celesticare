<?php
session_start();

// ✅ Handle form submission BEFORE any HTML output
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    include(__DIR__ . "/../config/dbconfig.php"); // ✅ For DB updates

    $name = trim($_POST['name']);
    $birthdate = trim($_POST['birthdate']);
    $gender = trim($_POST['gender']);

    // Save all to session (works even if not logged in)
    $_SESSION['name'] = $name;
    $_SESSION['birthdate'] = $birthdate;
    $_SESSION['gender'] = $gender;

    // ✅ Calculate zodiac sign from birthdate
    $month = date('m', strtotime($birthdate));
    $day = date('d', strtotime($birthdate));
    $zodiac_sign = '';

    if (($month == 3 && $day >= 21) || ($month == 4 && $day <= 19)) $zodiac_sign = 'Aries';
    elseif (($month == 4 && $day >= 20) || ($month == 5 && $day <= 20)) $zodiac_sign = 'Taurus';
    elseif (($month == 5 && $day >= 21) || ($month == 6 && $day <= 20)) $zodiac_sign = 'Gemini';
    elseif (($month == 6 && $day >= 21) || ($month == 7 && $day <= 22)) $zodiac_sign = 'Cancer';
    elseif (($month == 7 && $day >= 23) || ($month == 8 && $day <= 22)) $zodiac_sign = 'Leo';
    elseif (($month == 8 && $day >= 23) || ($month == 9 && $day <= 22)) $zodiac_sign = 'Virgo';
    elseif (($month == 9 && $day >= 23) || ($month == 10 && $day <= 22)) $zodiac_sign = 'Libra';
    elseif (($month == 10 && $day >= 23) || ($month == 11 && $day <= 21)) $zodiac_sign = 'Scorpio';
    elseif (($month == 11 && $day >= 22) || ($month == 12 && $day <= 21)) $zodiac_sign = 'Sagittarius';
    elseif (($month == 12 && $day >= 22) || ($month == 1 && $day <= 19)) $zodiac_sign = 'Capricorn';
    elseif (($month == 1 && $day >= 20) || ($month == 2 && $day <= 18)) $zodiac_sign = 'Aquarius';
    elseif (($month == 2 && $day >= 19) || ($month == 3 && $day <= 20)) $zodiac_sign = 'Pisces';

    $_SESSION['zodiac_sign'] = $zodiac_sign;

    // ✅ CRITICAL: If user is logged in, save to database IMMEDIATELY
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $query = "UPDATE users SET name = ?, birthdate = ?, gender = ?, zodiac_sign = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssi", $name, $birthdate, $gender, $zodiac_sign, $user_id);
        $stmt->execute();
    }

    // Continue to zodiac calculation
    header("Location: ../zodiac/zodiac_result.php");
    exit();
}

// Optional: pre-fill form if user already entered details (AFTER potential redirect)
$name = $_SESSION['name'] ?? '';
$birthdate = $_SESSION['birthdate'] ?? '';
$gender = $_SESSION['gender'] ?? '';

// Now include navbar and output HTML
include(__DIR__ . "/../includes/navbar.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Get to Know You - CelestiCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow-y: scroll;
            scrollbar-width: none;
            -ms-overflow-style: none;
            background: linear-gradient(135deg, #8e57b375 0%, #634b8653 100%);
            font-family: 'Poppins', sans-serif;
        }

        .bg-accent.one { background: #bba6ff; width: 400px; height: 400px; top: -80px; left: -80px; }
        .bg-accent.two { background: #d2b0ff; width: 500px; height: 500px; bottom: -100px; right: -100px; }

        body::-webkit-scrollbar { display: none; }
        .setup-container { min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .setup-box {
            background-color: #2e2e2e;
            color: #ffffff;
            padding: 40px;
            border-radius: 20px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        .setup-box h2 { font-size: 20px; text-align: center; margin-bottom: 8px; font-weight: 600; }
        .setup-box p { text-align: center; font-size: 0.9rem; color: #cfcfcf; margin-bottom: 25px; }
        .form-control { background-color: #4b4b4b; border: none; color: #fff; border-radius: 30px; padding: 12px 20px; }
        .form-control::placeholder { color: #cfcfcf; }
        select.form-control { appearance: none; }
        .btn-continue { background-color: #6b5b95; color: #fff; border: none; border-radius: 30px; padding: 10px 20px; width: 100%; transition: 0.3s; }
        .btn-continue:hover { background-color: #8c77c5; }
        .brand-title { font-weight: 700; font-size: 24px; letter-spacing: 1px; text-align: center; margin-bottom: 4px; }
    </style>
</head>
<body>

<div class="container setup-container">
    <div class="setup-box">
        <div class="brand-title">CELESTICARE</div>
        <h2>Time to get to know you</h2>
        <p>Provide the following details below</p>

        <!-- ✅ same-page POST -->
        <form action="" method="POST">
            <div class="mb-3">
                <input type="text" name="name" class="form-control" placeholder="Name" value="<?= htmlspecialchars($name) ?>" required>
            </div>

            <div class="mb-3">
                <input type="date" name="birthdate" class="form-control" value="<?= htmlspecialchars($birthdate) ?>" required>
            </div>

            <div class="mb-4">
                <select name="gender" class="form-control" required>
                    <option value="" disabled <?= $gender=='' ? 'selected' : '' ?>>Masculine, Feminine</option>
                    <option value="Masculine" <?= $gender=='Masculine' ? 'selected' : '' ?>>Masculine</option>
                    <option value="Feminine" <?= $gender=='Feminine' ? 'selected' : '' ?>>Feminine</option>
                </select>
            </div>

            <button type="submit" class="btn btn-continue">Continue</button>
        </form>
    </div>
</div>

</body>
</html>