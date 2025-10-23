<?php
session_start();

// ----------------------
// 1. Make sure zodiac is set
// ----------------------
$zodiac = $_SESSION['zodiac_sign'] ?? $_COOKIE['zodiac_sign'] ?? null;

if (!$zodiac) {
    header("Location: ../zodiac/zodiac_result.php");
    exit();
}

// ----------------------
// 2. Handle undertone selection via GET
// ----------------------
if (isset($_GET['tone'])) {
    $tone = $_GET['tone'];
    $_SESSION['undertone'] = $tone;
    setcookie("undertone", $tone, time() + (86400 * 30), "/"); // 30 days

    // ✅ CRITICAL FIX: Save to database if user is logged in
    if (isset($_SESSION['user_id'])) {
        include(__DIR__ . "/../config/dbconfig.php");
        $user_id = $_SESSION['user_id'];
        $query = "UPDATE users SET undertone = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $tone, $user_id);
        $stmt->execute();
    }

    header("Location: undertone_result.php");
    exit();
}

// Only include navbar after potential redirects
include("../includes/navbar.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Undertone Test - CelestiCare</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
  background: linear-gradient(135deg, #b99aeeff 0%, #e9e4f0 100%);
  font-family: 'Poppins', sans-serif;
  text-align: center;
  padding-top: 140px;
  margin: 0;
}
.navbar {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  z-index: 999;
}
.quiz-box {
  max-width: 900px;
  margin: auto;
}
h1 {
  font-weight: 700;
  font-size: 2rem;
  margin-bottom: 20px;
}
p {
  font-size: 1.1rem;
  color: #333;
  margin-bottom: 40px;
}
.images {
  display: flex;
  justify-content: center;
  gap: 40px;
  flex-wrap: wrap;
  margin-bottom: 30px;
}
.image-wrapper {
  display: inline;
  flex-direction: column;
  align-items: center;
}
.image-option {
  width: 150px;
  height: 130px;
  border-radius: 15px;
  background-size: cover;
  background-position: center;
  cursor: pointer;
  border: 3px solid transparent;
  transition: 0.3s;
}
.image-option:hover {
  transform: scale(1.05);
  border-color: #6b5b95;
}
.label {
  margin-top: 10px;
  font-weight: 600;
  color: #333;
  text-transform: capitalize;
}
.notes-container {
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 20px;
}
.note {
  background: #e8e8e8;
  padding: 20px;
  border-radius: 15px;
  flex: 1;
  min-width: 250px;
  color: #333;
  font-size: 1rem;
  text-align: left;
}
.quiz-box {
  background: #fff;
  padding: 40px;
  border-radius: 20px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.1);
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

<div class="quiz-box">
  <!-- Welcome instructions merged here -->
  <h1>Let's know your undertone</h1>
  <p>Check the color of your veins on your wrist and click the image that best matches your skin tone.</p>


  <!-- Undertone images -->
  <div class="images">
    <div class="image-wrapper">
      <div class="image-option" style="background-image: url('../undertone/assets/cool_skin.png');" data-tone="cool"></div>
      <div class="label">Cool</div>
    </div>
    <div class="image-wrapper">
      <div class="image-option" style="background-image: url('../undertone/assets/neutral_skin.png');" data-tone="neutral"></div>
      <div class="label">Neutral</div>
    </div>
    <div class="image-wrapper">
      <div class="image-option" style="background-image: url('../undertone/assets/warm_skin.png');" data-tone="warm"></div>
      <div class="label">Warm</div>
    </div>
  </div>

  <!-- Notes -->
  <div class="notes-container">
    <div class="note">
      • Use daylight for best accuracy to see your vein color clearly.
    </div>
    <div class="note">
      • Gold jewelry suits warm undertones, silver suits cool. Both look good? You may be neutral.
    </div>
  </div>
</div>

<script>
// Save undertone in session by reloading same page with GET
document.querySelectorAll('.image-option').forEach(option => {
  option.addEventListener('click', () => {
    const tone = option.getAttribute('data-tone');
    // Redirect to same page with GET parameter to trigger PHP session save
    window.location.href = `undertone_test.php?tone=${tone}`;
  });
});
</script>

</body>
</html>