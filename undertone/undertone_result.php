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
    background: linear-gradient(135deg, #d3cce3 0%, #e9e4f0 100%);
    padding-top: 80px; /* navbar height */
    overflow-y: auto; /* enable scrolling */
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE 10+ */
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
.info-box {
    background: #f4f4f4;
    padding: 20px;
    border-radius: 12px;
    margin-top: 20px;
    font-size: 1rem;
    color: #333;
}
.modal-content {
    background-color: #121212;
    color: #fff;
    border-radius: 20px;
    border: none;
}
.modal-header { 
    border-bottom: none; 
}
.form-control { 
  background-color: #1f1f1f; color: #fff; border: none;
}
.form-control:focus { 
  background-color: #2b2b2b; color: #fff; box-shadow: none;
}
.btn-dark { 
  border-radius: 10px; 
}
#message { 
  margin-top: 10px; 
}

@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

.login-box, .register-box {
  background-color: #2e2e2e;
  color: #ffffff;
  padding: 40px;
  border-radius: 20px;
  width: 100%;
  max-width: 420px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.2);
  font-family: 'Poppins', sans-serif;
}

.login-box h2, .register-box h2 {
  font-size: 20px;
  text-align: center;
  margin-bottom: 25px;
  font-weight: 600;
}

.form-control {
  background-color: #4b4b4b;
  border: none;
  color: #fff;
  border-radius: 30px;
  padding: 12px 20px;
}

.form-control::placeholder {
  color: #cfcfcf;
}

.btn-login, .btn-register {
  background-color: #6b5b95;
  color: #fff;
  border: none;
  border-radius: 30px;
  padding: 10px 20px;
  width: 100%;
  transition: 0.3s;
}

.btn-login:hover, .btn-register:hover {
  background-color: #8c77c5;
}

.brand-title {
  font-weight: 700;
  font-size: 24px;
  letter-spacing: 1px;
  text-align: center;
  margin-bottom: 5px;
}

.text-muted {
  color: #cfcfcf !important;
}

.text-muted a {
  color: #fff !important;
  text-decoration: underline;
}

.text-muted a:hover {
  color: #d3bfff !important;
}


/* --- Fix modal centering --- */
.modal-dialog {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh; /* forces perfect vertical center */
  margin: 0 auto;
}

/* Remove extra Bootstrap translate offset */
.modal.fade .modal-dialog {
  transform: translate(0, 0) !important;
  transition: transform 0.3s ease-out;
}


/* Adjust for any vertical offset (optional tweak) */
.modal-content {
  margin: 0 auto;
  top: 0;
  bottom: 0;
}

/* Prevent scrollbars from appearing during modal display */
body.modal-open {
  overflow: hidden !important;
  padding-right: 0 !important;
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

<div class="container">
    <h1>CELESTICARE</h1>

    <div class="result-section">
        <div class="image-box">
            <img src="<?= htmlspecialchars($wheelImage) ?>" alt="<?= htmlspecialchars($undertone) ?> Wheel" class="wheel" draggable="false" tabindex="0">
            <div class="info-box">
                <?= htmlspecialchars($zodiac) ?>’s undertone gives insight into the best colors for your style and wardrobe.
            </div>
        </div>

        <div class="text-box">
            <h2><?= htmlspecialchars($zodiac) ?> with a <?= htmlspecialchars($undertone) ?> undertone</h2>

            <div class="palette">
                <?php foreach($palette as $color): ?>
                    <div class="color-box" style="background-color: <?= htmlspecialchars($color) ?>;"></div>
                <?php endforeach; ?>
            </div>

            <p class="description"><?= htmlspecialchars($description) ?></p>

            <div class="info-box">
                These colors complement your undertone perfectly and can enhance your natural glow.
            </div>
            <div class="info-box">
                Experiment with these shades in clothing, makeup, and accessories to find your signature look.
            </div>
        </div>
    </div>

    <!-- Continue Button -->
    <<button type="button" class="btn btn-login" data-bs-toggle="modal" data-bs-target="#loginModal">
  Continue
</button>

<!-- LOGIN MODAL -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content login-box">
      <div class="brand-title">CELESTICARE</div>
      <h2>Log in to your CelestiCare profile</h2>

      <form method="POST" action="../auth/login_process.php">
        <div class="mb-3">
          <input type="email" name="email" class="form-control" placeholder="Email" required>
        </div>
        <div class="mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <button type="submit" class="btn btn-login">Login</button>

        <p class="text-center text-muted mt-3">
          Don’t have a CelestiCare profile?
          <a href="#" id="showRegister">Sign up</a>
        </p>
      </form>
    </div>
  </div>
</div>

<!-- REGISTER MODAL -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content register-box">
      <div class="brand-title">CELESTICARE</div>
      <h2>Sign up to create your CelestiCare Profile!</h2>

      <form method="POST" action="../auth/register_process.php">
        <div class="mb-3">
          <input type="email" name="email" class="form-control" placeholder="Email" required>
        </div>
        <div class="mb-3">
          <input type="text" name="username" class="form-control" placeholder="Username" required>
        </div>
        <div class="mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <div class="mb-3">
          <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
        </div>
        <button type="submit" class="btn btn-register">Continue</button>

        <p class="text-center text-muted mt-3">
          Already have an account? <a href="#" id="showLogin">Login</a>
        </p>
      </form>
    </div>
  </div>
</div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Toggle Login/Register sections
$("#showRegister").click(function(e){
  e.preventDefault();
  $("#loginSection").hide();
  $("#registerSection").show();
});
$("#showLogin").click(function(e){
  e.preventDefault();
  $("#registerSection").hide();
  $("#loginSection").show();
});

// Login process
$("#loginForm").submit(function(e){
  e.preventDefault();
  $.post("../auth/login_process.php", $(this).serialize(), function(data){
    if(data.status === "success"){
      $("#message").html('<span class="text-success">'+data.message+'</span>');
      setTimeout(()=>window.location.href="../dashboard/index.php", 1000);
    } else {
      $("#message").html('<span class="text-danger">'+data.message+'</span>');
    }
  }, "json");
});

// Register process
$("#registerForm").submit(function(e){
  e.preventDefault();
  $.post("../auth/register_process.php", $(this).serialize(), function(data){
    if(data.status === "success"){
      $("#registerMessage").html('<span class="text-success">'+data.message+'</span>');
      setTimeout(()=>window.location.href="../dashboard/index.php", 1000);
    } else {
      $("#registerMessage").html('<span class="text-danger">'+data.message+'</span>');
    }
  }, "json");
});

document.addEventListener('DOMContentLoaded', function() {
  const showRegister = document.getElementById('showRegister');
  const showLogin = document.getElementById('showLogin');
  const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
  const registerModal = new bootstrap.Modal(document.getElementById('registerModal'));

  if (showRegister) {
    showRegister.addEventListener('click', function(e) {
      e.preventDefault();
      loginModal.hide();
      setTimeout(() => registerModal.show(), 400);
    });
  }

  if (showLogin) {
    showLogin.addEventListener('click', function(e) {
      e.preventDefault();
      registerModal.hide();
      setTimeout(() => loginModal.show(), 400);
    });
  }
});

// WHEEL FUNCTIONALITY
(function(){
  const wheel = document.querySelector('.wheel');
  if (!wheel) return;

  const ROTATIONS_PER_SECOND = 0.2;
  const degreesPerMs = (ROTATIONS_PER_SECOND * 360) / 1000;

  let rafId = null;
  let lastTime = 0;
  let angle = 0;
  let running = false;

  // Spin animation frame
  function step(now){
    if (!running) { rafId = null; return; }
    if (!lastTime) lastTime = now;
    const delta = now - lastTime;
    lastTime = now;
    angle = (angle + delta * degreesPerMs) % 360;
    wheel.style.transform = `rotate(${angle}deg)`;
    rafId = requestAnimationFrame(step);
  }

  // Start rotation
  function start(){
    if (running) return;
    running = true;
    lastTime = 0;
    rafId = requestAnimationFrame(step);
  }

  // Stop rotation
  function stop(){
    if (!running) return;
    running = false;
    if (rafId) cancelAnimationFrame(rafId);
    rafId = null;
    lastTime = 0;
    wheel.style.transform = `rotate(${angle}deg)`;
  }

  // Hover spin
  wheel.addEventListener('mouseenter', start);
  wheel.addEventListener('mouseleave', stop);

  // Prevent dragging
  wheel.addEventListener('dragstart', e => e.preventDefault());

  // Optional click-to-spin button
  const spinButton = document.querySelector('#spinButton');
  if (spinButton) {
    spinButton.addEventListener('click', function(){
      if (!running) {
        start();
        spinButton.textContent = "Stop Wheel";
      } else {
        stop();
        spinButton.textContent = "Spin Wheel";
      }
    });
  }
})();
</script>

</body>
</html>