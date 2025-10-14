<?php
session_start();
include(__DIR__ . "/includes/navbar.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>

  <meta charset="UTF-8">
  <title>CelestiCare</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
    }

    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      overflow-y: scroll; /* ✅ enables scrolling */
      scrollbar-width: none; /* ✅ hide scrollbar in Firefox */
      -ms-overflow-style: none; /* ✅ hide scrollbar in IE/Edge */
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #d3cce3 0%, #e9e4f0 100%);
    }

    body::-webkit-scrollbar {
      display: none; /* ✅ hide scrollbar in Chrome/Safari */
    }

    /* Hero Section */
    .hero-section {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh; /* ✅ allow scrolling if content grows */
      padding: 60px 80px;
      background: linear-gradient(135deg, #d3cce3 0%, #e9e4f0 100%);
    }

    .hero-text {
      flex: 1;
      max-width: 600px;
    }

    .hero-text h1 {
      font-weight: 700;
      color: #000;
      font-size: 2.8rem;
      margin-bottom: 20px;
    }

    .hero-text p {
      color: #333;
      font-weight: 400;
      font-size: 1.1rem;
      margin-bottom: 30px;
      line-height: 1.6;
    }

    .hero-text .btn {
      background-color: #333;
      color: white;
      border: none;
      border-radius: 25px;
      padding: 10px 25px;
      font-weight: 500;
      transition: background 0.3s ease;
    }

    .hero-text .btn:hover {
      background-color: #555;
    }

    .hero-image {
      flex: 1;
      display: flex;
      justify-content: center;
    }

    /* Spinning Wheel Image */
    .wheel {
      width: 500px;
      height: 500px;
      object-fit: contain;
      margin-left: 180px;
      display: block;
      transform-origin: 50% 50%;
      will-change: transform;
      cursor: pointer;
      -webkit-user-drag: none;
      user-select: none;
      transition: none;
    }

    @media (max-width: 991px) {
      .hero-section {
        flex-direction: column;
        text-align: center;
        padding: 40px 20px;
      }

      .wheel {
        width: 250px;
        height: 250px;
        margin: 30px auto 0;
      }
    }
  </style>
</head>
<body>
  <section class="hero-section">
    <div class="hero-text">
      <h1>Let the stars guide your taste</h1>
      <p>
        The stylist of the stars, CelestiCare, gives you the latest fashion tips by using your zodiac sign
        and matching it to your preferred style preferences and aesthetics.
      </p>
      <a href="./setup/get_to_know.php" class="btn">Get Started</a>
    </div>

    <div class="hero-image">
      <img src="./assets/images/Photoroom.png" alt="Zodiac Wheel" class="wheel" draggable="false" tabindex="0">
    </div>
  </section>

<script>
(function(){
  const wheel = document.querySelector('.wheel');
  if (!wheel) return;

  const ROTATIONS_PER_SECOND = .2;
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

  // Start automatically once the page loads
  window.addEventListener('load', start);

  // Optional: keep drag prevention
  wheel.addEventListener('dragstart', e => e.preventDefault());
})();
</script>

</body>
</html>
