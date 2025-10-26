<?php
session_start();
include("../includes/navbar.php");
include("../config/dbconfig.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$aesthetic = $_GET['aesthetic'] ?? $_SESSION['aesthetic_result'] ?? null;

if (!$aesthetic) {
    $stmt = $conn->prepare("SELECT aesthetic_result FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($db_aesthetic);
    $stmt->fetch();
    $stmt->close();

    if ($db_aesthetic) {
        $aesthetic = $db_aesthetic;
        $_SESSION['aesthetic_result'] = $aesthetic; // Store in session
    } else {
        header("Location: aesthetic_quiz.php");
        exit();
    }
} else {
    $stmt = $conn->prepare("UPDATE users SET aesthetic_result = ? WHERE id = ?");
    $stmt->bind_param("si", $aesthetic, $user_id);
    $stmt->execute();
    $stmt->close();
    $_SESSION['aesthetic_result'] = $aesthetic;
}

$aesthetics = [
  "academia" => [
    "title" => "The Scholar",
    "desc" => "You're drawn to timeless sophistication — think books, tweed, and candlelit study sessions. You find beauty in intellect and classic detail.",
    "history" => "Rooted in literary charm and old-world academia, this aesthetic draws from classic literature, university libraries, and British collegiate style.",
    "mood" => "Intellectual • Vintage • Poised",
    "bg" => "../quizzes/assets/acad_bg3.jpg",
    "side1" => "../quizzes/assets/acad1.jpg",
    "side2" => "../quizzes/assets/acad3.jpg",
    "color" => "#271a0eff",
    "music" => "../music/acad.mp3"
  ],
  "boho" => [
    "title" => "The Bohemian Dreamer",
    "desc" => "You embody freedom, creativity, and a love for earthy tones and textures. Every outfit tells a story — unbothered, soulful, and effortlessly chic.",
    "history" => "Emerging from 1960s counterculture, Bohemian style fuses artistic expression with natural fabrics and global influences.",
    "mood" => "Free-Spirited • Earthy • Artistic",
    "bg" => "../quizzes/assets/boho_bg2.jpg",
    "side1" => "../quizzes/assets/boho2.jpg",
    "side2" => "../quizzes/assets/boho1.jpg",
    "color" => "#e38153ff",
    "music" => "../music/boho.mp3"
  ],
  "coquette" => [
    "title" => "The Coquette Muse",
    "desc" => "Romantic and soft, your style leans into vintage elegance — ribbons, lace, and an air of flirtatious nostalgia.",
    "history" => "Blending Rococo-era charm with 90s Lolita revival, the Coquette aesthetic celebrates femininity and delicate romanticism.",
    "mood" => "Romantic • Vintage • Playful",
    "bg" => "../quizzes/assets/coquette_bg.jpg",
    "side1" => "../quizzes/assets/coquette2.jpg",
    "side2" => "../quizzes/assets/coquette12.jpg",
    "color" => "#f5c4d4",
    "music" => "../music/coquette.mp3"
  ],
  "grunge" => [
    "title" => "The Rebel Soul",
    "desc" => "You thrive in expressive chaos — distressed textures, dark tones, and a bold disregard for convention define your edge.",
    "history" => "Born from 90s alternative rock culture, Grunge fashion embodies rebellion, comfort, and raw individuality.",
    "mood" => "Edgy • Unfiltered • Rebellious",
    "bg" => "../quizzes/assets/grunge_bg2.jpg",
    "side1" => "../quizzes/assets/grunge2.jpg",
    "side2" => "../quizzes/assets/grunge3.jpg",
    "color" => "#a1a1a1ff",
    "music" => "../music/grunge.mp3"
  ],
  "punk" => [
    "title" => "The Anarchic Icon",
    "desc" => "You wear rebellion like armor — unapologetic, loud, and endlessly cool. Spikes, leather, and confidence are your essentials.",
    "history" => "Emerging from 1970s UK subcultures, punk aesthetic rejected mainstream norms through fashion, music, and activism.",
    "mood" => "Defiant • Raw • Bold",
    "bg" => "../quizzes/assets/punk_bg1.jpg",
    "side1" => "../quizzes/assets/punk1.jpg",
    "side2" => "../quizzes/assets/punk3.jpg",
    "color" => "#ff0000ff",
    "music" => "../music/punk.mp3"
  ],
  "y2k" => [
    "title" => "The Futuristic Popstar",
    "desc" => "You radiate playful confidence with metallics, baby tees, and digital-era nostalgia. The early 2000s live in your sparkle.",
    "history" => "A revival of late 90s and early 2000s cyber fashion — glossy textures, technology-inspired motifs, and glittery confidence.",
    "mood" => "Playful • Futuristic • Glam",
    "bg" => "../quizzes/assets/y2k_bg2.jpg",
    "side1" => "../quizzes/assets/y2k1.jpg",
    "side2" => "../quizzes/assets/y2k3.jpg",
    "color" => "#d46be3",
    "music" => "../music/y2k.mp3"
  ],
  "luxurious" => [
    "title" => "The Luxe Visionary",
    "desc" => "Glitter, confidence, and high drama — your aura screams sophistication and luxury. You turn moments into red-carpet statements.",
    "history" => "Rooted in Old Hollywood and modern couture, Glam embraces opulence, allure, and timeless beauty.",
    "mood" => "Elegant • Dazzling • Confident",
    "bg" => "../quizzes/assets/glam_bg1.jpg",
    "side1" => "../quizzes/assets/glam2.jpg",
    "side2" => "../quizzes/assets/glam1.jpg",
    "color" => "#c93939ff",
    "music" => "../music/glam.mp3"
  ]
];

// Fallback 
if (!array_key_exists($aesthetic, $aesthetics)) $aesthetic = "boho";
$data = $aesthetics[$aesthetic];
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Your Aesthetic Result | CelestiCare</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body {
  margin: 0;
  font-family: 'Poppins', sans-serif;
  background: url('<?php echo $data['bg']; ?>') center/cover no-repeat fixed;
  color: white;
  overflow-x: hidden;
}

.result-overlay {
  background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0,0,0,0.8));
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 60px 20px;
  position: relative;
  overflow: hidden;
}

/* (magazine layout) */
.side-image {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 220px;
  height: 320px;
  border-radius: 18px;
  object-fit: cover;
  box-shadow: 0 8px 30px rgba(0,0,0,0.4);
  opacity: 0.9;
  transition: all 0.4s ease;
}
.side-image:hover {
  transform: translateY(-50%) scale(1.05);
  opacity: 1;
}

.side-left { left: 60px; }
.side-right { right: 60px; }

/* Main */
.result-card {
  background: rgba(255,255,255,0.15);
  backdrop-filter: blur(20px);
  border-radius: 30px;
  padding: 60px;
  max-width: 850px;
  width: 100%;
  color: #fdfdfd;
  box-shadow: 0 10px 50px rgba(0,0,0,0.6);
  text-align: left;
  position: relative;
  z-index: 2;
}

.result-card::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border: 1.5px solid rgba(255,255,255,0.25);
  border-radius: 30px;
  pointer-events: none;
}

h1 {
  font-size: 2.8rem;
  font-weight: 700;
  color: <?php echo $data['color']; ?>;
  text-transform: uppercase;
  letter-spacing: 2px;
  margin-bottom: 20px;
}

.section-label {
  text-transform: uppercase;
  font-weight: 600;
  font-size: 0.9rem;
  color: rgba(255,255,255,0.7);
  letter-spacing: 1.5px;
  margin-top: 30px;
}

p {
  font-size: 1.05rem;
  color: #f0f0f0;
  line-height: 1.8;
}

.mood {
  font-style: italic;
  font-weight: 500;
  font-size: 1.1rem;
  color: <?php echo $data['color']; ?>;
}

.btn-dashboard {
  background-color: <?php echo $data['color']; ?>;
  color: #fff;
  border: none;
  border-radius: 35px;
  padding: 14px 45px;
  font-weight: 600;
  margin-top: 40px;
  transition: all 0.3s ease;
}
.btn-dashboard:hover {
  transform: scale(1.07);
  background-color: #fff;
  color: <?php echo $data['color']; ?>;
}

/* Music  Button */
.music-control {
  position: fixed;
  top: 105px;
  right: 20px;
  z-index: 1050;
  background: rgba(255,255,255,0.2);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255,255,255,0.3);
  border-radius: 50%;
  width: 50px;
  height: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  color: white;
  font-size: 1.2rem;
}

.music-control:hover {
  background: rgba(255,255,255,0.3);
  transform: scale(1.1);
}

.music-control.muted {
  background: rgba(255,255,255,0.1);
  color: #ccc;
}


/* Mobile responsive styles for audio button */
@media (max-width: 991px) {
  .music-control {
    top: 90px;
    right: 15px;
    width: 45px;
    height: 45px;
    font-size: 1.1rem;
  }
}

html::-webkit-scrollbar, body::-webkit-scrollbar {
  width: 0 !important;
  height: 0 !important;
  background: transparent;
}
</style>
</head>

<body>
<!-- Music Player -->
<audio id="backgroundMusic" autoplay loop>
    <source src="<?php echo $data['music']; ?>" type="audio/mpeg">
    <source src="<?php echo str_replace('.mp3', '.ogg', $data['music']); ?>" type="audio/ogg">
    Your browser does not support the audio element.
</audio>

<!-- Music Button -->
<div id="musicControl" class="music-control" title="Click to mute/unmute">
    🔈
</div>

<div class="result-overlay">
  <img src="<?php echo $data['side1']; ?>" alt="Aesthetic Side 1" class="side-image side-left">
  <img src="<?php echo $data['side2']; ?>" alt="Aesthetic Side 2" class="side-image side-right">

  <div class="result-card">
    <h1><?php echo $data['title']; ?></h1>

    <div class="section-label">About</div>
    <p><?php echo $data['desc']; ?></p>

    <div class="section-label">History</div>
    <p><?php echo $data['history']; ?></p>

    <div class="section-label">Mood</div>
    <p class="mood"><?php echo $data['mood']; ?></p>

    <div class="d-flex justify-content-center gap-3 mt-4">
      <a href="../dashboard/index.php" class="btn btn-dashboard">Back to Dashboard</a>
      <a href="aesthetic_quiz.php" class="btn btn-dashboard">Retake Quiz</a>
    </div>
  </div>
</div>

<script>
// Music control functionality
const music = document.getElementById('backgroundMusic');
const musicControl = document.getElementById('musicControl');

music.volume = 0.1;

window.addEventListener('load', function() {
    music.play().catch(error => {
        console.log('Autoplay prevented:', error);
       
        musicControl.innerHTML = '▶️';
        musicControl.title = 'Click to play music (autoplay was blocked)';
    });
});


musicControl.addEventListener('click', function() {
    if (music.paused) {

        music.play().then(() => {
            music.muted = false;
            musicControl.innerHTML = '🔈';
            musicControl.classList.remove('muted');
            musicControl.title = 'Click to mute';
        }).catch(error => {
            console.log('Play failed:', error);
        });
    } else {

        music.muted = !music.muted;
        if (music.muted) {
            musicControl.innerHTML = '🔇';
            musicControl.classList.add('muted');
            musicControl.title = 'Click to unmute';
        } else {
            musicControl.innerHTML = '🔈';
            musicControl.classList.remove('muted');
            musicControl.title = 'Click to mute';
        }
    }
});


music.addEventListener('loadeddata', function() {
    if (music.muted) {
        musicControl.innerHTML = '🔇';
        musicControl.classList.add('muted');
    } else {
        musicControl.innerHTML = '🔈';
        musicControl.classList.remove('muted');
    }
});


music.addEventListener('pause', function() {
    if (music.currentTime === 0) {

        musicControl.innerHTML = '▶️';
        musicControl.title = 'Click to play music';
    }
});
</script>
</body>
</html>