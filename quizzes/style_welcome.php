<?php
session_start();
include("../includes/navbar.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome | CelestiCare</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      background: linear-gradient(135deg, #2e2e2e 0%, #3b3b3b 100%);
      font-family: 'Poppins', sans-serif;
      overflow: hidden;
      transition: filter 0.6s ease, opacity 0.6s ease;
    }

    .fade-blur-out {
      opacity: 0;
      filter: blur(25px);
    }

    .welcome-container {
      min-height: calc(100vh - 80px);
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }

    /* Floating light glow accents */
    .bg-accent {
      position: absolute;
      border-radius: 50%;
      filter: blur(120px);
      opacity: 0.3;
      z-index: 0;
      animation: float 10s ease-in-out infinite alternate;
    }

    .bg-accent.one { background: #bba6ff; width: 400px; height: 400px; top: -80px; left: -80px; }
    .bg-accent.two { background: #d2b0ff; width: 500px; height: 500px; bottom: -100px; right: -100px; }

    @keyframes float {
      from { transform: translateY(0); }
      to { transform: translateY(25px); }
    }

    /* Light purple gradient welcome box */
    .welcome-box {
      position: relative;
      z-index: 2;
      background: linear-gradient(135deg, #e8e4f2 0%, #c6b9e8 100%);
      backdrop-filter: blur(20px);
      border-radius: 35px;
      padding: 80px 60px;
      text-align: center;
      max-width: 850px;
      width: 100%;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.45);
      color: #2e2e2e;
      animation: fadeIn 1s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .brand-title {
      font-weight: 700;
      font-size: 2.8rem;
      letter-spacing: 3px;
      margin-bottom: 25px;
      color: #4b3f77;
      text-shadow: 0 0 10px rgba(255, 255, 255, 0.35);
    }

    .welcome-box h2 {
      font-size: 1.9rem;
      font-weight: 600;
      color: #3e3e3e;
      margin-bottom: 25px;
    }

    .welcome-box p {
      font-size: 1.15rem;
      color: #3f3f3f;
      margin-bottom: 35px;
      line-height: 1.8;
      max-width: 700px;
      margin-left: auto;
      margin-right: auto;
    }

    .btn-start {
      background-color: #4b3f77;
      color: #fff;
      border: none;
      border-radius: 40px;
      padding: 15px 55px;
      font-weight: 600;
      font-size: 1.1rem;
      transition: all 0.35s ease;
    }

    .btn-start:hover {
      background-color: #6b5b95;
      transform: scale(1.08);
    }

    @media (max-width: 992px) {
      .welcome-box { padding: 60px 30px; }
    }
  </style>
</head>
<body>
  <div class="welcome-container">
    <div class="bg-accent one"></div>
    <div class="bg-accent two"></div>

    <div class="welcome-box">
      <div class="brand-title">CELESTICARE</div>
      <h2>Discover Your Stylistic Vibe</h2>
      <p>
        Every person carries a signature visual rhythm — a harmony between personality, energy, and style.
        Begin your journey to uncover the style that defines and best suits you, shaping how you see and express yourself.
      </p>
      <a href="bodytype_analysis.php" class="btn btn-start mt-3" id="startBtn">Begin!</a>
    </div>
  </div>

  <script>
    const startBtn = document.getElementById("startBtn");
    startBtn.addEventListener("click", function(e) {
      e.preventDefault();
      document.body.classList.add("fade-blur-out");
      setTimeout(() => { window.location.href = this.href; }, 600);
    });
  </script>
</body>
</html>
