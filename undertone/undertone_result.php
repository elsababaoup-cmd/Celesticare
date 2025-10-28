<?php
session_start();
include("../includes/navbar.php");

// ✅ IMPROVED: Better zodiac and undertone retrieval
$zodiac = null;
$undertone = null;

// For logged-in users, always check database first
if (isset($_SESSION['user_id'])) {
    include("../config/dbconfig.php");
    $user_id = $_SESSION['user_id'];
    $query = "SELECT zodiac_sign, undertone FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        // Prioritize database values over session/cookie
        $zodiac = $user['zodiac_sign'] ?? null;
        $undertone = $user['undertone'] ?? null;
        
        // Sync session with database to prevent future issues
        if ($zodiac) $_SESSION['zodiac_sign'] = $zodiac;
        if ($undertone) $_SESSION['undertone'] = $undertone;
    }
    $stmt->close();
}

// Fallback to session/cookie if still not set
if (!$zodiac) {
    $zodiac = $_SESSION['zodiac_sign'] ?? $_COOKIE['zodiac_sign'] ?? null;
}
if (!$undertone) {
    $undertone = $_SESSION['undertone'] ?? $_COOKIE['undertone'] ?? null;
}

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

    "Sagittarius" => [
        "cool" => ["#40007b","#3830a0","#24879d","#7f1d80","#ce2c82","#ce6aac"],
        "warm" => ["#52201c","#415715","#fa7e1e","#e7735a","#aac265","#edba4a"],
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
    background: linear-gradient(135deg, #d3cce3 0%, #8d65c6ff 100%);
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

.admin-notice {
    background: rgba(107, 91, 149, 0.3);
    border: 1px solid #6b5b95;
    border-radius: 10px;
    padding: 10px 15px;
    margin-bottom: 15px;
    font-size: 0.9rem;
    text-align: center;
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
  min-height: 120vh; /* forces perfect vertical center */
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

.password-container {
    position: relative;
}

.toggle-password {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #cfcfcf;
    cursor: pointer;
    z-index: 10;
}

.toggle-password:hover {
    color: #ffffff;
}

html::-webkit-scrollbar,
.modal::-webkit-scrollbar,
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
    <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
      <!-- If logged in, go straight to dashboard -->
      <a href="../dashboard/index.php" class="btn btn-login">Continue</a>
    <?php else: ?>
      <!-- If not logged in, open the modal -->
    <button type="button" class="btn btn-login" data-bs-toggle="modal" data-bs-target="#loginModal">
        Continue
      </button>
    <?php endif; ?>

<!-- LOGIN MODAL -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content login-box">
      <div class="brand-title">CELESTICARE</div>
      <h2>Log in to your CelestiCare profile</h2>

      <form id="loginForm" method="POST" action="../auth/login_process.php">
        <!-- Error message container -->
        <div id="loginError"></div>
        <div class="mb-3">
          <input type="email" name="email" class="form-control" placeholder="Email" required>
        </div>
        <div class="mb-3 password-container">
          <input type="password" name="password" id="loginPassword" class="form-control" placeholder="Password" required>
          <button type="button" class="toggle-password" id="toggleLoginPassword">
            <i class="far fa-eye"></i>
          </button>
        </div>
        <button type="submit" class="btn btn-login">Login</button>

        <p class="text-center text-muted mt-3">
          Don't have a CelestiCare profile?
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

      <form id="registerForm" method="POST" action="../auth/register_process.php">
        <!-- Error message container -->
        <div id="registerError"></div>
        <div class="mb-3">
          <input type="email" name="email" id="registerEmail" class="form-control" placeholder="Email" required>
        </div>
        <div class="mb-3">
          <input type="text" name="username" class="form-control" placeholder="Username" required>
        </div>
        
        <!-- Regular User Password Fields -->
        <div id="regularPasswordFields">
          <div class="mb-3 password-container">
            <input type="password" name="password" id="registerPassword" class="form-control" placeholder="Password (min. 8 chars, 1 uppercase)" required>
            <button type="button" class="toggle-password" id="toggleRegisterPassword">
              <i class="far fa-eye"></i>
            </button>
          </div>
          <div class="mb-3 password-container">
            <input type="password" name="confirm_password" id="confirmPassword" class="form-control" placeholder="Confirm Password" required>
            <button type="button" class="toggle-password" id="toggleConfirmPassword">
              <i class="far fa-eye"></i>
            </button>
          </div>
        </div>

        <!-- Admin Password Fields (initially hidden) -->
        <div id="adminPasswordFields" style="display: none;">
          <div class="admin-notice">
            <i class="fas fa-shield-alt me-2"></i>Admin Account Registration
          </div>
          <div class="mb-3 password-container">
            <input type="password" name="admin_password" id="adminPassword" class="form-control" placeholder="Admin Password">
            <button type="button" class="toggle-password" id="toggleAdminPassword">
              <i class="far fa-eye"></i>
            </button>
          </div>
          <div class="mb-3 password-container">
            <input type="password" name="confirm_admin_password" id="confirmAdminPassword" class="form-control" placeholder="Confirm Admin Password">
            <button type="button" class="toggle-password" id="toggleConfirmAdminPassword">
              <i class="far fa-eye"></i>
            </button>
          </div>
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
// Password visibility toggles
function setupPasswordToggles() {
    console.log('Setting up password toggles...'); // Debug log
    
    // Login password toggle
    const toggleLoginPassword = document.getElementById('toggleLoginPassword');
    const loginPassword = document.getElementById('loginPassword');
    
    if (toggleLoginPassword && loginPassword) {
        console.log('Found login password elements'); // Debug log
        toggleLoginPassword.addEventListener('click', function() {
            console.log('Login password toggle clicked'); // Debug log
            const icon = this.querySelector('i');
            if (loginPassword.type === 'password') {
                loginPassword.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                loginPassword.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    } else {
        console.log('Login password elements not found'); // Debug log
    }

    // Register password toggle
    const toggleRegisterPassword = document.getElementById('toggleRegisterPassword');
    const registerPassword = document.getElementById('registerPassword');
    
    if (toggleRegisterPassword && registerPassword) {
        console.log('Found register password elements'); // Debug log
        toggleRegisterPassword.addEventListener('click', function() {
            console.log('Register password toggle clicked'); // Debug log
            const icon = this.querySelector('i');
            if (registerPassword.type === 'password') {
                registerPassword.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                registerPassword.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    }

    // Confirm password toggle
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const confirmPassword = document.getElementById('confirmPassword');
    
    if (toggleConfirmPassword && confirmPassword) {
        console.log('Found confirm password elements'); // Debug log
        toggleConfirmPassword.addEventListener('click', function() {
            console.log('Confirm password toggle clicked'); // Debug log
            const icon = this.querySelector('i');
            if (confirmPassword.type === 'password') {
                confirmPassword.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                confirmPassword.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    }
}

// ✅ DYNAMIC PASSWORD FIELD SWITCHING
function setupPasswordFieldSwitching() {
    const registerEmail = document.getElementById('registerEmail');
    const regularPasswordFields = document.getElementById('regularPasswordFields');
    const adminPasswordFields = document.getElementById('adminPasswordFields');
    const adminPasswordInput = document.getElementById('adminPassword');
    const confirmAdminPasswordInput = document.getElementById('confirmAdminPassword');
    const regularPasswordInput = document.getElementById('registerPassword');
    const confirmPasswordInput = document.getElementById('confirmPassword');

    function updateRequiredFields(isAdmin) {
        if (isAdmin) {
            // Admin mode - make admin fields required, regular fields not required
            adminPasswordInput.required = true;
            confirmAdminPasswordInput.required = true;
            regularPasswordInput.required = false;
            confirmPasswordInput.required = false;
        } else {
            // Regular mode - make regular fields required, admin fields not required
            regularPasswordInput.required = true;
            confirmPasswordInput.required = true;
            adminPasswordInput.required = false;
            confirmAdminPasswordInput.required = false;
        }
    }

    if (registerEmail && regularPasswordFields && adminPasswordFields) {
        registerEmail.addEventListener('input', function() {
            const email = this.value.toLowerCase();
            // Show admin password fields and hide regular fields for admin emails
            if (email.includes('@celesticare.admin.com')) {
                regularPasswordFields.style.display = 'none';
                adminPasswordFields.style.display = 'block';
                updateRequiredFields(true);
                
                // Clear regular password fields
                regularPasswordInput.value = '';
                confirmPasswordInput.value = '';
            } else {
                regularPasswordFields.style.display = 'block';
                adminPasswordFields.style.display = 'none';
                updateRequiredFields(false);
                
                // Clear admin password fields
                adminPasswordInput.value = '';
                confirmAdminPasswordInput.value = '';
            }
        });

        // Also check on page load in case of back navigation
        if (registerEmail.value.includes('@celesticare.admin.com')) {
            regularPasswordFields.style.display = 'none';
            adminPasswordFields.style.display = 'block';
            updateRequiredFields(true);
        } else {
            updateRequiredFields(false);
        }
    }

    // Setup admin password toggles
    const toggleAdminPassword = document.getElementById('toggleAdminPassword');
    if (toggleAdminPassword && adminPasswordInput) {
        toggleAdminPassword.addEventListener('click', function() {
            const icon = this.querySelector('i');
            if (adminPasswordInput.type === 'password') {
                adminPasswordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                adminPasswordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    }

    const toggleConfirmAdminPassword = document.getElementById('toggleConfirmAdminPassword');
    if (toggleConfirmAdminPassword && confirmAdminPasswordInput) {
        toggleConfirmAdminPassword.addEventListener('click', function() {
            const icon = this.querySelector('i');
            if (confirmAdminPasswordInput.type === 'password') {
                confirmAdminPasswordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                confirmAdminPasswordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    }
}

// Combined function to set up all password features
function setupAllPasswordToggles() {
    setupPasswordToggles();
    setupPasswordFieldSwitching();
}
// Single DOMContentLoaded event listener
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded'); // Debug log
    
    // Initialize password toggles immediately
    setupAllPasswordToggles();
    
    // Modal elements
    const showRegister = document.getElementById('showRegister');
    const showLogin = document.getElementById('showLogin');
    const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
    const registerModal = new bootstrap.Modal(document.getElementById('registerModal'));
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    // Switch from login to register modal
    if (showRegister) {
        showRegister.addEventListener('click', function(e) {
            e.preventDefault();
            loginModal.hide();
            setTimeout(() => {
                registerModal.show();
                // Re-initialize ALL password toggles after modal switch
                setTimeout(setupAllPasswordToggles, 100);
            }, 400);
        });
    }

    // Switch from register to login modal
    if (showLogin) {
        showLogin.addEventListener('click', function(e) {
            e.preventDefault();
            registerModal.hide();
            setTimeout(() => {
                loginModal.show();
                // Re-initialize ALL password toggles after modal switch
                setTimeout(setupAllPasswordToggles, 100);
            }, 400);
        });
    }

    // Re-initialize password toggles when modals are opened
    const loginModalElement = document.getElementById('loginModal');
    const registerModalElement = document.getElementById('registerModal');

    if (loginModalElement) {
        loginModalElement.addEventListener('shown.bs.modal', function() {
            console.log('Login modal shown'); // Debug log
            setTimeout(setupAllPasswordToggles, 100);
        });
    }

    if (registerModalElement) {
        registerModalElement.addEventListener('shown.bs.modal', function() {
            console.log('Register modal shown'); // Debug log
            setTimeout(setupAllPasswordToggles, 100);
        });
    }

    // Handle login form submission with AJAX
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(loginForm);
            const loginError = document.getElementById('loginError');
            
            // Clear previous errors
            loginError.innerHTML = '';
            
            // Show loading state
            const submitBtn = loginForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Logging in...';
            submitBtn.disabled = true;

            fetch('../auth/login_process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data.startsWith('success:')) {
                    // Success - redirect to dashboard
                    const redirectUrl = data.split(':')[1];
                    window.location.href = redirectUrl;
                } else if (data.startsWith('error:')) {
                    // Error - show message in modal
                    const errorMessage = data.split(':')[1];
                    loginError.innerHTML = '<div class="alert alert-danger text-center">' + errorMessage + '</div>';
                } else {
                    // Unknown response
                    loginError.innerHTML = '<div class="alert alert-danger text-center">Login failed. Please try again.</div>';
                }
            })
            .catch(error => {
                loginError.innerHTML = '<div class="alert alert-danger text-center">Network error. Please try again.</div>';
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
    }

    // Handle register form submission with AJAX
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(registerForm);
            const registerError = document.getElementById('registerError');
            
            // Clear previous errors
            registerError.innerHTML = '';
            
            // Show loading state
            const submitBtn = registerForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Creating Account...';
            submitBtn.disabled = true;

            fetch('../auth/register_process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data.startsWith('success:')) {
                    // Success - redirect to dashboard
                    const redirectUrl = data.split(':')[1];
                    window.location.href = redirectUrl;
                } else if (data.startsWith('error:')) {
                    // Error - show message in modal
                    const errorMessage = data.split(':')[1];
                    registerError.innerHTML = '<div class="alert alert-danger text-center">' + errorMessage + '</div>';
                } else {
                    // Unknown response
                    registerError.innerHTML = '<div class="alert alert-danger text-center">Registration failed. Please try again.</div>';
                }
            })
            .catch(error => {
                registerError.innerHTML = '<div class="alert alert-danger text-center">Network error. Please try again.</div>';
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
    }

    // Handle URL parameters for auto-showing modals with errors
    const urlParams = new URLSearchParams(window.location.search);
    const modalType = urlParams.get('modal');
    
    // Check if there's an error message from login/register process
    <?php if (isset($_SESSION['error'])): ?>
        const errorMessage = "<?php echo htmlspecialchars($_SESSION['error']); ?>";
        
        if (errorMessage) {
            // Show error in appropriate modal
            if (modalType === 'login') {
                const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                const loginError = document.getElementById('loginError');
                
                // Add error message to login modal
                loginError.innerHTML = '<div class="alert alert-danger text-center">' + errorMessage + '</div>';
                loginModal.show();
                
            } else if (modalType === 'register') {
                const registerModal = new bootstrap.Modal(document.getElementById('registerModal'));
                const registerError = document.getElementById('registerError');
                
                // Add error message to register modal
                registerError.innerHTML = '<div class="alert alert-danger text-center">' + errorMessage + '</div>';
                registerModal.show();
            }
            
            // Clear the error session
            <?php unset($_SESSION['error']); ?>
        }
    <?php endif; ?>
    
    // Clear errors when switching between modals
    document.getElementById('showRegister')?.addEventListener('click', function() {
        document.getElementById('loginError').innerHTML = '';
    });
    
    document.getElementById('showLogin')?.addEventListener('click', function() {
        document.getElementById('registerError').innerHTML = '';
    });
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

// ✅ FIXED: Only save undertone data, not zodiac
<?php if (isset($_SESSION['user_id'])): ?>
const undertone = "<?= htmlspecialchars($undertone) ?>";
const season = "<?= htmlspecialchars($season ?? '') ?>";

if (undertone) {
    fetch("../user/save_color_data.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            undertone: undertone,
            season: season
            // ✅ REMOVED zodiac from the payload to prevent overwriting
        })
    })
    .then(res => res.json())
    .then(data => {
        console.log("✅ Save undertone response:", data);
    })
    .catch(err => console.error("❌ Error saving undertone data:", err));
}
<?php endif; ?>
</script>
</body>
</html>