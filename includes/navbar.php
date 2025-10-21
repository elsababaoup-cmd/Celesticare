<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$BASE = '/celesticare';
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

  @media (max-width: 991px) {
    nav.navbar {
      padding: 15px 20px;
    }
    
    .navbar-brand {
      font-size: 2rem !important;
    }
    
    .navbar-nav .nav-link {
      flex-direction: row;
      justify-content: flex-start;
    }
    
    .nav-icon, .nav-fa-icon {
      margin-bottom: 0;
      margin-right: 8px;
    }
  }
</style>

<nav class="navbar navbar-expand-lg navbar-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?= $BASE ?>/index.php">CELESTICARE</a>
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
              <span>Dashboard</span>
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
            <a class="nav-link" href="<?= $BASE ?>/auth/register.php">
              <i class="fas fa-user-plus nav-fa-icon"></i>
              <span>Sign Up</span>
            </a>
          </li>
        <?php endif; ?>

      </ul>
    </div>
  </div>
</nav>