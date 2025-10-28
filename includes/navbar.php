<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$BASE = '/celesticare';

// ✅ ADD THIS: Restore user data from database if logged in
if (isset($_SESSION['user_id']) && empty($_SESSION['zodiac_sign'])) {
    include(__DIR__ . "/../config/dbconfig.php");
    $user_id = $_SESSION['user_id'];
    
    $query = "SELECT zodiac_sign, undertone, season FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        // Restore missing session data from database
        if (empty($_SESSION['zodiac_sign']) && !empty($user['zodiac_sign'])) {
            $_SESSION['zodiac_sign'] = $user['zodiac_sign'];
            setcookie('zodiac_sign', $user['zodiac_sign'], time() + (86400 * 30), "/");
        }
        
        if (empty($_SESSION['undertone']) && !empty($user['undertone'])) {
            $_SESSION['undertone'] = $user['undertone'];
            setcookie('undertone', $user['undertone'], time() + (86400 * 30), "/");
        }
        
        if (empty($_SESSION['season']) && !empty($user['season'])) {
            $_SESSION['season'] = $user['season'];
        }
    }
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
  nav.navbar {
    background: #ffffff;
    padding: 15px 60px;
    font-family: 'Poppins', sans-serif;
    border-bottom: 1px solid #eee;
    min-height: 80px;
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1030;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
  }

  .navbar-brand {
    font-weight: 900 !important;
    font-family: 'Poppins', sans-serif;
    color: #000000ff !important;
    font-size: 2rem !important;
    letter-spacing: 2px !important;
    text-transform: uppercase !important;
    padding: 10px 20px !important;
    border-radius: 10px !important;
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    height: 60px !important;
  }

  .navbar-logo {
    height: 80px;
    width: auto;
    object-fit: contain;
    max-height: 80px;
  }

  .navbar-nav .nav-link {
    color: #333 !important;
    font-weight: 500;
    margin: 0 10px;
    transition: color 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 8px 12px;
  }

  .navbar-nav .nav-link:hover {
    color: #000 !important;
  }

  .navbar-nav .nav-link.text-danger {
    color: #d9534f !important;
  }

  .navbar-toggler {
    border: none;
    padding: 4px 8px;
  }

  .navbar-toggler:focus {
    box-shadow: none !important;
  }

  .navbar-toggler-icon {
    width: 1.5em;
    height: 1.5em;
  }

  /* Mobile menu styles */
  @media (max-width: 991px) {
    nav.navbar {
      padding: 15px 20px;
      min-height: 70px;
    }
    
    .navbar-collapse {
      background: #ffffff;
      margin-top: 15px;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      padding: 10px;
    }
    
    .navbar-nav .nav-link {
      flex-direction: row;
      justify-content: flex-start;
      padding: 12px 15px;
      border-bottom: 1px solid #f5f5f5;
      margin: 0;
    }
    
    .nav-icon, .nav-fa-icon {
      margin-bottom: 0;
      margin-right: 12px;
    }

    .navbar-brand {
      font-size: 1.8rem !important;
      height: 50px !important;
    }

    .navbar-logo {
      height: 35px;
      max-height: 40px;
    }
  }

  .nav-icon {
    width: 24px;
    height: 24px;
    margin-bottom: 4px;
    transition: transform 0.3s ease;
    object-fit: contain;
  }

  .nav-link:hover .nav-icon {
    transform: translateY(-2px);
  }

  .nav-fa-icon {
    font-size: 1.2rem;
    margin-bottom: 4px;
    transition: transform 0.3s ease;
  }

  .nav-link:hover .nav-fa-icon {
    transform: translateY(-2px);
  }

  /* Body padding to account for fixed navbar */
  body {
    padding-top: 100px !important;
  }

  @media (max-width: 991px) {
    body {
      padding-top: 90px !important;
    }
  }
</style>

<nav class="navbar navbar-expand-lg navbar-light fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?= $BASE ?>/index.php">
      <img src="<?= $BASE ?>/includes/logo1.png" alt="CelestiCare Logo" class="navbar-logo">
      CELESTICARE
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav align-items-center">

        <?php if (!empty($_SESSION['user_id'])): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?= $BASE ?>/dashboard/index.php">
              <img src="<?= $BASE ?>/assets/icons/dashboard.png" alt="Dashboard" class="nav-icon">
              <span>User Dashboard</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= $BASE ?>/zodiac/index.php">
              <img src="<?= $BASE ?>/assets/icons/zodiacs.png" alt="Zodiacs" class="nav-icon">
              <span>Zodiacs</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= $BASE ?>/forecast/index.php">
              <img src="<?= $BASE ?>/assets/icons/forecast.png" alt="Fashion Forecast" class="nav-icon">
              <span>Fashion Forecast</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= $BASE ?>/about.php">
              <i class="fas fa-info-circle nav-fa-icon"></i>
              <span>About Us</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-danger" href="<?= $BASE ?>/auth/logout.php">
              <i class="fas fa-sign-out-alt nav-fa-icon"></i>
              <span>Logout</span>
            </a>
          </li>

        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="<?= $BASE ?>/index.php">
              <i class="fas fa-home nav-fa-icon"></i>
              <span>Home</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= $BASE ?>/about.php">
              <i class="fas fa-info-circle nav-fa-icon"></i>
              <span>About</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= $BASE ?>/auth/login.php">
              <i class="fas fa-user-plus nav-fa-icon"></i>
              <span>Login</span>
            </a>
          </li>
        <?php endif; ?>

      </ul>
    </div>
  </div>
</nav>

<!-- Make sure Bootstrap JS is loaded -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Close mobile menu when clicking on a link
document.addEventListener('DOMContentLoaded', function() {
  const navLinks = document.querySelectorAll('.nav-link');
  const navbarCollapse = document.querySelector('.navbar-collapse');
  
  navLinks.forEach(link => {
    link.addEventListener('click', () => {
      if (window.innerWidth < 992) {
        const bsCollapse = new bootstrap.Collapse(navbarCollapse);
        bsCollapse.hide();
      }
    });
  });
});
</script>