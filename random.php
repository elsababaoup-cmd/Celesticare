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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
      background: linear-gradient(135deg, #3232709e 0%, #554172ff 100%);
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
      z-index: 0;
      overflow: hidden;
      pointer-events: none;
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

    /* Music Control Button */
    .music-control {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 1000;
      background: rgba(255,255,255,0.2);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255,255,255,0.3);
      border-radius: 50%;
      width: 50px;
      height: 50px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s ease;
      color: white;
      font-size: 1.2rem;
    }

    .music-control:hover {
      background: rgba(255,255,255,0.3);
      transform: scale(1.1);
    }

    .music-control.muted {
      background: rgba(255,255,255,0.1);
      color: #ccc;
    }

    /* New Sections Styling - Matching Your Theme */
    .features-section {
      padding: 100px 0;
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(10px);
      position: relative;
    }

    .zodiac-section {
      padding: 100px 0;
      background: rgba(0, 0, 0, 0.1);
      position: relative;
    }

    .cta-section {
      padding: 80px 0;
      background: linear-gradient(135deg, rgba(166, 40, 197, 0.8), rgba(124, 90, 164, 0.8));
      position: relative;
    }

    .section-title {
      text-align: center;
      font-weight: 700;
      font-size: 2.5rem;
      margin-bottom: 3rem;
      text-shadow: 0 0 10px rgba(166, 40, 197, 0.5);
    }

    .section-subtitle {
      text-align: center;
      color: #e0e0ff;
      font-size: 1.2rem;
      margin-bottom: 4rem;
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
    }

    /* Feature Cards */
    .feature-card {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      padding: 40px 30px;
      text-align: center;
      height: 100%;
      border: 1px solid rgba(255, 255, 255, 0.2);
      transition: all 0.3s ease;
      margin-bottom: 30px;
    }

    .feature-card:hover {
      transform: translateY(-10px);
      background: rgba(255, 255, 255, 0.15);
      box-shadow: 0 15px 30px rgba(166, 40, 197, 0.3);
    }

    .feature-icon {
      font-size: 3rem;
      color: #c19bfa;
      margin-bottom: 1.5rem;
      text-shadow: 0 0 15px rgba(193, 155, 250, 0.5);
    }

    .feature-card h4 {
      font-weight: 600;
      margin-bottom: 1rem;
      color: white;
    }

    .feature-card p {
      color: #e0e0ff;
      line-height: 1.6;
    }

    /* Zodiac Cards */
    .zodiac-card {
      text-align: center;
      padding: 30px 20px;
      border-radius: 15px;
      transition: all 0.3s ease;
      cursor: pointer;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      margin-bottom: 30px;
    }

    .zodiac-card:hover {
      background: rgba(255, 255, 255, 0.1);
      transform: scale(1.05);
      box-shadow: 0 10px 25px rgba(166, 40, 197, 0.3);
    }

    .zodiac-icon {
      width: 80px;
      height: 80px;
      margin: 0 auto 20px;
      background: linear-gradient(135deg, #c19bfa, #7c5aa4);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      color: white;
      box-shadow: 0 0 20px rgba(193, 155, 250, 0.5);
    }

    .zodiac-card h5 {
      font-weight: 600;
      margin-bottom: 10px;
      color: white;
    }

    .zodiac-card .zodiac-desc {
      color: #e0e0ff;
      font-size: 0.9rem;
      line-height: 1.4;
    }

    /* CTA Section */
    .cta-content {
      text-align: center;
      max-width: 600px;
      margin: 0 auto;
    }

    .cta-content h2 {
      font-weight: 700;
      font-size: 2.5rem;
      margin-bottom: 1.5rem;
      text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
    }

    .cta-content p {
      font-size: 1.2rem;
      margin-bottom: 2.5rem;
      color: rgba(255, 255, 255, 0.9);
    }

    .cta-btn {
      background-color: white;
      color: #7c5aa4;
      border: none;
      border-radius: 25px;
      padding: 12px 40px;
      font-weight: 600;
      font-size: 1.1rem;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .cta-btn:hover {
      background-color: #f0f0f0;
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    }

    /* Footer Styling */
    footer {
      background: rgba(0, 0, 0, 0.3);
      backdrop-filter: blur(10px);
      padding: 60px 0 30px;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .footer-brand {
      font-weight: 700;
      font-size: 1.8rem;
      color: white;
      margin-bottom: 1rem;
      text-shadow: 0 0 10px rgba(166, 40, 197, 0.5);
    }

    .footer-description {
      color: #e0e0ff;
      line-height: 1.6;
      margin-bottom: 2rem;
    }

    .footer-links h5 {
      color: white;
      font-weight: 600;
      margin-bottom: 1.5rem;
      font-size: 1.1rem;
    }

    .footer-links a {
      color: #e0e0ff;
      text-decoration: none;
      transition: color 0.3s ease;
      display: block;
      margin-bottom: 0.8rem;
      font-size: 0.95rem;
    }

    .footer-links a:hover {
      color: #c19bfa;
      transform: translateX(5px);
    }

    .social-links {
      display: flex;
      gap: 15px;
      margin-top: 1rem;
    }

    .social-links a {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 40px;
      height: 40px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 50%;
      color: #e0e0ff;
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .social-links a:hover {
      background: #c19bfa;
      color: white;
      transform: translateY(-3px);
    }

    .subscribe-input {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 25px;
      padding: 12px 20px;
      color: white;
      width: 100%;
    }

    .subscribe-input::placeholder {
      color: #e0e0ff;
    }

    .subscribe-input:focus {
      background: rgba(255, 255, 255, 0.15);
      border-color: #c19bfa;
      box-shadow: 0 0 10px rgba(193, 155, 250, 0.3);
      outline: none;
    }

    .subscribe-btn {
      background: #c19bfa;
      border: none;
      border-radius: 25px;
      padding: 12px 25px;
      color: white;
      font-weight: 500;
      transition: all 0.3s ease;
      white-space: nowrap;
    }

    .subscribe-btn:hover {
      background: #7c5aa4;
      transform: translateY(-2px);
    }

    .footer-bottom {
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      padding-top: 30px;
      margin-top: 40px;
    }

    .footer-bottom a {
      color: #e0e0ff;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .footer-bottom a:hover {
      color: #c19bfa;
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

      .music-control {
        top: 15px;
        right: 15px;
        width: 45px;
        height: 45px;
        font-size: 1.1rem;
      }

      .section-title {
        font-size: 2rem;
      }

      .feature-card, .zodiac-card {
        margin-bottom: 20px;
      }

      .footer-links {
        margin-bottom: 2rem;
      }
    }
  </style>
</head>
<body>
  <!-- Music Player -->
  <audio id="backgroundMusic" autoplay loop>
    <source src="./music/Astral.mp3" type="audio/mpeg">
    <source src="./music/homepage.ogg" type="audio/ogg">
    Your browser does not support the audio element.
  </audio>

  <!-- Music Control Button -->
  <div id="musicControl" class="music-control" title="Click to mute/unmute">
    🔈
  </div>

  <!-- Starfield Background -->
  <div class="starfield" id="starfield"></div>
  
  <!-- Content Container -->
  <div class="content-container">
    <!-- Hero Section -->
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

    <!-- How CelestiCare Works Section -->
    <section class="features-section">
      <div class="container">
        <h2 class="section-title">How CelestiCare Works</h2>
        <p class="section-subtitle">We combine astrology with fashion to help you express your true self through clothing.</p>
        
        <div class="row g-4">
          <div class="col-md-4">
            <div class="feature-card">
              <div class="feature-icon">
                <i class="fas fa-chart-line"></i>
              </div>
              <h4>Personalized Forecast</h4>
              <p>Get daily, weekly, and monthly fashion recommendations based on your zodiac sign and current planetary alignments.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="feature-card">
              <div class="feature-icon">
                <i class="fas fa-palette"></i>
              </div>
              <h4>Color Guidance</h4>
              <p>Discover which colors will bring you positive energy and complement your natural aura each day.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="feature-card">
              <div class="feature-icon">
                <i class="fas fa-tshirt"></i>
              </div>
              <h4>Style Recommendations</h4>
              <p>Receive tailored outfit suggestions that align with your zodiac personality and current fashion trends.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Explore Zodiacs Section -->
    <section class="zodiac-section">
      <div class="container">
        <h2 class="section-title">Explore Zodiac Fashion</h2>
        <p class="section-subtitle">Each sign has unique style characteristics. Discover yours!</p>
        
        <div class="row g-4">
          <!-- Aries -->
          <div class="col-md-3 col-6">
            <div class="zodiac-card">
              <div class="zodiac-icon">
                <i class="fas fa-fw fa-ram"></i>
              </div>
              <h5>Aries</h5>
              <p class="zodiac-desc">Bold & Dynamic - Confident, energetic styles with fiery accents</p>
            </div>
          </div>
          <!-- Taurus -->
          <div class="col-md-3 col-6">
            <div class="zodiac-card">
              <div class="zodiac-icon">
                <i class="fas fa-fw fa-gem"></i>
              </div>
              <h5>Taurus</h5>
              <p class="zodiac-desc">Luxurious & Earthy - Quality fabrics and nature-inspired tones</p>
            </div>
          </div>
          <!-- Gemini -->
          <div class="col-md-3 col-6">
            <div class="zodiac-card">
              <div class="zodiac-icon">
                <i class="fas fa-fw fa-users"></i>
              </div>
              <h5>Gemini</h5>
              <p class="zodiac-desc">Versatile & Expressive - Mix-and-match pieces for every occasion</p>
            </div>
          </div>
          <!-- Cancer -->
          <div class="col-md-3 col-6">
            <div class="zodiac-card">
              <div class="zodiac-icon">
                <i class="fas fa-fw fa-moon"></i>
              </div>
              <h5>Cancer</h5>
              <p class="zodiac-desc">Comforting & Nostalgic - Soft textures and sentimental pieces</p>
            </div>
          </div>
          <!-- Leo -->
          <div class="col-md-3 col-6">
            <div class="zodiac-card">
              <div class="zodiac-icon">
                <i class="fas fa-fw fa-sun"></i>
              </div>
              <h5>Leo</h5>
              <p class="zodiac-desc">Dramatic & Regal - Bold statements and attention-grabbing pieces</p>
            </div>
          </div>
          <!-- Virgo -->
          <div class="col-md-3 col-6">
            <div class="zodiac-card">
              <div class="zodiac-icon">
                <i class="fas fa-fw fa-pencil-alt"></i>
              </div>
              <h5>Virgo</h5>
              <p class="zodiac-desc">Refined & Practical - Tailored fits and functional elegance</p>
            </div>
          </div>
          <!-- Libra -->
          <div class="col-md-3 col-6">
            <div class="zodiac-card">
              <div class="zodiac-icon">
                <i class="fas fa-fw fa-balance-scale"></i>
              </div>
              <h5>Libra</h5>
              <p class="zodiac-desc">Harmonious & Chic - Balanced ensembles and romantic touches</p>
            </div>
          </div>
          <!-- Scorpio -->
          <div class="col-md-3 col-6">
            <div class="zodiac-card">
              <div class="zodiac-icon">
                <i class="fas fa-fw fa-scorpion"></i>
              </div>
              <h5>Scorpio</h5>
              <p class="zodiac-desc">Intense & Mysterious - Dark hues and transformative pieces</p>
            </div>
          </div>
          <!-- Sagittarius -->
          <div class="col-md-3 col-6">
            <div class="zodiac-card">
              <div class="zodiac-icon">
                <i class="fas fa-fw fa-arrow-right"></i>
              </div>
              <h5>Sagittarius</h5>
              <p class="zodiac-desc">Adventurous & Free - Bohemian styles and travel-ready outfits</p>
            </div>
          </div>
          <!-- Capricorn -->
          <div class="col-md-3 col-6">
            <div class="zodiac-card">
              <div class="zodiac-icon">
                <i class="fas fa-fw fa-mountain"></i>
              </div>
              <h5>Capricorn</h5>
              <p class="zodiac-desc">Classic & Ambitious - Timeless silhouettes and professional elegance</p>
            </div>
          </div>
          <!-- Aquarius -->
          <div class="col-md-3 col-6">
            <div class="zodiac-card">
              <div class="zodiac-icon">
                <i class="fas fa-fw fa-water"></i>
              </div>
              <h5>Aquarius</h5>
              <p class="zodiac-desc">Innovative & Unique - Futuristic cuts and unconventional styling</p>
            </div>
          </div>
          <!-- Pisces -->
          <div class="col-md-3 col-6">
            <div class="zodiac-card">
              <div class="zodiac-icon">
                <i class="fas fa-fw fa-fish"></i>
              </div>
              <h5>Pisces</h5>
              <p class="zodiac-desc">Dreamy & Artistic - Flowing fabrics and ethereal, romantic looks</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Ready to Transform Your Style Section -->
    <section class="cta-section">
      <div class="container">
        <div class="cta-content">
          <h2>Ready to Transform Your Style?</h2>
          <p>Join thousands of fashion-forward individuals who use astrology to enhance their personal style.</p>
          <a href="./auth/signin.php" class="btn cta-btn">Sign In to Your Account</a>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer>
      <div class="container">
        <div class="row">
          <div class="col-lg-4 mb-4">
            <div class="footer-brand">CELESTICARE</div>
            <p class="footer-description">
              Where astrology meets fashion. Discover your unique style through the wisdom of the stars and express your true cosmic self.
            </p>
            <div class="social-links">
              <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
              <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
              <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
              <a href="#" title="Pinterest"><i class="fab fa-pinterest"></i></a>
            </div>
          </div>
          
          <div class="col-lg-2 col-md-4 mb-4">
            <div class="footer-links">
              <h5>Quick Links</h5>
              <a href="#">Home</a>
              <a href="#">Zodiacs</a>
              <a href="#">Forecast</a>
              <a href="#">About Us</a>
              <a href="#">Contact</a>
            </div>
          </div>
          
          <div class="col-lg-3 col-md-4 mb-4">
            <div class="footer-links">
              <h5>Zodiac Signs</h5>
              <a href="#">Aries</a>
              <a href="#">Taurus</a>
              <a href="#">Gemini</a>
              <a href="#">Cancer</a>
              <a href="#">View All Signs</a>
            </div>
          </div>
          
          <div class="col-lg-3 col-md-4 mb-4">
            <div class="footer-links">
              <h5>Newsletter</h5>
              <p class="footer-description" style="margin-bottom: 1rem;">Get daily fashion tips based on your zodiac sign.</p>
              <div class="input-group mb-3">
                <input type="email" class="form-control subscribe-input" placeholder="Your email address">
                <button class="btn subscribe-btn">Subscribe</button>
              </div>
            </div>
          </div>
        </div>
        
        <div class="footer-bottom">
          <div class="row align-items-center">
            <div class="col-md-6">
              <p class="mb-0">&copy; 2024 CelestiCare. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-md-end">
              <a href="#" class="me-3">Privacy Policy</a>
              <a href="#">Terms of Service</a>
            </div>
          </div>
        </div>
      </div>
    </footer>
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
  
  // Music control functionality
  function setupMusic() {
    const music = document.getElementById('backgroundMusic');
    const musicControl = document.getElementById('musicControl');

    music.volume = 0.3;

    // Try to play music automatically when page loads
    window.addEventListener('load', function() {
      music.play().catch(error => {
        console.log('Autoplay prevented:', error);
        // If autoplay is blocked, show a play button
        musicControl.innerHTML = '▶️';
        musicControl.title = 'Click to play music (autoplay was blocked)';
      });
    });

    // Toggle mute/unmute when music control is clicked
    musicControl.addEventListener('click', function() {
      if (music.paused) {
        // If music is paused, play it
        music.play().then(() => {
          music.muted = false;
          musicControl.innerHTML = '🔈';
          musicControl.classList.remove('muted');
          musicControl.title = 'Click to mute';
        }).catch(error => {
          console.log('Play failed:', error);
        });
      } else {
        // If music is playing, toggle mute
        music.muted = !music.muted;
        if (music.muted) {
          musicControl.innerHTML = '🔇';
          musicControl.classList.add('muted');
          musicControl.title = 'Click to unmute';
        } else {
          musicControl.innerHTML = '🔈';
          musicControl.classList.remove('muted');
          musicControl.title = 'Click to mute';
        }
      }
    });

    // Update icon based on initial state
    music.addEventListener('loadeddata', function() {
      if (music.muted) {
        musicControl.innerHTML = '🔇';
        musicControl.classList.add('muted');
      } else {
        musicControl.innerHTML = '🔈';
        musicControl.classList.remove('muted');
      }
    });

    // Handle cases where autoplay might be blocked by browser
    music.addEventListener('pause', function() {
      if (music.currentTime === 0) {
        // Music was never started due to autoplay restrictions
        musicControl.innerHTML = '▶️';
        musicControl.title = 'Click to play music';
      }
    });
  }

  // Initialize animations and music
  createStars();
  startShootingStars();
  setupMusic();
});
</script>

</body>
</html>