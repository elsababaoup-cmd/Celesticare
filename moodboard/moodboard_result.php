<?php
session_start();
include("../includes/navbar.php");
include("../config/dbconfig.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's complete profile data
$stmt = $conn->prepare("SELECT aesthetic_result, style_result, zodiac_sign, undertone, season, gender, birthdate FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("⚠️ No user found with ID: $user_id");
}

$aesthetic_result = trim($user['aesthetic_result'] ?? '');
$style_result = trim($user['style_result'] ?? '');

// Redirect if quizzes not completed
if (empty($aesthetic_result)) {
    header("Location: ../quizzes/aesthetic_welcome.php");
    exit();
}

if (empty($style_result)) {
    header("Location: ../quizzes/style_welcome.php");
    exit();
}

// Fetch user's latest outfit
$outfit_stmt = $conn->prepare("SELECT outfit_data FROM user_outfits WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$outfit_stmt->bind_param("i", $user_id);
$outfit_stmt->execute();
$outfit_result = $outfit_stmt->get_result();
$outfit_data = $outfit_result->fetch_assoc();
$outfit_stmt->close();

$outfit_items = [];
if ($outfit_data && $outfit_data['outfit_data']) {
    $outfit_items = json_decode($outfit_data['outfit_data'], true);
}

// Aesthetic definitions with all assets
$aesthetics = [
    "academia" => [
        "color" => "#271a0eff", 
        "mood" => "Intellectual • Vintage • Poised",
        "title" => "The Scholar",
        "desc" => "You're drawn to timeless sophistication — think books, tweed, and candlelit study sessions. You find beauty in intellect and classic detail.",
        "assets" => [
            "../quizzes/assets/acad1.jpg",
            "../quizzes/assets/acad2.jpg", 
            "../quizzes/assets/acad3.jpg"
        ]
    ],
    "boho" => [
        "color" => "#e38153ff", 
        "mood" => "Free-Spirited • Earthy • Artistic",
        "title" => "The Bohemian Dreamer",
        "desc" => "You embody freedom, creativity, and a love for earthy tones and textures. Every outfit tells a story — unbothered, soulful, and effortlessly chic.",
        "assets" => [
            "../quizzes/assets/boho1.jpg",
            "../quizzes/assets/boho2.jpg",
            "../quizzes/assets/boho3.jpg"
        ]
    ],
    "coquette" => [
        "color" => "#f5c4d4", 
        "mood" => "Romantic • Vintage • Playful",
        "title" => "The Coquette Muse",
        "desc" => "Romantic and soft, your style leans into vintage elegance — ribbons, lace, and an air of flirtatious nostalgia.",
        "assets" => [
            "../quizzes/assets/coquette12.jpg",
            "../quizzes/assets/coquette2.jpg",
            "../quizzes/assets/coquette3.jpg"
        ]
    ],
    "grunge" => [
        "color" => "#a1a1a1ff", 
        "mood" => "Edgy • Unfiltered • Rebellious",
        "title" => "The Rebel Soul",
        "desc" => "You thrive in expressive chaos — distressed textures, dark tones, and a bold disregard for convention define your edge.",
        "assets" => [
            "../quizzes/assets/grunge1.jpg",
            "../quizzes/assets/grunge2.jpg",
            "../quizzes/assets/grunge3.jpg"
        ]
    ],
    "punk" => [
        "color" => "#ff0000ff", 
        "mood" => "Defiant • Raw • Bold",
        "title" => "The Anarchic Icon",
        "desc" => "You wear rebellion like armor — unapologetic, loud, and endlessly cool. Spikes, leather, and confidence are your essentials.",
        "assets" => [
            "../quizzes/assets/punk1.jpg",
            "../quizzes/assets/punk2.jpg",
            "../quizzes/assets/punk3.jpg"
        ]
    ],
    "y2k" => [
        "color" => "#d46be3", 
        "mood" => "Playful • Futuristic • Glam",
        "title" => "The Futuristic Popstar",
        "desc" => "You radiate playful confidence with metallics, baby tees, and digital-era nostalgia. The early 2000s live in your sparkle.",
        "assets" => [
            "../quizzes/assets/y2k1.jpg",
            "../quizzes/assets/y2k2.jpg",
            "../quizzes/assets/y2k3.jpg"
        ]
    ],
    "luxurious" => [
        "color" => "#c93939ff", 
        "mood" => "Elegant • Dazzling • Confident",
        "title" => "The Luxe Visionary",
        "desc" => "Glitter, confidence, and high drama — your aura screams sophistication and luxury. You turn moments into red-carpet statements.",
        "assets" => [
            "../quizzes/assets/glam1.jpg",
            "../quizzes/assets/glam2.jpg",
            "../quizzes/assets/glam3.jpg"
        ]
    ]
];

// Style definitions
$styles = [
    'minimalist' => [
        'name' => 'Minimalist Elegance', 
        'colors' => 'White, black, beige, grey',
        'desc' => 'Clean, simple, and timeless pieces. Less is more and comfort meets elegance.'
    ],
    'businesswear' => [
        'name' => 'Professional Businesswear', 
        'colors' => 'Navy, black, grey, white',
        'desc' => 'Polished, professional, and structured looks perfect for work and meetings.'
    ],
    'elegant' => [
        'name' => 'Classic Elegance', 
        'colors' => 'Pastels, jewel tones, black, white',
        'desc' => 'Sophisticated, classy, and luxurious styles with refined silhouettes.'
    ],
    'creative' => [
        'name' => 'Creative Expression', 
        'colors' => 'Bright, contrasting, eclectic',
        'desc' => 'Bold, artistic, and expressive looks that show your personality.'
    ],
    'soft' => [
        'name' => 'Soft Elegance', 
        'colors' => 'Pastels, cream, light pink, baby blue',
        'desc' => 'Gentle, pastel, and cozy pieces that create a soft, approachable vibe.'
    ],
    'rough' => [
        'name' => 'Rough Edge', 
        'colors' => 'Black, grey, earthy tones',
        'desc' => 'Edgy, casual, and street-inspired styles that stand out with attitude.'
    ],
    'streetwear' => [
        'name' => 'Urban Streetwear', 
        'colors' => 'Black, white, bold bright accents',
        'desc' => 'Urban, trendy, and casual styles that combine comfort with flair.'
    ]
];

// Your actual zodiac color palettes from undertone result
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
        "warm" => ["#52201c","#415715","#fa7e1e","#Fe7735aF9F1C","#aac265","#edba4a"],
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

// Astro facts
$astroFacts = [
    "Aries" => "You are bold, ambitious, and a natural leader.",
    "Taurus" => "You value comfort and beauty in all things.",
    "Gemini" => "Your curiosity and wit make you adaptable.",
    "Cancer" => "You are intuitive and deeply caring.",
    "Leo" => "Your creativity shines brightly.",
    "Virgo" => "You find harmony in order and detail.",
    "Libra" => "Balance and beauty guide your choices.",
    "Scorpio" => "You're magnetic and passionate.",
    "Sagittarius" => "Your adventurous heart inspires others.",
    "Capricorn" => "You are grounded and ambitious.",
    "Aquarius" => "You think differently and value authenticity.",
    "Pisces" => "Your creativity and empathy define you."
];

// Zodiac symbol assets
$zodiacSymbols = [
    "Aries" => "./assets/aries_circle.png",
    "Taurus" => "./assets/taurus_circle.png",
    "Gemini" => "./assets/gemini_circle.png",
    "Cancer" => "./assets/cancer_circle.png",
    "Leo" => "./assets/leo_circle.png",
    "Virgo" => "./assets/virgo_circle.png",
    "Libra" => "./assets/libra_circle.png",
    "Scorpio" => "./assets/scorpio_circle.png",
    "Sagittarius" => "./assets/sag_circle.png",
    "Capricorn" => "./assets/capricorn_circle.png",
    "Aquarius" => "./assets/aqua_circle.png",
    "Pisces" => "./assets/pisces_circle.png"
];
// Get the zodiac symbol image path
$zodiac_symbol = $zodiacSymbols[$user['zodiac_sign']] ?? "../quizzes/assets/zodiac/default.png";

$aesthetic_data = $aesthetics[$aesthetic_result] ?? $aesthetics['boho'];
$style_data = $styles[$style_result] ?? ['name' => 'Personal Style', 'colors' => 'Your unique palette', 'desc' => 'Your unique fashion sense'];
$astro_fact = $astroFacts[$user['zodiac_sign']] ?? "Your zodiac influences your unique style expression";

// Get the actual color palette for this user
$user_palette = $palettes[$user['zodiac_sign']][strtolower($user['undertone'])] ?? ["#8B5FBF", "#6D28D9", "#C084FC", "#495482", "#9b83d3", "#F8FAFC"];

$base_path = "/Celesticare/undertone/assets/";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Editorial Moodboard | CelestiCare</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;800&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary-purple: #9177c0ff;
      --primary-dark: #594082ff;
      --accent: #C084FC;
      --light-blue: #495482;
      --lavender: #9b83d3;
      --cream: #F8FAFC;
      --dark: #16222eff;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, var(--light-blue) 0%, var(--lavender) 100%);
      color: #333;
      overflow-x: hidden;
      min-height: 100vh;
    }

    .editorial-container {
      max-width: 1400px;
      margin: 2rem auto;
      background: rgba(255, 255, 255, 0.95);
      border-radius: 25px;
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
      overflow: hidden;
    }

    /* Magazine Header */
    .magazine-header {
      background: linear-gradient(135deg, var(--primary-purple), var(--primary-dark));
      color: white;
      padding: 3rem 2rem;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .magazine-header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" opacity="0.1"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    }

    .magazine-title {
      font-family: 'Playfair Display', serif;
      font-size: 4rem;
      font-weight: 800;
      margin-bottom: 0.5rem;
      text-transform: uppercase;
      letter-spacing: 3px;
    }

    .magazine-subtitle {
      font-size: 1.2rem;
      font-weight: 300;
      letter-spacing: 8px;
      text-transform: uppercase;
      opacity: 0.9;
    }

    /* Main Content Grid */
    .main-content {
      display: grid;
      grid-template-columns: 1.2fr 0.8fr;
      gap: 0;
      min-height: 700px;
    }

    /* Left Panel - Aesthetic & Style */
    .left-panel {
      padding: 3rem;
      background: linear-gradient(135deg, #faf8fcff, #f1f5f9);
      display: flex;
      flex-direction: column;
      gap: 2rem;
    }

    /* Right Panel - Color & Zodiac */
    .right-panel {
      padding: 3rem;
      background: linear-gradient(135deg, #f8fafc, #f1f5f9);
      display: flex;
      flex-direction: column;
      gap: 2rem;
    }

    /* Section Styles */
    .section-card {
      background: white;
      border-radius: 15px;
      padding: 2rem;
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
      border-left: 4px solid var(--primary-purple);
    }

    .section-title {
      font-family: 'Playfair Display', serif;
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--dark);
      margin-bottom: 1.5rem;
      border-bottom: 2px solid var(--accent);
      padding-bottom: 0.5rem;
    }

    /* Aesthetic Display */
    .aesthetic-display {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1.5rem;
      margin-bottom: 1.5rem;
    }

    .aesthetic-image {
      width: 100%;
      height: 200px;
      object-fit: cover;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
      transition: transform 0.3s ease;
    }

    .aesthetic-image:hover {
      transform: scale(1.05);
    }

    .aesthetic-info {
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .aesthetic-tag {
      display: inline-block;
      background: <?= $aesthetic_data['color'] ?>;
      color: white;
      padding: 0.6rem 1.5rem;
      border-radius: 25px;
      font-size: 1rem;
      font-weight: 600;
      margin-bottom: 1rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .aesthetic-title {
      font-family: 'Playfair Display', serif;
      font-size: 2rem;
      font-weight: 700;
      color: var(--dark);
      margin-bottom: 1rem;
    }

    .aesthetic-mood {
      font-size: 1.1rem;
      color: <?= $aesthetic_data['color'] ?>;
      font-weight: 500;
      margin-bottom: 1rem;
      font-style: italic;
    }

    .aesthetic-desc {
      color: #666;
      line-height: 1.6;
      font-size: 0.95rem;
    }

    /* Style Section */
    .style-tag {
      display: inline-block;
      background: linear-gradient(135deg, var(--primary-purple), var(--primary-dark));
      color: white;
      padding: 0.6rem 1.5rem;
      border-radius: 25px;
      font-size: 1rem;
      font-weight: 600;
      margin-bottom: 1rem;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    /* Color Analysis */
    .color-wheel-container {
      text-align: center;
      margin: 1.5rem 0;
    }

    .color-wheel {
      max-width: 200px;
      border-radius: 50%;
      border: 4px solid white;
      box-shadow: 0 8px 25px rgba(0,0,0,0.2);
      margin: 0 auto;
      transition: transform 0.3s ease;
      cursor: pointer;
    }

    .color-wheel:hover {
      transform: scale(1.05);
    }

    .color-palette {
      display: flex;
      justify-content: center;
      gap: 0.8rem;
      margin: 1.5rem 0;
      flex-wrap: wrap;
    }

    .color-swatch {
      width: 40px;
      height: 40px;
      border-radius: 8px;
      border: 2px solid white;
      box-shadow: 0 3px 8px rgba(0,0,0,0.2);
      transition: transform 0.3s ease;
    }

    .color-swatch:hover {
      transform: scale(1.2);
    }

    /* Zodiac Section */
    .zodiac-display {
      text-align: center;
      padding: 1.5rem;
      background: linear-gradient(135deg, var(--cream), #f3e8ff);
      border-radius: 12px;
      margin: 1rem 0;
    }

    .zodiac-icon {
      font-size: 3rem;
      margin-bottom: 1rem;
    }

    .zodiac-fact {
      font-style: italic;
      color: #666;
      margin-top: 1rem;
    }

    /* Outfit Section */
    .outfit-section {
      padding: 3rem;
      background: linear-gradient(135deg, #f8fafc, #f1f5f9);
      border-top: 3px solid var(--accent);
    }

    .outfit-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 1.5rem;
      margin-top: 1.5rem;
    }

    .outfit-item {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.15);
      transition: all 0.3s ease;
    }

    .outfit-item:hover {
      transform: scale(1.08);
      box-shadow: 0 12px 30px rgba(0,0,0,0.25);
    }

    /* Action Buttons */
    .action-buttons {
      display: flex;
      gap: 1rem;
      margin-top: 2rem;
      flex-wrap: wrap;
    }

    .action-btn {
      padding: 1rem 2rem;
      border: none;
      border-radius: 50px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s ease;
      text-align: center;
      flex: 1;
      min-width: 160px;
    }

    .action-btn.primary {
      background: linear-gradient(135deg, var(--primary-purple), var(--primary-dark));
      color: white;
    }

    .action-btn.secondary {
      background: transparent;
      border: 2px solid var(--primary-purple);
      color: var(--primary-purple);
    }

    .action-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }

    /* Responsive */
    @media (max-width: 1024px) {
      .main-content {
        grid-template-columns: 1fr;
      }
      
      .aesthetic-display {
        grid-template-columns: 1fr;
      }
      
      .magazine-title {
        font-size: 2.5rem;
      }
    }

    @media (max-width: 768px) {
      .editorial-container {
        margin: 1rem;
      }
      
      .left-panel, .right-panel {
        padding: 2rem;
      }
      
      .action-buttons {
        flex-direction: column;
      }
      
      .action-btn {
        min-width: 100%;
      }
      
      .color-palette {
        gap: 0.5rem;
      }
      
      .color-swatch {
        width: 35px;
        height: 35px;
      }
    }

    /* Your Style Story Styles */
.style-story-content {
    text-align: center;
    padding: 1rem 0;
}

.style-story-text {
    font-size: 1.1rem;
    line-height: 1.6;
    color: #555;
    margin-bottom: 1.5rem;
    font-style: italic;
}

.style-story-highlights {
    display: flex;
    justify-content: center;
    gap: 2rem;
    flex-wrap: wrap;
    margin-top: 1.5rem;
}

.highlight-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
    color: var(--dark);
}

.highlight-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
}

/* Responsive adjustments for style story */
@media (max-width: 768px) {
    .style-story-highlights {
        flex-direction: column;
        gap: 1rem;
        align-items: center;
    }
    
    .style-story-text {
        font-size: 1rem;
    }
}

/* Refine Your Style Section - Full Width */
.refine-section {
    padding: 0 3rem 3rem 3rem;
    background: transparent;
}

.refine-card {
    background: linear-gradient(135deg, var(--primary-purple), var(--primary-dark));
    color: white;
    text-align: center;
    padding: 2rem;
    border-left: 4px solid var(--accent);
}

.refine-card .section-title {
    color: white;
    border-bottom: 2px solid rgba(255,255,255,0.3);
    margin-bottom: 1.5rem;
}

.refine-card .action-btn.primary {
    background: rgba(255,255,255,0.2);
    border: 2px solid rgba(255,255,255,0.3);
    color: white;
    backdrop-filter: blur(10px);
}

.refine-card .action-btn.secondary {
    background: transparent;
    border: 2px solid white;
    color: white;
}

.refine-card .action-btn:hover {
    background: rgba(255,255,255,0.3);
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.3);
}

/* Adjust main content to remove bottom padding since refine section is separate */
.main-content {
    display: grid;
    grid-template-columns: 1.2fr 0.8fr;
    gap: 0;
    min-height: auto;
    padding-bottom: 0; /* Remove bottom padding */
}


/* Zodiac Symbol Styles */
.zodiac-symbol {
    width: 110px;
    height: 110px;
    object-fit: contain;
    margin-bottom: 1rem;
    filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
    transition: transform 0.3s ease;
}

.zodiac-symbol:hover {
    transform: scale(1.1) rotate(5deg);
}

/* Remove the old zodiac icon styles */
.zodiac-icon {
    /* Remove font-size and margin-bottom from here */
    display: flex;
    justify-content: center;
    align-items: center;
}

html::-webkit-scrollbar, body::-webkit-scrollbar { width:0!important; height:0!important; background:transparent; }
  </style>
</head>
<body>
  <div class="editorial-container">
    <!-- Magazine Header -->
    <header class="magazine-header">
      <h1 class="magazine-title">CelestiCare</h1>
      <p class="magazine-subtitle">Your Personal Style Editorial MoodBoard</p>
    </header>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Left Panel - Aesthetic & Style -->
      <div class="left-panel">
        <!-- Aesthetic Section -->
        <div class="section-card">
          <h2 class="section-title">Aesthetic Signature</h2>
          <div class="aesthetic-display">
            <div>
              <?php foreach (array_slice($aesthetic_data['assets'], 0, 2) as $asset): ?>
                <img src="<?= htmlspecialchars($asset) ?>" alt="Aesthetic inspiration" class="aesthetic-image mb-2">
              <?php endforeach; ?>
            </div>
            <div class="aesthetic-info">
              <div class="aesthetic-tag">Aesthetic</div>
              <h3 class="aesthetic-title"><?= $aesthetic_data['title'] ?></h3>
              <div class="aesthetic-mood"><?= $aesthetic_data['mood'] ?></div>
              <p class="aesthetic-desc"><?= $aesthetic_data['desc'] ?></p>
            </div>
          </div>
        </div>

        <!-- Style Section -->
        <div class="section-card">
          <h2 class="section-title">Style DNA</h2>
          <div class="style-tag"><?= $style_data['name'] ?></div>
          <p class="aesthetic-desc"><?= $style_data['desc'] ?></p>
          <div class="mt-3">
            <strong>Recommended Colors:</strong>
            <div class="color-palette mt-2">
              <?php foreach($user_palette as $color): ?>
                <div class="color-swatch" style="background: <?= $color ?>" title="<?= $color ?>"></div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <!-- Outfit Collection -->
        <?php if (!empty($outfit_items)): ?>
        <div class="section-card">
          <h2 class="section-title">Your Curated Collection</h2>
          <div class="outfit-grid">
            <?php foreach($outfit_items as $item): ?>
              <img src="<?= htmlspecialchars($item['src']) ?>" 
                   alt="<?= htmlspecialchars($item['category'] ?? 'Fashion item') ?>" 
                   class="outfit-item"
                   title="<?= htmlspecialchars($item['category'] ?? 'Style item') ?>">
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>

      <!-- Right Panel - Color & Zodiac -->
      <div class="right-panel">
        <!-- Color Analysis -->
        <div class="section-card">
          <h2 class="section-title">Color Analysis</h2>
          <p><strong>Undertone:</strong> <?= $user['undertone'] ?? 'Not specified' ?></p>
          
          <?php if ($user['undertone'] && $user['undertone'] !== 'Not set'): ?>
            <div class="color-wheel-container">
              <?php
              $undertone_lower = strtolower($user['undertone']);
              $image_path = $base_path . $undertone_lower . "_wheel.png";
              $fallback_path = $base_path . "default_wheel.png";
              ?>
              <img src="<?= $image_path ?>" 
                   alt="<?= $user['undertone'] ?> color wheel" 
                   class="color-wheel"
                   onerror="this.src='<?= $fallback_path ?>'; this.onerror=null;">
            </div>
            
            <div class="color-palette">
              <?php foreach($user_palette as $color): ?>
                <div class="color-swatch" style="background: <?= $color ?>" title="<?= $color ?>"></div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-muted">Complete color analysis to see your personalized color wheel</p>
          <?php endif; ?>
        </div>

        <!-- Zodiac Influence -->
        <div class="section-card">
          <h2 class="section-title">Celestial Influence</h2>
          <div class="zodiac-display">
            <div class="zodiac-icon">
              <img src="<?= $zodiac_symbol ?>" 
                   alt="<?= $user['zodiac_sign'] ?? 'Zodiac' ?> symbol" 
                   class="zodiac-symbol"
                   onerror="this.src='../quizzes/assets/zodiac/default.png'">
            </div>
            <h4><?= $user['zodiac_sign'] ?? 'Your Zodiac' ?></h4>
            <p class="zodiac-fact"><?= $astro_fact ?></p>
          </div>
        </div>

        <!-- Your Style Story Section -->
        <div class="section-card">
            <h2 class="section-title">Your Style Story</h2>
            <div class="style-story-content">
                <p class="style-story-text">
                    A unique blend of <strong><?= $aesthetic_data['title'] ?></strong> aesthetic with 
                    <strong><?= $style_data['name'] ?></strong> sensibilities. 
                    <?php if ($user['zodiac_sign'] && $user['zodiac_sign'] !== 'Not set'): ?>
                    Your <strong><?= $user['zodiac_sign'] ?></strong> energy shines through in every curated piece, 
                    <?php endif; ?>
                    creating a signature look that's authentically you.
                </p>
                <div class="style-story-highlights">
                    <div class="highlight-item">
                        <span class="highlight-dot" style="background: <?= $aesthetic_data['color'] ?>"></span>
                        <span><?= $aesthetic_data['title'] ?></span>
                    </div>
                    <div class="highlight-item">
                        <span class="highlight-dot" style="background: linear-gradient(135deg, var(--primary-purple), var(--primary-dark))"></span>
                        <span><?= $style_data['name'] ?></span>
                    </div>
                    <?php if ($user['zodiac_sign'] && $user['zodiac_sign'] !== 'Not set'): ?>
                    <div class="highlight-item">
                        <span class="highlight-dot" style="background: #FFD700"></span>
                        <span><?= $user['zodiac_sign'] ?> Energy</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
      </div>
    </div> <!-- Close main-content -->

    <!-- Refine Your Style Section - MOVED OUTSIDE MAIN CONTENT, FULL WIDTH -->
    <div class="refine-section">
      <div class="section-card refine-card">
        <h2 class="section-title text-center">Refine Your Style Journey</h2>
        <div class="action-buttons justify-content-center">
          <a href="../quizzes/aesthetic_quiz.php" class="action-btn primary">Refine Aesthetic</a>
          <a href="../quizzes/style_quiz.php" class="action-btn secondary">Update Style</a>
          <a href="../undertone/undertone_result.php" class="action-btn primary">Color Analysis</a>
        </div>
      </div>
    </div>

    
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Add spinning effect to color wheel on hover
    document.addEventListener('DOMContentLoaded', function() {
      const colorWheel = document.querySelector('.color-wheel');
      if (colorWheel) {
        let animationId;
        
        colorWheel.addEventListener('mouseenter', function() {
          let rotation = 0;
          const rotateWheel = () => {
            rotation = (rotation + 1) % 360;
            colorWheel.style.transform = `rotate(${rotation}deg) scale(1.05)`;
            animationId = requestAnimationFrame(rotateWheel);
          };
          animationId = requestAnimationFrame(rotateWheel);
        });
        
        colorWheel.addEventListener('mouseleave', function() {
          if (animationId) {
            cancelAnimationFrame(animationId);
          }
          colorWheel.style.transform = 'rotate(0deg) scale(1)';
        });
      }
    });
  </script>
</body>
</html>
