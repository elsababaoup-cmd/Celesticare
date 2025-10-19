<?php
session_start();
include("../includes/navbar.php");
include("../config/dbconfig.php");

// --- Fetch user gender from DB ---
$user_id = $_SESSION['user_id'] ?? null;
$gender = null;

if ($user_id) {
    $stmt = $conn->prepare("SELECT gender FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($gender);
    $stmt->fetch();
    $stmt->close();
}

// --- Normalize gender just in case ---
$gender = strtolower(trim($gender)); // e.g. "Feminine" → "feminine"
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Body Type Analysis | CelestiCare</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      background: linear-gradient(135deg, #615564ff 0%, #3c314bff 100%);
      font-family: 'Poppins', sans-serif;
      color: #fff;
      overflow-x: hidden;
    }

    .container-bodytype {
      padding: 60px 20px 100px; /* reduced top padding from 100px → 60px */
      text-align: center;
      position: relative;
      z-index: 2;
    }


    h1 {
      font-weight: 700;
      font-size: 2.5rem;
      margin-bottom: 20px;
      color: #d4c6ff;
      text-shadow: 0 0 10px rgba(255,255,255,0.2);
    }

    p {
      color: #d8d8d8;
      font-size: 1.1rem;
      max-width: 800px;
      margin: 0 auto 60px;
    }

    .bodytype-grid {
      display: grid;
      gap: 25px;
      justify-content: center;
    }

    .fem-grid {
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      max-width: 1100px;
      margin: 0 auto 70px;
    }

    .masc-grid {
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      max-width: 900px;
      margin: 0 auto;
    }

    .bodytype-card {
      background: rgba(255,255,255,0.05);
      border-radius: 25px;
      padding: 20px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      cursor: pointer;
    }

    .bodytype-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 8px 30px rgba(255,255,255,0.15);
    }

    .bodytype-card img {
      width: 100%;
      height: auto;
      object-fit: contain;
      aspect-ratio: 440 / 1238;
    }

    .masc-grid img {
      aspect-ratio: 380 / 1047;
    }

    .bodytype-label {
      margin-top: 10px;
      font-weight: 600;
      font-size: 1rem;
      color: #dcd3ff;
    }

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

    .selected {
      border: 2px solid #bba6ff;
      box-shadow: 0 0 25px rgba(187,166,255,0.4);
      transform: scale(1.03);
    }

    .continue-btn {
      display: none;
      margin-top: 40px;
      padding: 12px 30px;
      background: #bba6ff;
      color: #222;
      border: none;
      border-radius: 30px;
      font-weight: 600;
      font-size: 1rem;
      transition: all 0.3s ease;
    }

    .continue-btn:hover {
      background: #d4c6ff;
      transform: translateY(-3px);
    }

    /* === Updated Modal to Match Welcome Page === */
    .modal-content {
      background: linear-gradient(135deg, #e8e4f2 0%, #c6b9e8 100%);
      border-radius: 35px;
      border: none;
      text-align: center;
      color: #2e2e2e; /* <-- Base text color changed to dark gray */
      padding: 60px 40px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.45);
      animation: fadeIn 0.8s ease;
    }

    .modal-content h5 {
      font-weight: 700;
      font-size: 1.9rem;
      color: #3e3e3e; /* <-- Darker for better visibility */
      margin-bottom: 20px;
    }

    .modal-content p {
      color: #4b4b4b; /* <-- Added paragraph color fix */
      font-size: 1.05rem;
      line-height: 1.8;
      margin-bottom: 30px;
    }

    .modal-content .btn-primary {
      background-color: #4b3f77;
     color: #fff;
      border: none;
      border-radius: 40px;
      padding: 15px 55px;
      font-weight: 600;
      font-size: 1.1rem;
      transition: all 0.35s ease;
    }

    .modal-content .btn-primary:hover {
      background-color: #6b5b95;
      transform: scale(1.08);
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
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
  <div class="bg-accent one"></div>
  <div class="bg-accent two"></div>

  <div class="container-bodytype">
    <h1>Body Type Analysis</h1>
    <p>Select the silhouette that best represents your body shape to personalize your style recommendations.</p>

    <?php if ($gender === 'feminine'): ?>
      <div class="bodytype-grid fem-grid">
        <div class="bodytype-card"><img src="assets/fem_hourglass.png" alt="Hourglass"><div class="bodytype-label">Hourglass</div></div>
        <div class="bodytype-card"><img src="assets/fem_inverted.png" alt="Inverted Triangle"><div class="bodytype-label">Inverted Triangle</div></div>
        <div class="bodytype-card"><img src="assets/fem_pear.png" alt="Pear"><div class="bodytype-label">Pear</div></div>
        <div class="bodytype-card"><img src="assets/fem_rectangle.png" alt="Rectangle"><div class="bodytype-label">Rectangle</div></div>
        <div class="bodytype-card"><img src="assets/fem_round.png" alt="Round"><div class="bodytype-label">Round</div></div>
        <div class="bodytype-card"><img src="assets/fem_standard.png" alt="Standard"><div class="bodytype-label">Standard</div></div>
      </div>

    <?php elseif ($gender === 'masculine'): ?>
      <div class="bodytype-grid masc-grid">
        <div class="bodytype-card"><img src="assets/masc_trapezoid.png" alt="Trapezoid"><div class="bodytype-label">Trapezoid</div></div>
        <div class="bodytype-card"><img src="assets/masc_rectangle.png" alt="Rectangle"><div class="bodytype-label">Rectangle</div></div>
        <div class="bodytype-card"><img src="assets/masc_round.png" alt="Round"><div class="bodytype-label">Round</div></div>
        <div class="bodytype-card"><img src="assets/masc_triangle.png" alt="Triangle"><div class="bodytype-label">Triangle</div></div>
        <div class="bodytype-card"><img src="assets/masc_inverted.png" alt="Inverted Triangle"><div class="bodytype-label">Inverted Triangle</div></div>
      </div>

    <?php else: ?>
      <p style="color:#ccc;">We couldn’t find your gender in the database. Please update it in your dashboard to continue.</p>
    <?php endif; ?>

    <button class="continue-btn" id="continueBtn">Continue</button>
  </div>

  <!-- Updated Modal -->
  <div class="modal fade" id="nextStepModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <h5>Discover Your Stylistic Vibe</h5>
        <p>
          Every person carries a signature visual rhythm — a harmony between personality, energy, and style.
          Let’s begin shaping how you see and express yourself.
          
          Dress your Personal Mannequin, You can right click to remove unwanted clothing.
        </p>
        <div class="mt-4">
          <a href="style_quiz.php" class="btn btn-primary">Go to Style Quiz</a>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const cards = document.querySelectorAll('.bodytype-card');
    const continueBtn = document.getElementById('continueBtn');

    cards.forEach(card => {
      card.addEventListener('click', () => {
        cards.forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        continueBtn.style.display = 'inline-block';
      });
    });

    continueBtn.addEventListener('click', () => {
      const modal = new bootstrap.Modal(document.getElementById('nextStepModal'));
      modal.show();
    });
  </script>
</body>
</html>
