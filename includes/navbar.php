<?php
// includes/navbar.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Base path (update if your folder name is different)
$BASE = '/celesticare';
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
  nav.navbar {
    background: #ffffff;
    padding: 15px 60px;
    font-family: 'Poppins', sans-serif;
    border-bottom: 1px solid #eee;
  }

  .navbar-brand {
    font-weight: 700;
    color: #000 !important;
    font-size: 1.5rem;
    letter-spacing: 0.5px;
  }

  .navbar-nav .nav-link {
    color: #333 !important;
    font-weight: 500;
    margin: 0 10px;
    transition: color 0.3s ease;
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

  @media (max-width: 991px) {
    nav.navbar {
      padding: 15px 20px;
    }
  }
</style>

<nav class="navbar navbar-expand-lg navbar-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?= $BASE ?>/index.php">CelestiCare</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav align-items-center">
        <?php if (!empty($_SESSION['user_id'])): ?>
          <li class="nav-item"><a class="nav-link" href="<?= $BASE ?>/dashboard/index.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= $BASE ?>/auth/edit_profile.php">Edit Profile</a></li>
          <li class="nav-item"><a class="nav-link text-danger" href="<?= $BASE ?>/auth/logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="<?= $BASE ?>/index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= $BASE ?>/about.php">About</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= $BASE ?>/auth/register.php">Sign Up</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= $BASE ?>/auth/login.php">Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
