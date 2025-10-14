<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/dbconfig.php");
include(__DIR__ . "/../includes/navbar.php");

// ✅ Fetch user data
$user_id = $_SESSION['user_id'];
$query = "SELECT username, email, name, gender, birthdate, zodiac_sign, undertone, season FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$username = htmlspecialchars($user['username']);
$name = htmlspecialchars($user['name'] ?? '');
$email = htmlspecialchars($user['email']);
$gender = htmlspecialchars($user['gender'] ?? '');
$birthdate = htmlspecialchars($user['birthdate'] ?? '');
$zodiac = htmlspecialchars($user['zodiac_sign'] ?? 'Not set');
$undertone = htmlspecialchars($user['undertone'] ?? 'Not set');
$season = htmlspecialchars($user['season'] ?? 'Not set');

// ✅ Astro facts
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
$astroFact = $astroFacts[$zodiac] ?? "Complete your zodiac profile to unlock your astro fact!";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard - CelestiCare</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
html, body {
    height: 100%;
    margin: 0;
    background: linear-gradient(135deg, #d3cce3 0%, #e9e4f0 100%);
    font-family: 'Poppins', sans-serif;
}
.container { max-width: 1200px; }
.card {
    border-radius: 15px;
    background-color: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
h2 { color: #6a1b9a; font-weight: 600; }
.section-title { font-size: 1.2rem; font-weight: 600; color: #333; }
.profile-section, .astro-section, .color-section, .aesthetic-section, .outfit-section {
    border-radius: 10px;
    background-color: #fff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    padding: 20px;
}
.grid-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 20px;
    margin-top: 30px;
}
.color-wheel { max-width: 150px; display: block; margin: 0 auto 10px; }
.btn-style {
    display: block;
    margin: 10px auto;
    background-color: #000;
    color: #fff;
    border: none;
    padding: 10px 18px;
    border-radius: 25px;
    font-size: 0.9rem;
    font-weight: 500;
    transition: background 0.3s ease;
}
.btn-style:hover { background-color: #333; }
.btn-warning { background-color: #f7b731; border: none; }
.btn-warning:hover { background-color: #f9ca4f; }
.btn-danger { background-color: #e74c3c; border: none; }
.btn-danger:hover { background-color: #ff6b5a; }

/* 🔮 MODAL DESIGN MATCHING LOGIN */
.modal-content {
    background-color: #2e2e2e;
    color: #ffffff;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}
.modal-title {
    font-size: 20px;
    text-align: center;
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
.btn-primary {
    background-color: #6b5b95;
    border: none;
    border-radius: 30px;
    padding: 10px 20px;
    width: 100%;
    transition: 0.3s;
}
.btn-primary:hover {
    background-color: #8c77c5;
}
@keyframes fadeIn {
    from { transform: translateY(-20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
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
<div class="container mt-5 mb-5">
    <div class="card p-4 shadow-sm">
        <h2>The Universe Welcomes your presence, <?php echo $username; ?>!</h2>
        <p>This is your dashboard where your zodiac style journey begins.</p>

        <div class="grid-container mt-4">
            <div class="profile-section">
                <div class="section-title">Profile</div>
                <p><strong>Name:</strong> <span id="displayName"><?= $name ?: 'Not set' ?></span></p>
                <p><strong>Username:</strong> <?= $username ?></p>
                <p><strong>Email:</strong> <?= $email ?></p>
                <p><strong>Gender:</strong> <span id="displayGender"><?= $gender ?: 'Not set' ?></span></p>
                <p><strong>Date of Birth:</strong> <span id="displayBirthdate"><?= $birthdate ?: 'Not set' ?></span></p>
                <p><strong>Zodiac Sign:</strong> <span id="displayZodiac"><?= $zodiac ?></span></p>

            </div>

            <div class="astro-section">
                <div class="section-title">Astro Fact</div>
                <p id="displayFact"><?= $astroFact ?></p>
            </div>

            <div class="color-section text-center" id="colorSection">
                <div class="section-title mb-2">Your Color Analysis</div>

                <img src="../assets/images/color-wheel.png" alt="Color Wheel" class="color-wheel" id="colorWheel">
                <div id="colorPalette" class="d-flex justify-content-center gap-2 mt-2"></div>
                <p class="mt-3"><strong>Undertone:</strong> <span id="displayUndertone"><?= $undertone ?></span></p>
                <p><strong>Best Season:</strong> <span id="displaySeason"><?= $season ?></span></p>
                <p class="text-muted small" id="displaySeasonDesc">
                <?= $season && $season !== 'Not set' ? "Personalized palette based on your undertone and season." : "Discover your color palette by taking the color analysis!" ?>
                </p> 
                <a href="../color-analysis/index.php" class="btn-style mt-3" id="analyzeBtn">I want to know!</a>
                </div>

            <div class="aesthetic-section text-center">
                <div class="section-title mb-2">Aesthetics</div>
                <p>Want to know your Aesthetic?</p>
                <a href="../aesthetics/index.php" class="btn-style">I want to know!</a>
            </div>

            <div class="outfit-section text-center">
                <div class="section-title mb-2">Your Outfits</div>
                <p>Want to know your best outfits?</p>
                <a href="../style/index.php" class="btn-style">I want to know!</a>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profile</button>
            <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
</div>

<!-- 🌙 Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <h5 class="modal-title mb-3">Edit Profile</h5>
      <form id="editProfileForm">
        <div class="mb-3">
            <input type="text" name="name" class="form-control" placeholder="Full Name" value="<?= $name ?>">
        </div>
        <div class="mb-3">
            <input type="date" name="birthdate" id="birthdate" class="form-control" value="<?= $birthdate ?>">
        </div>
        <div class="mb-3">
            <select name="gender" class="form-control">
                <option value="">Select Gender</option>
                <option value="Masculine" <?= $gender=='Masculine' ? 'selected' : '' ?>>Masculine</option>
                <option value="Feminine" <?= $gender=='Feminine' ? 'selected' : '' ?>>Feminine</option>
            </select>
        </div>
        <input type="hidden" name="zodiac_sign" id="zodiac_sign" value="<?= $zodiac ?>">
        <button type="submit" class="btn btn-primary mt-2">Save Changes</button>
        <button type="button" class="btn btn-secondary w-100 mt-2" data-bs-dismiss="modal">Cancel</button>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// 🌟 Zodiac logic
function getZodiacSign(month, day) {
  const zodiacs = [
    { sign: "Capricorn", start: [12, 22], end: [1, 19] },
    { sign: "Aquarius", start: [1, 20], end: [2, 18] },
    { sign: "Pisces", start: [2, 19], end: [3, 20] },
    { sign: "Aries", start: [3, 21], end: [4, 19] },
    { sign: "Taurus", start: [4, 20], end: [5, 20] },
    { sign: "Gemini", start: [5, 21], end: [6, 20] },
    { sign: "Cancer", start: [6, 21], end: [7, 22] },
    { sign: "Leo", start: [7, 23], end: [8, 22] },
    { sign: "Virgo", start: [8, 23], end: [9, 22] },
    { sign: "Libra", start: [9, 23], end: [10, 22] },
    { sign: "Scorpio", start: [10, 23], end: [11, 21] },
    { sign: "Sagittarius", start: [11, 22], end: [12, 21] }
  ];
  for (let z of zodiacs) {
    const [startMonth, startDay] = z.start;
    const [endMonth, endDay] = z.end;
    if (
      (month === startMonth && day >= startDay) ||
      (month === endMonth && day <= endDay)
    ) return z.sign;
  }
  return "Capricorn";
}

// 🪄 Update hidden zodiac field when birthdate changes
document.getElementById("birthdate").addEventListener("change", function() {
  const date = new Date(this.value);
  if (!isNaN(date)) {
    const zodiac = getZodiacSign(date.getMonth() + 1, date.getDate());
    document.getElementById("zodiac_sign").value = zodiac;
  }
});

// 💾 Save via AJAX
document.getElementById("editProfileForm").addEventListener("submit", async function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  const response = await fetch("save_profile_ajax.php", { method: "POST", body: formData });
  const result = await response.json();

  if (result.success) {
    // Update dashboard display instantly
    document.getElementById("displayName").textContent = result.data.name || "Not set";
    document.getElementById("displayGender").textContent = result.data.gender || "Not set";
    document.getElementById("displayBirthdate").textContent = result.data.birthdate || "Not set";
    document.getElementById("displayZodiac").textContent = result.data.zodiac_sign || "Not set";
    document.getElementById("displayFact").textContent = result.data.astroFact;
    const modal = bootstrap.Modal.getInstance(document.getElementById("editProfileModal"));
    modal.hide();
  } else {
    alert("Error: " + result.message);
  }
});
</script>
</body>
</html>