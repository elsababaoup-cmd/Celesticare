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
      overflow-y: scroll;
      scrollbar-width: none;
      -ms-overflow-style: none;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #5555c477 0%, #554172ff 100%);
      color: white;
      position: relative;
    }

    body::-webkit-scrollbar {
      display: none;
    }

    /* Starfield Background - Fixed positioning with proper z-index */
    .starfield {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 0; /* Behind content but above background */
      overflow: hidden;
      pointer-events: none; /* Allow clicks to pass through */
    }

    .star {
      position: absolute;
      background-color: white;
      border-radius: 50%;
      box-shadow: 0 0 6px 2px rgba(255, 255, 255, 0.8);
    }

    .shooting-star {
      position: absolute;
      width: 3px;
      height: 3px;
      background: linear-gradient(45deg, transparent, white, #a628c5, transparent);
      border-radius: 50%;
      opacity: 0;
      box-shadow: 0 0 10px 2px rgba(255, 255, 255, 0.9);
    }

    /* Content container with higher z-index */
    .content-container {
      position: relative;
      z-index: 10;
    }

    /* Hero Section */
    .hero-section {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 60px 80px;
      position: relative;
    }

    .hero-text {
      flex: 1;
      max-width: 600px;
    }

    .hero-text h1 {
      font-weight: 700;
      color: #fff;
      font-size: 2.8rem;
      margin-bottom: 20px;
      text-shadow: 0 0 10px rgba(166, 40, 197, 0.5);
    }

    .hero-text p {
      color: #e0e0ff;
      font-weight: 400;
      font-size: 1.1rem;
      margin-bottom: 30px;
      line-height: 1.6;
    }

    .hero-text .btn {
      background-color: #c19bfaff;
      color: white;
      border: none;
      border-radius: 25px;
      padding: 10px 25px;
      font-weight: 500;
      transition: background 0.3s ease, transform 0.2s ease;
      box-shadow: 0 4px 15px rgba(153, 119, 174, 1);
    }

    .hero-text .btn:hover {
      background-color: #7c5aa4ff;
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(23, 15, 26, 0.6);
    }

    .hero-image {
      flex: 1;
      display: flex;
      justify-content: center;
    }

    /* Spinning Wheel Image */
    .wheel {
      width: 600px;
      height: 600px;
      object-fit: contain;
      margin-left: 180px;
      display: block;
      transform-origin: 50% 50%;
      will-change: transform;
      cursor: pointer;
      -webkit-user-drag: none;
      user-select: none;
      transition: none;
      filter: drop-shadow(0 0 15px rgba(166, 40, 197, 0.7));
    }

    @media (max-width: 991px) {
      .hero-section {
        flex-direction: column;
        text-align: center;
        padding: 40px 20px;
      }

      .wheel {
        width: 350px;
        height: 350px;
        margin: 30px auto 0;
      }
    }
  </style>
</head>
<body>
  <!-- Starfield Background -->
  <div class="starfield" id="starfield"></div>
  
  <!-- Content Container -->
  <div class="content-container">
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
        <img src="./assets/images/zodiac_circle.png" alt="Zodiac Wheel" class="wheel" draggable="false" tabindex="0">
      </div>
    </section>
  </div>

<script>
(function(){
  // Zodiac wheel animation (unchanged)
  const wheel = document.querySelector('.wheel');
  if (!wheel) return;

  const ROTATIONS_PER_SECOND = .1;
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

// Enhanced Starfield animation
document.addEventListener('DOMContentLoaded', function() {
  const starfield = document.getElementById('starfield');
  const stars = [];
  
  // Create background stars with varying sizes and brightness
  function createStars() {
    const starCount = 200;
    
    for (let i = 0; i < starCount; i++) {
      const star = document.createElement('div');
      star.classList.add('star');
      
      // Random size between 1-5px with some larger stars
      const size = Math.random() < 0.1 ? Math.random() * 4 + 3 : Math.random() * 2 + 1;
      star.style.width = `${size}px`;
      star.style.height = `${size}px`;
      
      // Random position
      star.style.left = `${Math.random() * 100}%`;
      star.style.top = `${Math.random() * 100}%`;
      
      // Random opacity for twinkling effect - brighter stars
      const baseOpacity = Math.random() * 0.8 + 0.2;
      star.style.opacity = baseOpacity;
      
      // Add glow effect to larger stars
      if (size > 3) {
        star.style.boxShadow = '0 0 8px 3px rgba(255, 255, 255, 0.9)';
      }
      
      starfield.appendChild(star);
      stars.push({
        element: star,
        baseOpacity: baseOpacity,
        speed: Math.random() * 0.5 + 0.1,
        x: Math.random() * 100,
        y: Math.random() * 100
      });
    }
    
    // Animate stars (subtle movement)
    animateStars();
  }
  
  function animateStars() {
    stars.forEach(star => {
      // Twinkling effect - more pronounced
      const opacity = star.baseOpacity * (0.7 + 0.3 * Math.sin(Date.now() * star.speed / 1000));
      star.element.style.opacity = opacity;
    });
    
    requestAnimationFrame(animateStars);
  }
  
  // Create shooting stars
  function createShootingStar() {
    const shootingStar = document.createElement('div');
    shootingStar.classList.add('shooting-star');
    
    // Random starting position (from top)
    const startX = Math.random() * 100;
    shootingStar.style.left = `${startX}%`;
    shootingStar.style.top = `${Math.random() * 30}%`; // Start from top 30% of screen
    
    // Random angle for the shooting star
    const angle = Math.random() * 30 + 15; // Between 15-45 degrees
    
    starfield.appendChild(shootingStar);
    
    // Animate shooting star
    const duration = Math.random() * 1500 + 800; // 0.8-2.3 seconds
    const distance = Math.random() * 150 + 250; // 250-400px
    
    // Use keyframes for smoother animation
    shootingStar.style.animation = `shootStar ${duration}ms linear forwards`;
    shootingStar.style.setProperty('--translateX', `${distance * Math.cos(angle * Math.PI/180)}px`);
    shootingStar.style.setProperty('--translateY', `${distance * Math.sin(angle * Math.PI/180)}px`);
    
    // Remove after animation completes
    setTimeout(() => {
      if (shootingStar.parentNode) {
        shootingStar.parentNode.removeChild(shootingStar);
      }
    }, duration);
  }
  
  // Define keyframes for shooting star animation
  const style = document.createElement('style');
  style.textContent = `
    @keyframes shootStar {
      0% {
        transform: translate(0, 0);
        opacity: 0;
      }
      10% {
        opacity: 1;
      }
      90% {
        opacity: 1;
      }
      100% {
        transform: translate(var(--translateX), var(--translateY));
        opacity: 0;
      }
    }
  `;
  document.head.appendChild(style);
  
  // Create shooting stars at intervals
  function startShootingStars() {
    createShootingStar();
    // Random interval between 1-5 seconds
    const nextInterval = Math.random() * 4000 + 1000;
    setTimeout(startShootingStars, nextInterval);
  }
  
  // Initialize animations
  createStars();
  startShootingStars();
});
</script>

</body>
</html>