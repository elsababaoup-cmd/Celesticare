<?php
session_start();
include("../includes/navbar.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Aesthetic Quiz - CelestiCare</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      background: linear-gradient(135deg, #d3cce3 0%, #705794ff 100%);
      font-family: 'Poppins', sans-serif;
      overflow-y: auto;
      transition: filter 0.6s ease, opacity 0.6s ease; /* 🆕 smooth blur transition */
    }

    /* 🆕 fade + blur animation class */
    .fade-blur-out {
      opacity: 0;
      filter: blur(25px);
    }

    .result-container {
      min-height: calc(100vh - 80px);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 60px 20px;
    }

    .result-box {
      background-color: #2e2e2e;
      color: #ffffff;
      padding: 80px 60px;
      border-radius: 35px;
      max-width: 850px;
      width: 100%;
      box-shadow: 0 10px 40px rgba(0,0,0,0.25);
      text-align: center;
      animation: fadeIn 0.8s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .brand-title {
      font-weight: 700;
      font-size: 36px;
      letter-spacing: 2px;
      margin-bottom: 30px;
      color: #fff;
    }

    .result-box h2 {
      font-size: 1.8rem;
      font-weight: 600;
      color: #e4e4e4;
      margin-bottom: 25px;
    }

    .result-box p {
      font-size: 1.15rem;
      color: #dcdcdc;
      margin-bottom: 35px;
      line-height: 1.8;
      max-width: 700px;
      margin-left: auto;
      margin-right: auto;
    }

    .btn-continue {
      background-color: #6b5b95;
      color: #fff;
      border: none;
      border-radius: 35px;
      padding: 15px 45px;
      font-weight: 500;
      font-size: 1.1rem;
      transition: 0.3s ease;
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
      <h2>Discover Your Aesthetic Essence</h2>
      <p>
        Every individual has a unique style energy — a blend of personality, mood, and self-expression.
        Knowing your aesthetic helps you express your identity confidently and create a cohesive wardrobe that truly reflects who you are.
      </p>
      <a href="aesthetic_quiz.php" class="btn btn-continue mt-3" id="beginBtn">Begin</a>
    </div>
  </div>

  <!-- 🆕 Fade + Blur Transition Script -->
  <script>
    const beginBtn = document.getElementById("beginBtn");

    beginBtn.addEventListener("click", function(e) {
      e.preventDefault(); // stop instant navigation
      document.body.classList.add("fade-blur-out");

      // Wait for transition to finish, then go to next page
      setTimeout(() => {
        window.location.href = this.href;
      }, 600); // matches transition duration
    });
  </script>
</body>
</html>
