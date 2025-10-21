<?php
session_start();
include("../includes/navbar.php");

$ELEMENTS = ['fire','water','air','earth'];
$ELEMENT_LABEL = [
  'fire'  => 'Fire Signs',
  'water' => 'Water Signs',
  'air'   => 'Air Signs',
  'earth' => 'Earth Signs'
];
$ELEMENT_SIGNS = [
  'fire'  => ['aries','leo','sagittarius'],
  'water' => ['cancer','scorpio','pisces'],
  'air'   => ['gemini','libra','aquarius'],
  'earth' => ['taurus','virgo','capricorn'],
];
$SIGN_DATA = [
  'aries' => [
    'name' => 'Aries','element' => 'fire','tagline' => 'The Fiery','dates' => 'March 21 – April 19',
    'traits' => 'Bold, dynamic, and unafraid to take charge. Aries ignites movement wherever they go, driven by pure enthusiasm. Passion and courage define their leadership style.',
    'element_desc' => 'Energy, vitality, and initiative.','planet' => 'Mars','planet_desc' => 'The planet of action and strength.',
    'bg' => 'fire_bg.jpeg'
  ],
  'leo' => [
    'name' => 'Leo','element' => 'fire','tagline' => 'The Blazing Feline','dates' => 'July 23 – August 22',
    'traits' => 'Charismatic, confident, and warm-hearted. Leo lives to express and uplift, bringing light to every space they enter. Their generosity and creativity make them unforgettable.',
    'element_desc' => 'Passion, self-expression, and pride.','planet' => 'Sun','planet_desc' => 'The ruler of vitality and purpose.',
    'bg' => 'fire_bg.jpeg'
  ],
  'sagittarius' => [
    'name' => 'Sagittarius','element' => 'fire','tagline' => 'The Adventurous','dates' => 'November 22 – December 21',
    'traits' => 'Adventurous, optimistic, and endlessly curious. Sagittarius seeks meaning through exploration, learning, and laughter. Their joy inspires others to look beyond limits.',
    'element_desc' => 'Growth, vision, and enthusiasm.','planet' => 'Jupiter','planet_desc' => 'The planet of expansion and wisdom.',
    'bg' => 'fire_bg.jpeg'
  ],
  'taurus' => [
    'name' => 'Taurus','element' => 'earth','tagline' => 'The Grounded','dates' => 'April 20 – May 20',
    'traits' => 'Loyal, sensual, and steadfast. Taurus finds peace in beauty and stability, creating comfort that lasts. Their calm nature anchors everyone around them.',
    'element_desc' => 'Practicality, security, and endurance.','planet' => 'Venus','planet_desc' => 'The planet of love and pleasure.',
    'bg' => 'earth_bg.jpeg'
  ],
  'virgo' => [
    'name' => 'Virgo','element' => 'earth','tagline' => 'The Maiden','dates' => 'August 23 – September 22',
    'traits' => 'Intelligent, detail-oriented, and quietly powerful. Virgo refines and improves everything they touch. Their thoughtful nature brings structure to chaos with grace.',
    'element_desc' => 'Precision, purpose, and balance.','planet' => 'Mercury','planet_desc' => 'The planet of logic and service.',
    'bg' => 'earth_bg.jpeg'
  ],
  'capricorn' => [
    'name' => 'Capricorn','element' => 'earth','tagline' => 'The Ambitious','dates' => 'December 22 – January 19',
    'traits' => 'Ambitious, responsible, and disciplined. Capricorn climbs steadily toward mastery, blending wisdom with willpower. They are builders of both success and legacy.',
    'element_desc' => 'Stability, focus, and determination.','planet' => 'Saturn','planet_desc' => 'The ruler of time, structure, and perseverance.',
    'bg' => 'earth_bg.jpeg'
  ],
  'gemini' => [
    'name' => 'Gemini','element' => 'air','tagline' => 'The Versatile','dates' => 'May 21 – June 20',
    'traits' => 'Quick-witted, adaptable, and endlessly curious. Gemini thrives on movement and conversation, forever gathering stories and insights. Their lively presence keeps energy flowing.',
    'element_desc' => 'Intellect, communication, and change.','planet' => 'Mercury','planet_desc' => 'The planet of thought and expression.',
    'bg' => 'air_bg.jpeg'
  ],
  'libra' => [
    'name' => 'Libra','element' => 'air','tagline' => 'The Harmonizer','dates' => 'September 23 – October 22',
    'traits' => 'Graceful, charming, and fair. Libra seeks equilibrium in beauty and relationships. They bring peace through empathy and elegant compromise.',
    'element_desc' => 'Balance, elegance, and awareness.','planet' => 'Venus','planet_desc' => 'Ruler of love, art, and diplomacy.',
    'bg' => 'air_bg.jpeg'
  ],
  'aquarius' => [
    'name' => 'Aquarius','element' => 'air','tagline' => 'The Innovator','dates' => 'January 20 – February 18',
    'traits' => 'Innovative, independent, and forward-thinking. Aquarius lives for originality and ideas that serve humanity. Their perspective is futuristic, yet deeply humanitarian.',
    'element_desc' => 'Intellect, innovation, and freedom.','planet' => 'Uranus','planet_desc' => 'Planet of progress and change (traditionally Saturn).',
    'bg' => 'air_bg.jpeg'
  ],
  'cancer' => [
    'name' => 'Cancer','element' => 'water','tagline' => 'The Nurturer','dates' => 'June 21 – July 22',
    'traits' => 'Sensitive, protective, and nurturing. Cancer builds emotional safety for themselves and their loved ones. They love through care, creating homes filled with warmth.',
    'element_desc' => 'Emotion, intuition, and protection.','planet' => 'Moon','planet_desc' => 'Ruler of feeling, memory, and instinct.',
    'bg' => 'water_bg.jpeg'
  ],
  'scorpio' => [
    'name' => 'Scorpio','element' => 'water','tagline' => 'The Intense','dates' => 'October 23 – November 21',
    'traits' => 'Mysterious, passionate, and deeply loyal. Scorpio transforms pain into power and truth into intimacy. They live and love with unshakable intensity.',
    'element_desc' => 'Depth, passion, and renewal.','planet' => 'Pluto','planet_desc' => 'Ruler of transformation and desire (traditionally Mars).',
    'bg' => 'water_bg.jpeg'
  ],
  'pisces' => [
    'name' => 'Pisces','element' => 'water','tagline' => 'The Whymsical','dates' => 'February 19 – March 20',
    'traits' => 'Empathetic, creative, and soulful. Pisces channels emotion into imagination, seeing beauty where others see chaos. They live through intuition and compassion.',
    'element_desc' => 'Sensitivity, art, and spirituality.','planet' => 'Neptune','planet_desc' => 'Planet of dreams and inspiration (traditionally Jupiter).',
    'bg' => 'water_bg.jpeg'
  ],
];

// Element background mapping
$ELEMENT_BG = [
  'fire' => 'fire_bg.jpeg',
  'water' => 'water_bg.jpeg',
  'air' => 'air_bg.jpeg',
  'earth' => 'earth_bg.jpeg'
];

// ---------- HELPERS ----------
function h($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function prevElem($elem, $order){ $i=array_search($elem,$order); return $order[($i-1+count($order))%count($order)]; }
function nextElem($elem, $order){ $i=array_search($elem,$order); return $order[($i+1)%count($order)]; }

// ---------- ROUTING ----------
$view = $_GET['view'] ?? 'element';
$elem = strtolower($_GET['elem'] ?? 'fire');
$elem = in_array($elem,$ELEMENTS) ? $elem : 'fire';

if ($view === 'sign') {
  $slug = strtolower($_GET['sign'] ?? '');
  if (!isset($SIGN_DATA[$slug])) { header("Location: ?view=element&elem=fire"); exit(); }
  $sign = $SIGN_DATA[$slug];
  $bodyClass = $sign['element'];
  $backgroundImage = $sign['bg'];
} else {
  $bodyClass = $elem;
  $backgroundImage = $ELEMENT_BG[$elem];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CelestiCare<?php
    if($view==='element') echo ' - '.h($ELEMENT_LABEL[$elem]);
    if($view==='sign')    echo ' - '.h($sign['name']);
  ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    /* ===== Global ===== */
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: url('<?php echo $backgroundImage; ?>') center/cover no-repeat fixed;
      color: white;
      overflow-x: hidden;
      min-height: 100vh;
    }

    /* ===== Overlay ===== */
    .page-overlay {
      background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.34));
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* ===== Main Content ===== */
    main {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 60px 20px;
      position: relative;
    }

    /* ===== Headers ===== */
    h2 {
      font-size: 2.8rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 2px;
      margin-bottom: 40px;
      text-align: center;
      color: white;
    }

    /* ===== Diamonds ===== */
    .diamond-container {
      display: flex;
      gap: 80px;
      justify-content: center;
      flex-wrap: wrap;
      margin: 40px 0;
    }

    .diamond-wrapper {
      position: relative;
      display: inline-block;
      text-align: center;
    }

    .diamond {
      width: 160px;
      height: auto;
      transition: transform .3s ease;
      cursor: pointer;
    }

    .diamond:hover {
      transform: scale(1.1);
    }

    .sign-name {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      color: #000;
      font-weight: 700;
      font-size: 18px;
      text-align: center;
      pointer-events: none;
      opacity: 0;
      transition: opacity .3s ease;
    }

    .diamond-wrapper:hover .sign-name {
      opacity: 1;
    }

    /* ===== Arrows ===== */
    .arrow {
      position: absolute;
      top: 50%;
      font-size: 48px;
      font-weight: bold;
      cursor: pointer;
      user-select: none;
      color: white;
      transition: transform .3s;
      background: rgba(255,255,255,0.2);
      backdrop-filter: blur(10px);
      border-radius: 50%;
      width: 60px;
      height: 60px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .arrow.left { left: 60px; transform: translateY(-50%); }
    .arrow.right { right: 60px; transform: translateY(-50%); }
    .arrow:hover { transform: translateY(-50%) scale(1.2); }

    /* ===== Sign Detail Page ===== */
    .sign-page {
      min-height: calc(100vh - 80px);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 20px;
    }

    .sign-card {
      background: rgba(99, 99, 99, 0.36);
      backdrop-filter: blur(20px);
      border-radius: 30px;
      padding: 60px;
      max-width: 900px;
      width: 100%;
      color: #fdfdfd;
      box-shadow: 0 10px 50px rgba(0, 0, 0, 0.55);
      text-align: left;
      position: relative;
    }

    .sign-card::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      border: 1.5px solid rgba(99, 99, 99, 0.54);
      border-radius: 30px;
      pointer-events: none;
    }

    .sign-title {
      font-size: 72px;
      line-height: 1.02;
      margin: 8px 0 8px;
      font-weight: 800;
    }

    .sign-tagline {
      font-size: 24px;
      font-weight: 700;
      margin: 0 0 12px;
    }

    .sign-dates {
      font-size: 19px;
      color: rgba(255,255,255,0.8);
      margin: 0 0 32px;
    }

    .sign-section {
      margin: 28px 0 36px;
    }

    .sign-section h3 {
      margin: 0 0 12px;
      font-size: 32px;
    }

    .sign-section p {
      margin: 0;
      line-height: 1.75;
      font-size: 18px;
      color: #f0f0f0;
    }

    /* ===== Buttons (from your reference code) ===== */
    .sign-actions {
      display: flex;
      align-items: center;
      gap: 18px;
      margin-top: 40px;
    }

    .cta {
      display: inline-block;
      padding: 12px 20px;
      background: #111;
      color: #fff;
      border-radius: 24px;
      font-weight: 700;
      text-decoration: none;
      transition: transform .12s ease, opacity .12s ease;
    }

    .cta:hover {
      transform: translateY(-1px);
      opacity: .9;
      color: #fff;
    }

    /* ===== Hide Scrollbars ===== */
    html::-webkit-scrollbar,
    body::-webkit-scrollbar {
      width: 0 !important;
      height: 0 !important;
      background: transparent;
    }

    /* ===== Fade ===== */
    body.fade-out { opacity: 0; transition: opacity 0.4s ease; }
  </style>
</head>
<body class="<?php echo h($bodyClass); ?>">
<div class="page-overlay">

  <?php if ($view === 'sign'): ?>
    <!-- ========== SIGN DETAIL VIEW ========== -->
    <main class="sign-page">
      <div class="sign-card">
        <h1 class="sign-title"><?php echo h($sign['name']); ?></h1>
        <div class="sign-tagline"><?php echo h($sign['tagline']); ?></div>
        <div class="sign-dates"><?php echo h($sign['dates']); ?></div>

        <section class="sign-section">
          <h3>Core Traits</h3>
          <p><?php echo h($sign['traits']); ?></p>
        </section>

        <section class="sign-section">
          <h3>Element</h3>
          <p><strong><?php echo h(ucfirst($sign['element'])); ?></strong> — <?php echo h($sign['element_desc']); ?></p>
        </section>

        <section class="sign-section">
          <h3>Planet</h3>
          <p><strong><?php echo h($sign['planet']); ?></strong> — <?php echo h($sign['planet_desc']); ?></p>
        </section>

        <div class="sign-actions">
          <a class="cta" href="?view=element&elem=<?php echo h($sign['element']); ?>">Back to <?php echo h(ucfirst($sign['element'])); ?> signs</a>
          <a class="cta" href="?view=element&elem=fire">All Elements</a>
        </div>
      </div>
    </main>

  <?php else: ?>
    <!-- ========== ELEMENT GRID VIEW ========== -->
    <?php
      $prev = prevElem($elem, $ELEMENTS);
      $next = nextElem($elem, $ELEMENTS);
      $signs = $ELEMENT_SIGNS[$elem];
    ?>
    <main>
      <h2><?php echo h($ELEMENT_LABEL[$elem]); ?></h2>
      
      <div class="arrow left" onclick="goElem('<?php echo h($prev); ?>')">←</div>

      <div class="diamond-container">
        <?php foreach ($signs as $slug): ?>
          <?php $sd = $SIGN_DATA[$slug]; ?>
          <div class="diamond-wrapper">
            <a href="?view=sign&sign=<?php echo h($slug); ?>">
              <img src="vector.png" alt="<?php echo h($sd['name']); ?>" class="diamond">
              <span class="sign-name"><?php echo h($sd['name']); ?></span>
            </a>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="arrow right" onclick="goElem('<?php echo h($next); ?>')">→</div>
    </main>
  <?php endif; ?>

</div>

<script>
  function goElem(elem){
    document.body.classList.add("fade-out");
    setTimeout(function(){ window.location.href='?view=element&elem='+encodeURIComponent(elem); }, 400);
  }
</script>
</body>
</html>