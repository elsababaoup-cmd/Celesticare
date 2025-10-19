<?php
session_start();

// Zodiac traits
$traits = [
    "Aries" => "The Bold",
    "Taurus" => "The Grounded",
    "Gemini" => "The Curious",
    "Cancer" => "The Nurturer",
    "Leo" => "The Radiant",
    "Virgo" => "The Perfectionist",
    "Libra" => "The Harmonizer",
    "Scorpio" => "The Intense",
    "Sagittarius" => "The Adventurer",
    "Capricorn" => "The Ambitious",
    "Aquarius" => "The Visionary",
    "Pisces" => "The Whimsical"
];

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["birthdate"])) {
    $birthdate = $_POST["birthdate"];
    $timestamp = strtotime($birthdate);
    if ($timestamp !== false) {
        $month = (int)date("n", $timestamp);
        $day = (int)date("j", $timestamp);

        function getZodiacSign($month, $day) {
            if     (($month == 1 && $day >= 20) || ($month == 2 && $day <= 18)) return "Aquarius";
            elseif (($month == 2 && $day >= 19) || ($month == 3 && $day <= 20)) return "Pisces";
            elseif (($month == 3 && $day >= 21) || ($month == 4 && $day <= 19)) return "Aries";
            elseif (($month == 4 && $day >= 20) || ($month == 5 && $day <= 20)) return "Taurus";
            elseif (($month == 5 && $day >= 21) || ($month == 6 && $day <= 20)) return "Gemini";
            elseif (($month == 6 && $day >= 21) || ($month == 7 && $day <= 22)) return "Cancer";
            elseif (($month == 7 && $day >= 23) || ($month == 8 && $day <= 22)) return "Leo";
            elseif (($month == 8 && $day >= 23) || ($month == 9 && $day <= 22)) return "Virgo";
            elseif (($month == 9 && $day >= 23) || ($month == 10 && $day <= 22)) return "Libra";
            elseif (($month == 10 && $day >= 23) || ($month == 11 && $day <= 21)) return "Scorpio";
            elseif (($month == 11 && $day >= 22) || ($month == 12 && $day <= 21)) return "Sagittarius";
            else return "Capricorn";
        }

        $sign = getZodiacSign($month, $day);
        $trait = $traits[$sign] ?? "The Unique";

        $_SESSION['zodiac_sign'] = $sign;
        $_SESSION['zodiac_trait'] = $trait;
        setcookie("zodiac_sign", $sign, time() + (86400 * 30), "/");
        setcookie("zodiac_trait", $trait, time() + (86400 * 30), "/");

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

$sign = $_SESSION['zodiac_sign'] ?? $_COOKIE['zodiac_sign'] ?? null;
$trait = $traits[$sign] ?? ($_SESSION['zodiac_trait'] ?? $_COOKIE['zodiac_trait'] ?? "The Unique");

if (!$sign) {
    header("Location: get_to_know.php");
    exit;
}

include("../includes/navbar.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Zodiac Result - CelestiCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow-y: scroll;
            background: linear-gradient(135deg, #ab8df1ff 0%, #e9e4f0 100%);
            font-family: 'Poppins', sans-serif;
        }

        .result-container {
            min-height: calc(100vh - 80px);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start; /* content starts under navbar */
            padding-top: 120px; /* spacing for navbar */
            padding-bottom: 80px; /* space for scroll bottom */
            position: relative;
            z-index: 1;
        }

        .result-box {
            background-color: #2e2e2e;
            color: #ffffff;
            padding: 50px 40px;
            border-radius: 25px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
            text-align: center;
            animation: fadeIn 0.8s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .brand-title {
            font-weight: 700;
            font-size: 26px;
            letter-spacing: 1px;
            margin-bottom: 20px;
            color: #fff;
        }

        .result-box h2 {
            font-size: 1.3rem;
            font-weight: 500;
            color: #cfcfcf;
            margin-bottom: 10px;
        }

        .result-box h1 {
            font-size: 3rem;
            font-weight: 800;
            color: #b69df1;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }

        .result-box p {
            font-size: 1.2rem;
            color: #dcdcdc;
            margin-bottom: 25px;
        }

        .btn-continue {
            background-color: #6b5b95;
            color: #fff;
            border: none;
            border-radius: 30px;
            padding: 12px 30px;
            font-weight: 500;
            transition: 0.3s;
        }

        .btn-continue:hover {
            background-color: #8c77c5;
            transform: scale(1.05);
        }

        html::-webkit-scrollbar,
        body::-webkit-scrollbar {
            width: 0 !important;
            height: 0 !important;
            background: transparent;
}
    </style>
</head>
<body>
    <div class="result-container">
        <div class="result-box">
            <div class="brand-title">CELESTICARE</div>
            <h2>Your Sun Sign Is</h2>
            <h1><?= htmlspecialchars($sign) ?></h1>
            <p><?= htmlspecialchars($trait) ?></p>
            <a href="../undertone/undertone_test.php" class="btn btn-continue mt-3">Continue</a>
        </div>
    </div>
</body>
</html>
