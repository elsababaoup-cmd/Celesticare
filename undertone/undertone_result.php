<?php
session_start();
include("../includes/navbar.php");

// Retrieve zodiac and undertone
$zodiac = $_SESSION['zodiac_sign'] ?? $_COOKIE['zodiac_sign'] ?? null;
$undertone = $_SESSION['undertone'] ?? $_COOKIE['undertone'] ?? null;

if (!$zodiac || !$undertone) {
    header("Location: undertone_test.php");
    exit();
}

$palettes = [
    "Aries" => [
        "cool" => ["#131111","#6b0e3d","#80012b","#ad0f53","#8b3979","#f4338a"],
        "warm" => ["#40180b","#88171c","#d72113","#f0500c","#ff762d","#ff9a51"],
        "neutral" => ["#7f1619","#9b2627","#bd5858","#926454","#b8717e","#efaeb4"]
    ],
    "Taurus" => [
        "cool" => ["#22a068","#ff5ee1","#ff9afb","#9bee99","#ffc8fb","#a7efcb"],
        "warm" => ["#2e3f16","#3a5d09","#7bab44","#b25526","#ff762d","#ffc451"],
        "neutral" => ["#253e18","#9d6f45","#689257","#c68139","#eccb67","#a4c49c"]
    ],

    "Gemini" => [
        "cool" => ["#052542","#175374","#9e37a0","#b478cd","#e669ef","#82d3d7"],
        "warm" => ["#567b18","#55844f","#ffa408","#fab738","#afd84b","#ffcf40"],
        "neutral" => ["#19344d","#435c89","#807145","#5e666b","#cea63f","#96accd"]
    ],

    "Cancer" => [
        "cool" => ["#0c205b","#212d9b","#009cff","#81b0ff","#a3e6ff","#85e9e9"],
        "warm" => ["#0c3b3b","#097b77","#17cba0","#5df6bf","#a5ffe7","#b3ffc8"],
        "neutral" => ["#013b60","#2b2f59","#435c89","#5e5f73","#a5b2c2","#cddfeb"]
    ],

    "Leo" => [
        "cool" => ["#5b204e","#862d58","#7c465d","#cf1e5a","#ff0068","#be2736"],
        "warm" => ["#774526","#c43b3b","#ac4b2a","#fa4d00","#ff6a04","#f28115"],
        "neutral" => ["#52351a","#724923","#a96236","#c78d2f","#d09940","#edc98b"]
    ],

    "Virgo" => [
        "cool" => ["#043921","#034d37","#00835e","#4bcbaa","#4df2cc","#a0dbbf"],
        "warm" => ["#0f3917","#6d3d12","#376b32","#9d6216","#5a6b19","#e17e16"],
        "neutral" => ["#3b4626","#596b3c","#857039","#a5a95e","#d1ac6d","#ddb793"]
    ],

    "Libra" => [
        "cool" => ["#622674","#6d4b66","#9b63c2","#9683be","#b4b0e1","#ecc0d9"],
        "warm" => ["#569c7e","#72af8e","#ffaaa5","#ffcbc3","#dcedc1","#ffe3c6"],
        "neutral" => ["#ecc0d9","#7fa292","#a6d2bb","#f6bdb4","#fdd8c6","#fef0d5"]
    ],

    "Scorpio" => [
        "cool" => ["#191933","#461d49","#15456b","#44229a","#7648b0","#3a89db"],
        "warm" => ["#730101","#b70000","#e2293b","#b2301f","#c76a50","#c18584"],
        "neutral" => ["#110f0d","#4c3860","#662a48","#504840","#867a6e","#d6d5da"]
    ],

    "Saggitarius" => [
        "cool" => ["#40007b","#3830a0","#24879d","#7f1d80","#ce2c82","#ce6aac"],
        "warm" => ["#52201c","#415715","#fa7e1e","#Fe7735aF9F1C","#aac265","#edba4a"],
        "neutral" => ["#2c2956","#4d234b","#786988","#6d76a7","#5386a2","#7b98b7"]
    ],

    "Capricorn" => [
        "cool" => ["#001b33","#2e3958","#267b64","#4d90cf","#77d6c2","#78b895"],
        "warm" => ["#3e1f1c","#2e472a","#662f0b","#ac6114","#639261","#fedca3"],
        "neutral" => ["#303c54","#667f61","#979593","#8d7d6d","#6b9da6","#c4af90"]
    ],

    "Aquarius" => [
        "cool" => ["#293aaa","#5f35b1","#0768c9","#ae4a8c","#ad55ea","#a09dea"],
        "warm" => ["#8a2a2b","#108bff","#ee8f21","#f5c976","#fffa81","#eccbd1"],
        "neutral" => ["#44499a","#4d90cf","#78b895","#75c3d0","#76899f","#bfafd3"]
    ],

    "Pisces" => [
        "cool" => ["#007392","#727cd6","#b2adfd","#64ffd5","#b1d6ff","#b4fff6"],
        "warm" => ["#ff8080","#ffab88","#fcb9b0","#ffd2c9","#fcf0da","#ffebeb"],
        "neutral" => ["#39536d","#1ba8a0","#9e99d1","#8ed1d1","#a9d8de","#b5e8d5"]
    ]
];

$descriptions = [
    "cool" => "Cool undertones tend to have hints of blue, pink or red. They look best with silver jewelry and icy colors.",
    "warm" => "Warm undertones have hints of yellow, golden or peach. They glow with gold jewelry and warm earthy colors.",
    "neutral" => "Neutral undertones are balanced and can wear both warm and cool colors harmoniously."
];

$palette = $palettes[$zodiac][$undertone] ?? ["#ccc","#999","#666","#333","#000","#fff"];
$description = $descriptions[$undertone] ?? "Discover your unique color palette!";

// Map undertones to wheel images
$wheelImages = [
    "cool" => "assets/cool_wheel.png",
    "warm" => "assets/warm_wheel.png",
    "neutral" => "assets/neutral_wheel.png",
];

$wheelImage = $wheelImages[$undertone] ?? "assets/neutral_wheel.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Color Analysis Result - CelestiCare</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    background: #f8f8f8;
    padding-top: 80px; /* navbar height */
}
.navbar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 9999;
}
.container {
    max-width: 1000px;
    margin: 0 auto;
    background: #fff;
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

/* Layout for image + text side by side */
.result-section {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 30px;
    text-align: left;
}

/* Left: wheel area */
.image-box {
    flex: 1 1 40%;
    text-align: center;
}
.wheel {
    width: 300px;
    height: 300px;
    transform-origin: 50% 50%;
    cursor: pointer;
    -webkit-user-drag: none;
    user-select: none;
    transition: none;
    border-radius: 10px;
}
@media (max-width:480px){
    .wheel { width: 220px; height: 220px; }
}

/* Right: text area */
.text-box {
    flex: 1 1 55%;
}

/* Headings and palette */
h1 {
    font-weight: 700;
    font-size: 2.2rem;
    text-align: center;
}
h2 {
    font-weight: 600;
    font-size: 1.5rem;
    color: #555;
    margin-bottom: 25px;
}
.palette {
    display: flex;
    justify-content: flex-start;
    gap: 10px;
    flex-wrap: wrap;
    margin: 25px 0;
}
.color-box {
    flex: 1;
    min-width: 70px;
    height: 70px;
    border-radius: 8px;
    border: 1px solid #ddd;
}
.description {
    font-size: 1rem;
    color: #333;
    margin-top: 10px;
    line-height: 1.5;
}
</style>
</head>
<body>

<div class="container">
    <h1>CELESTICARE</h1>

    <div class="result-section">
        <div class="image-box">
            <!-- Spinning wheel image -->
            <img src="<?= htmlspecialchars($wheelImage) ?>" alt="<?= htmlspecialchars($undertone) ?> Wheel" class="wheel" draggable="false" tabindex="0">
        </div>

        <div class="text-box">
            <h2><?= htmlspecialchars($zodiac) ?> with a <?= htmlspecialchars($undertone) ?> undertone</h2>

            <div class="palette">
                <?php foreach($palette as $color): ?>
                    <div class="color-box" style="background-color: <?= htmlspecialchars($color) ?>;"></div>
                <?php endforeach; ?>
            </div>

            <p class="description"><?= htmlspecialchars($description) ?></p>
        </div>
    </div>
</div>

<script>
(function(){
  const wheel = document.querySelector('.wheel');
  if (!wheel) return;

  const ROTATIONS_PER_SECOND = 0.2;
  const degreesPerMs = (ROTATIONS_PER_SECOND * 360) / 1000;

  let rafId = null;
  let lastTime = 0;
  let angle = 0;
  let running = false;

  function step(now){
    if (!running) { rafId = null; return; }
    if (!lastTime) lastTime = now;
    const delta = now - lastTime;
    lastTime = now;
    angle = (angle + delta * degreesPerMs) % 360;
    wheel.style.transform = `rotate(${angle}deg)`;
    rafId = requestAnimationFrame(step);
  }

  function start(){
    if (running) return;
    running = true;
    lastTime = 0;
    rafId = requestAnimationFrame(step);
  }

  function stop(){
    if (!running) return;
    running = false;
    if (rafId) cancelAnimationFrame(rafId);
    rafId = null;
    lastTime = 0;
    wheel.style.transform = `rotate(${angle}deg)`;
  }

  wheel.addEventListener('mouseenter', start);
  wheel.addEventListener('mouseleave', stop);
  wheel.addEventListener('focus', start);
  wheel.addEventListener('blur', stop);
  wheel.addEventListener('dragstart', e => e.preventDefault());
})();
</script>

</body>
</html>
