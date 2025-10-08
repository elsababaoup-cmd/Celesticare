<?php
// includes/navbar.php
// This file is safe to include from any page.
// It will start a session only if one is not already active.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// OPTIONAL: set your project base path if your project folder name is different.
// Change '/celesticare' if your app root is at a different path.
$BASE = '/celesticare';
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= $BASE ?>/index.php">CelestiCare</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
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
