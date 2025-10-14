<?php
session_start();
include("../config/dbconfig.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit();
}

$user_id = $_SESSION['user_id'];

$name = trim($_POST['name'] ?? '');
$birthdate = trim($_POST['birthdate'] ?? '');
$gender = trim($_POST['gender'] ?? '');
$zodiac_sign = trim($_POST['zodiac_sign'] ?? '');

// compute zodiac if needed
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
        ["Sagittarius", 1122, 1221]
    ];
    $md = intval(sprintf("%02d%02d", $month, $day));
    foreach ($zodiacs as [$sign, $start, $end]) {
        if (($md >= $start && $md <= $end) || ($start > $end && ($md >= $start || $md <= $end))) {
            return $sign;
        }
    }
    return "Capricorn";
}

if (!empty($birthdate)) {
    $date = date_create($birthdate);
    $zodiac_sign = $zodiac_sign ?: getZodiacSign(date_format($date, "m"), date_format($date, "d"));
}

$update = $conn->prepare("UPDATE users SET name=?, birthdate=?, gender=?, zodiac_sign=? WHERE id=?");
$update->bind_param("ssssi", $name, $birthdate, $gender, $zodiac_sign, $user_id);

if ($update->execute()) {
    $astroFacts = [
        "Aries" => "You are bold, ambitious, and a natural leader.",
        "Taurus" => "You value comfort and beauty in all things.",
        "Gemini" => "Your curiosity and wit make you adaptable.",
        "Cancer" => "You are intuitive and deeply caring.",
        "Leo" => "Your creativity shines brightly.",
        "Virgo" => "You find harmony in order and detail.",
        "Libra" => "Balance and beauty guide your choices.",
        "Scorpio" => "You’re magnetic and passionate.",
        "Sagittarius" => "Your adventurous heart inspires others.",
        "Capricorn" => "You are grounded and ambitious.",
        "Aquarius" => "You think differently and value authenticity.",
        "Pisces" => "Your creativity and empathy define you."
    ];

    echo json_encode([
        "success" => true,
        "data" => [
            "name" => $name,
            "birthdate" => $birthdate,
            "gender" => $gender,
            "zodiac_sign" => $zodiac_sign,
            "astroFact" => $astroFacts[$zodiac_sign] ?? "Complete your zodiac profile to unlock your astro fact!"
        ]
    ]);
} else {
    echo json_encode(["success" => false, "message" => "DB error: ".$update->error]);
}
?>
