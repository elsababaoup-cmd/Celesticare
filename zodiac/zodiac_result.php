<?php
session_start();

// Handle form submission first
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $birthdate = $_POST["birthdate"] ?? '';
    $month = date("n", strtotime($birthdate));
    $day = date("j", strtotime($birthdate));

    // Function to calculate zodiac
    function getZodiacSign($month, $day) {
        $zodiacs = [
            ['Capricorn', 1, 19],
            ['Aquarius', 2, 18],
            ['Pisces', 3, 20],
            ['Aries', 4, 19],
            ['Taurus', 5, 20],
            ['Gemini', 6, 20],
            ['Cancer', 7, 22],
            ['Leo', 8, 22],
            ['Virgo', 9, 22],
            ['Libra', 10, 22],
            ['Scorpio', 11, 21],
            ['Sagittarius', 12, 21],
            ['Capricorn', 12, 31]
        ];

        foreach ($zodiacs as $i => $zodiac) {
            [$s, $zMonth, $zDay] = $zodiac;
            if (($month == $zMonth && $day <= $zDay) || ($month == ($zMonth - 1) && $day > $zodiacs[$i - 1][2])) {
                return $s;
            }
        }
        return "Unknown";
    }

    $sign = getZodiacSign($month, $day);

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

    $trait = $traits[$sign] ?? "The Unique";

    // Save in session and cookie
    $_SESSION['zodiac_sign'] = $sign;
    $_SESSION['zodiac_trait'] = $trait;
    setcookie("zodiac_sign", $sign, time() + (86400 * 30), "/");
    setcookie("zodiac_trait", $trait, time() + (86400 * 30), "/");
}

// If zodiac is still missing in session/cookies, redirect
$sign = $_SESSION['zodiac_sign'] ?? $_COOKIE['zodiac_sign'] ?? null;
$trait = $_SESSION['zodiac_trait'] ?? $_COOKIE['zodiac_trait'] ?? null;

if (!$sign) {
    header("Location: get_to_know.php");
    exit();
}

// Include navbar
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
            scrollbar-width: none;
            -ms-overflow-style: none;
            background: linear-gradient(135deg, #d3cce3 0%, #e9e4f0 100%);
            font-family: 'Poppins', sans-serif;
        }
        body::-webkit-scrollbar { display: none; }

        .result-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            position: relative;
            top: -70px;
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
