<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/dbconfig.php");
include(__DIR__ . "/../includes/navbar.php");

// ✅ Fetch user data with aesthetic and style results
$user_id = $_SESSION['user_id'];
$query = "SELECT username, email, name, gender, birthdate, zodiac_sign, undertone, season, aesthetic_result, style_result 
          FROM users WHERE id = ?";
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
$aesthetic_result = htmlspecialchars($user['aesthetic_result'] ?? '');
$style_result = htmlspecialchars($user['style_result'] ?? '');

// ✅ Define aesthetic details (same as in your aesthetic_result.php)
$aesthetics = [
  "academia" => [
    "title" => "The Scholar",
    "desc" => "You're drawn to timeless sophistication — think books, tweed, and candlelit study sessions. You find beauty in intellect and classic detail.",
    "history" => "Rooted in literary charm and old-world academia, this aesthetic draws from classic literature, university libraries, and British collegiate style.",
    "mood" => "Intellectual • Vintage • Poised",
    "bg" => "../quizzes/assets/acad1.jpg",
    "side1" => "../quizzes/assets/acad_bg2.jpg",
    "side2" => "../quizzes/assets/acad3.jpg",
    "color" => "#271a0eff"
  ],
  "boho" => [
    "title" => "The Bohemian Dreamer",
    "desc" => "You embody freedom, creativity, and a love for earthy tones and textures. Every outfit tells a story — unbothered, soulful, and effortlessly chic.",
    "history" => "Emerging from 1960s counterculture, Bohemian style fuses artistic expression with natural fabrics and global influences.",
    "mood" => "Free-Spirited • Earthy • Artistic",
    "bg" => "../quizzes/assets/boho1.jpg",
    "side1" => "../quizzes/assets/boho1.jpg",
    "side2" => "../quizzes/assets/boho3.jpg",
    "color" => "#e38153ff"
  ],
  "coquette" => [
    "title" => "The Coquette Muse",
    "desc" => "Romantic and soft, your style leans into vintage elegance — ribbons, lace, and an air of flirtatious nostalgia.",
    "history" => "Blending Rococo-era charm with 90s Lolita revival, the Coquette aesthetic celebrates femininity and delicate romanticism.",
    "mood" => "Romantic • Vintage • Playful",
    "bg" => "../quizzes/assets/coquette12.jpg",
    "side1" => "../quizzes/assets/coquette2.jpg",
    "side2" => "../quizzes/assets/coquette3.jpg",
    "color" => "#f5c4d4"
  ],
  "grunge" => [
    "title" => "The Rebel Soul",
    "desc" => "You thrive in expressive chaos — distressed textures, dark tones, and a bold disregard for convention define your edge.",
    "history" => "Born from 90s alternative rock culture, Grunge fashion embodies rebellion, comfort, and raw individuality.",
    "mood" => "Edgy • Unfiltered • Rebellious",
    "bg" => "../quizzes/assets/grunge1.jpg",
    "side1" => "../quizzes/assets/grunge2.jpg",
    "side2" => "../quizzes/assets/grunge3.jpg",
    "color" => "#a1a1a1ff"
  ],
  "punk" => [
    "title" => "The Anarchic Icon",
    "desc" => "You wear rebellion like armor — unapologetic, loud, and endlessly cool. Spikes, leather, and confidence are your essentials.",
    "history" => "Emerging from 1970s UK subcultures, punk aesthetic rejected mainstream norms through fashion, music, and activism.",
    "mood" => "Defiant • Raw • Bold",
    "bg" => "../quizzes/assets/punk1.jpg",
    "side1" => "../quizzes/assets/punk_bg2.jpg",
    "side2" => "../quizzes/assets/punk3.jpg",
    "color" => "#ff0000ff"
  ],
  "y2k" => [
    "title" => "The Futuristic Popstar",
    "desc" => "You radiate playful confidence with metallics, baby tees, and digital-era nostalgia. The early 2000s live in your sparkle.",
    "history" => "A revival of late 90s and early 2000s cyber fashion — glossy textures, technology-inspired motifs, and glittery confidence.",
    "mood" => "Playful • Futuristic • Glam",
    "bg" => "../quizzes/assets/y2k1.jpg",
    "side1" => "../quizzes/assets/y2k2.jpg",
    "side2" => "../quizzes/assets/y2k3.jpg",
    "color" => "#d46be3"
  ],
  "luxurious" => [
    "title" => "The Luxe Visionary",
    "desc" => "Glitter, confidence, and high drama — your aura screams sophistication and luxury. You turn moments into red-carpet statements.",
    "history" => "Rooted in Old Hollywood and modern couture, Glam embraces opulence, allure, and timeless beauty.",
    "mood" => "Elegant • Dazzling • Confident",
    "bg" => "../quizzes/assets/glam1.jpg",
    "side1" => "../quizzes/assets/glam2.jpg",
    "side2" => "../quizzes/assets/glam3.jpg",
    "color" => "#c93939ff"
  ]
];

// Get aesthetic image for dashboard
$aesthetic_image = "";
if (!empty($aesthetic_result) && array_key_exists($aesthetic_result, $aesthetics)) {
    // Use side1 image for the dashboard preview
    $aesthetic_image = $aesthetics[$aesthetic_result]['side1'];
}

// ✅ Enhanced Astro facts with detailed information
$zodiacDetails = [
    "Aries" => [
        "personality" => "Energetic, bold, confident, and adventurous.",
        "element" => "Fire",
        "planet" => "Mars",
        "lucky_numbers" => "1, 9, 14",
        "strengths" => "Courageous, passionate, determined",
        "weaknesses" => "Impulsive, impatient, short-tempered",
        "traits" => "Aries is about action and leadership, always ready to start new adventures and face challenges head-on.",
        "compatibility" => "Best matches with Leo and Sagittarius."
    ],
    "Taurus" => [
        "personality" => "Patient, reliable, practical, and loving.",
        "element" => "Earth",
        "planet" => "Venus",
        "lucky_numbers" => "2, 6, 9",
        "strengths" => "Loyal, persistent, trustworthy",
        "weaknesses" => "Stubborn, possessive, resistant to change",
        "traits" => "Taurus values comfort and stability, enjoying the finer things in life and lasting relationships.",
        "compatibility" => "Best matches with Virgo and Capricorn."
    ],
    "Gemini" => [
        "personality" => "Curious, adaptable, witty, and sociable.",
        "element" => "Air",
        "planet" => "Mercury",
        "lucky_numbers" => "3, 5, 7",
        "strengths" => "Intelligent, expressive, versatile",
        "weaknesses" => "Inconsistent, indecisive, restless",
        "traits" => "Gemini thrives on communication and learning, always exploring new ideas and experiences.",
        "compatibility" => "Best matches with Libra and Aquarius."
    ],
    "Cancer" => [
        "personality" => "Emotional, caring, protective, and intuitive.",
        "element" => "Water",
        "planet" => "Moon",
        "lucky_numbers" => "2, 7, 11",
        "strengths" => "Loyal, empathetic, nurturing",
        "weaknesses" => "Moody, sensitive, clingy",
        "traits" => "Cancer values home, family, and emotional security, guided by deep feelings and compassion.",
        "compatibility" => "Best matches with Scorpio and Pisces."
    ],
    "Leo" => [
        "personality" => "Confident, charismatic, generous, and creative.",
        "element" => "Fire",
        "planet" => "Sun",
        "lucky_numbers" => "1, 3, 10",
        "strengths" => "Ambitious, warm-hearted, loyal",
        "weaknesses" => "Arrogant, stubborn, attention-seeking",
        "traits" => "Leo loves to shine and lead, inspiring others through enthusiasm and self-expression.",
        "compatibility" => "Best matches with Aries and Sagittarius."
    ],
    "Virgo" => [
        "personality" => "Practical, analytical, reliable, and modest.",
        "element" => "Earth",
        "planet" => "Mercury",
        "lucky_numbers" => "5, 14, 23",
        "strengths" => "Detail-oriented, hardworking, intelligent",
        "weaknesses" => "Overcritical, perfectionist, anxious",
        "traits" => "Virgo is focused on improvement, organization, and helping others in meaningful ways.",
        "compatibility" => "Best matches with Taurus and Capricorn."
    ],
    "Libra" => [
        "personality" => "Charming, fair-minded, diplomatic, and sociable.",
        "element" => "Air",
        "planet" => "Venus",
        "lucky_numbers" => "6, 15, 24",
        "strengths" => "Cooperative, graceful, balanced",
        "weaknesses" => "Indecisive, people-pleasing, superficial",
        "traits" => "Libra values harmony and beauty, always striving to create peace and fairness in relationships.",
        "compatibility" => "Best matches with Gemini and Aquarius."
    ],
    "Scorpio" => [
        "personality" => "Passionate, mysterious, determined, and resourceful.",
        "element" => "Water",
        "planet" => "Pluto (and Mars)",
        "lucky_numbers" => "8, 11, 18",
        "strengths" => "Loyal, brave, intuitive",
        "weaknesses" => "Jealous, secretive, controlling",
        "traits" => "Scorpio is about transformation, depth, and emotional power, often symbolizing rebirth and truth.",
        "compatibility" => "Best matches with Cancer and Pisces."
    ],
    "Sagittarius" => [
        "personality" => "Adventurous, optimistic, honest, and free-spirited.",
        "element" => "Fire",
        "planet" => "Jupiter",
        "lucky_numbers" => "3, 9, 12",
        "strengths" => "Enthusiastic, open-minded, idealistic",
        "weaknesses" => "Impulsive, blunt, inconsistent",
        "traits" => "Sagittarius seeks knowledge and adventure, always aiming for growth and exploration.",
        "compatibility" => "Best matches with Aries and Leo."
    ],
    "Capricorn" => [
        "personality" => "Ambitious, disciplined, responsible, and patient.",
        "element" => "Earth",
        "planet" => "Saturn",
        "lucky_numbers" => "4, 8, 22",
        "strengths" => "Practical, hardworking, dependable",
        "weaknesses" => "Pessimistic, rigid, workaholic",
        "traits" => "Capricorn strives for success and stability, valuing structure and long-term achievements.",
        "compatibility" => "Best matches with Taurus and Virgo."
    ],
    "Aquarius" => [
        "personality" => "Innovative, original, independent, humanitarian.",
        "element" => "Air",
        "planet" => "Uranus",
        "lucky_numbers" => "2, 7, 11",
        "strengths" => "Innovative, idealistic, independent",
        "weaknesses" => "Unpredictable, aloof, stubborn",
        "traits" => "Aquarius is about innovation, individuality, and humanitarian causes, representing progressive ideas and social change.",
        "compatibility" => "Best matches with Gemini and Libra."
    ],
    "Pisces" => [
        "personality" => "Compassionate, artistic, gentle, and empathetic.",
        "element" => "Water",
        "planet" => "Neptune",
        "lucky_numbers" => "3, 9, 12",
        "strengths" => "Imaginative, kind, intuitive",
        "weaknesses" => "Escapist, overly trusting, emotional",
        "traits" => "Pisces is deeply connected to dreams and emotions, often drawn to creativity and spirituality.",
        "compatibility" => "Best matches with Cancer and Scorpio."
    ]
];

$zodiacData = $zodiacDetails[$zodiac] ?? [
    "personality" => "Complete your zodiac profile to unlock your astro details!",
    "element" => "Not set",
    "planet" => "Not set",
    "lucky_numbers" => "Not set",
    "strengths" => "Not set",
    "weaknesses" => "Not set",
    "traits" => "Complete your zodiac profile to unlock your astro details!",
    "compatibility" => "Not set"
];

// Define the correct base path for assets
$base_path = "/Celesticare/undertone/assets/";

// Fetch outfit data for moodboard
$outfit_stmt = $conn->prepare("SELECT outfit_data FROM user_outfits WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$outfit_stmt->bind_param("i", $user_id);
$outfit_stmt->execute();
$outfit_stmt->bind_result($outfit_json);
$outfit_stmt->fetch();
$outfit_stmt->close();
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
    background: linear-gradient(135deg, #d3bff5ff 0%, #3c3b7dff 100%);
    font-family: 'Poppins', sans-serif;
    overflow-x: hidden;
}
.container { max-width: 1200px; background: transparent; }
.card {
    border-radius: 15px;
    background-color: rgba(255, 255, 255, 0.9);
    box-shadow: none;
    border: none;
}
h2 { color: #6a1b9a; font-weight: 600; }
.section-title { font-size: 1.2rem; font-weight: 600; color: #333; }
.profile-section, .astro-section, .color-section, .aesthetic-section, .outfit-section, .moodboard-section {
    border-radius: 10px;
    background-color: #fff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    padding: 20px;
    min-height: 280px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.grid-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 20px;
    margin-top: 30px;
}
.color-wheel {
    max-width: 220px;
    height: auto;
    display: block;
    margin: 0 auto;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    
    /* Add these for better control */
    object-fit: cover;
    transition: all 0.3s ease;
}
.aesthetic-image {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-radius: 10px;
    margin-bottom: 15px;
    border: 3px solid #fff;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}
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
    text-align: center;
    width: fit-content;
    min-width: 180px;
}
.btn-warning { background-color: #f7b731; border: none; }
.btn-warning:hover { background-color: #f9ca4f; }
.btn-danger { background-color: #e74c3c; border: none; }
.btn-danger:hover { background-color: #ff6b5a; }

.modal-content {
    background-color: #2e2e2e;
    color: #ffffff;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}
.modal-title { font-size: 20px; text-align: center; font-weight: 600; }
.form-control {
    background-color: #4b4b4b;
    border: none;
    color: #fff;
    border-radius: 30px;
    padding: 12px 20px;
}
.form-control::placeholder { color: #cfcfcf; }
.btn-primary {
    background-color: #6b5b95;
    border: none;
    border-radius: 30px;
    padding: 10px 20px;
    width: 100%;
    transition: 0.3s;
}
.btn-primary:hover { background-color: #8c77c5; }

html::-webkit-scrollbar,
body::-webkit-scrollbar { width: 0 !important; height: 0 !important; background: transparent; }

.color-swatch {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    border: 2px solid #fff;
    display: inline-block;
    margin: 0 5px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

/* Enhanced Profile Section */
.profile-section {
    border-radius: 10px;
    background-color: #fff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    padding: 20px;
    min-height: 280px;
    display: flex;
    flex-direction: column;
}

.profile-info {
    margin-top: 0;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
}

.profile-info p {
    margin-bottom: 8px;
    line-height: 1.3;
    font-size: 0.95rem;
    padding: 2px 0;
}

.profile-item {
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    min-height: auto;
    padding: 4px 0;
}

.profile-label {
    font-weight: 700; /* Changed from 600 to 700 for bolder text */
    color: #6a1b9a;
    min-width: 100px;
    font-size: 0.9rem;
}

.profile-value {
    color: #333; /* Changed from #555 to #333 for better contrast */
    flex: 1;
    font-size: 0.9rem;
    font-weight: 600; /* Added bold weight to user info */
    text-align: left; /* Ensure consistent alignment */
}

/* Remove any extra margins that might be causing gaps */
.section-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 15px;
}

/* Ensure grid items have consistent spacing */
.grid-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 20px;
    margin-top: 30px;
}


/* Enhanced mobile responsiveness */
@media (max-width: 768px) {
    .profile-info p {
        margin-bottom: 6px;
        line-height: 1.2;
    }
    
    .profile-item {
        margin-bottom: 6px;
        flex-direction: row; /* Keep items in row layout */
        align-items: flex-start; /* Align to top */
        text-align: left;
    }
    
    .profile-label {
        min-width: 90px;
        font-weight: 700; /* Maintain bold on mobile */
    }
    
    .profile-value {
        text-align: left; /* Force left alignment */
        font-weight: 600; /* Maintain bold on mobile */
        margin-left: 0; /* Remove any left margin */
        padding-left: 0; /* Remove any left padding */
    }
}

@media (max-width: 480px) {
    .grid-container {
        grid-template-columns: 1fr;
    }
    
    .profile-label {
        min-width: 85px;
        font-size: 0.85rem;
        font-weight: 700;
    }
    
    .profile-value {
        font-size: 0.85rem;
        font-weight: 600;
        text-align: left;
        word-break: break-word; /* Handle long text */
    }
    
    .profile-item {
        flex-wrap: nowrap; /* Prevent wrapping to new lines */
    }
}


/* Single item preview */
.single-item-preview {
    width: 60px;
    height: 60px;
    object-fit: contain;
    border-radius: 8px;
    border: 2px solid #e9ecef;
    padding: 3px;
    background: white;
}

/* Enhanced Badge Styles */
.enhanced-badge {
    display: inline-block;
    padding: 0.4rem 1rem;
    border-radius: 25px;
    font-size: 0.85rem;
    font-weight: 600;
    margin: 8px 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border: 2px solid rgba(255,255,255,0.3);
}

.aesthetic-badge {
    display: inline-block;
    padding: 0.4rem 1rem;
    border-radius: 25px;
    font-size: 0.85rem;
    font-weight: 600;
    margin: 8px 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border: 2px solid rgba(255,255,255,0.3);
}

/* Enhanced Clothing Preview */
.enhanced-outfit-preview {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin: 15px 0;
    flex-wrap: wrap;
}

.enhanced-clothing-item {
    width: 80px;
    height: 80px;
    border-radius: 12px;
    overflow: hidden;
    background: white;
    border: 3px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.enhanced-clothing-item:hover {
    border-color: #8B5FBF;
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(139, 95, 191, 0.3);
}

.enhanced-clothing-img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    padding: 5px;
    border-radius: 8px;
}

/* Enhanced Aesthetic Image */
.enhanced-aesthetic-image {
    width: 100%;
    height: 160px;
    object-fit: cover;
    border-radius: 12px;
    margin: 12px 0;
    border: 3px solid #fff;
    box-shadow: 0 6px 15px rgba(0,0,0,0.2);
}

/* More Items Indicator */
.more-items-indicator {
    margin: 8px 0;
}

.more-badge {
    background: linear-gradient(135deg, #8B5FBF, #6D28D9);
    color: white;
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 500;
}

/* Enhanced Astro Section */
.zodiac-header {
    text-align: center;
    margin-bottom: 15px;
}

.zodiac-name {
    font-size: 1.4rem;
    font-weight: 700;
    color: #6a1b9a;
    margin-bottom: 15px;
}

.zodiac-details {
    font-size: 0.9rem;
    font-weight: 600;
    line-height: 1.4;
}

.zodiac-detail-item {
    margin-bottom: 8px;
    display: flex;
}

.zodiac-label {
    font-weight: 600;
    color: #6a1b9a;
    min-width: 120px;
}

.zodiac-value {
    color: #555;
    flex: 1;
}

/* Enhanced Moodboard Section */
.moodboard-complete {
    background: linear-gradient(135deg, #8B5FBF, #6D28D9);
    color: white;
    text-align: center;
    border-radius: 10px;
    padding: 25px 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    transition: all 0.3s ease;
}

.moodboard-complete:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(139, 95, 191, 0.3);
}

.moodboard-icon {
    font-size: 3rem;
    margin-bottom: 15px;
}

.moodboard-title {
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 10px;
}

.moodboard-subtitle {
    font-size: 0.9rem;
    opacity: 0.9;
    margin-bottom: 20px;
}

.moodboard-incomplete {
    background: #f8f9fa;
    text-align: center;
    border-radius: 10px;
    padding: 25px 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    border: 2px dashed #dee2e6;
}

.moodboard-incomplete-icon {
    font-size: 2.5rem;
    margin-bottom: 15px;
    opacity: 0.5;
}

.moodboard-incomplete-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 10px;
}

.moodboard-incomplete-subtitle {
    font-size: 0.85rem;
    color: #6c757d;
    margin-bottom: 15px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .enhanced-clothing-item {
        width: 70px;
        height: 70px;
    }
    
    .enhanced-aesthetic-image {
        height: 140px;
    }
    
    .zodiac-detail-item,
    .profile-item {
        flex-direction: column;
    }
    
    .zodiac-label,
    .profile-label {
        min-width: auto;
        margin-bottom: 2px;
    }
    
    .profile-label {
        min-width: 120px;
    }
}

@media (max-width: 480px) {
    .grid-container {
        grid-template-columns: 1fr;
    }
    
    .profile-label {
        min-width: 110px;
    }
}
</style>
</head>

<body>
<div class="container mt-5 mb-5">
    <div class="card p-4 shadow-sm">
        <h2>The Universe Welcomes your presence, <?php echo $username; ?>!</h2>
        <p>This is your profile dashboard where your zodiac style journey begins.</p>

        <div class="grid-container mt-4">
            <!-- Enhanced Profile Section - FIXED VERSION -->
            <div class="profile-section">
                <div class="section-title">Profile</div>
                <div class="profile-info">
                    <div class="profile-item">
                        <span class="profile-label">Name:</span>
                        <span class="profile-value" id="displayName"><?= $name ?: 'Not set' ?></span>
                    </div>
                    <div class="profile-item">
                        <span class="profile-label">Username:</span>
                        <span class="profile-value"><?= $username ?></span>
                    </div>
                    <div class="profile-item">
                        <span class="profile-label">Email:</span>
                        <span class="profile-value"><?= $email ?></span>
                    </div>
                    <div class="profile-item">
                        <span class="profile-label">Gender:</span>
                        <span class="profile-value" id="displayGender"><?= $gender ?: 'Not set' ?></span>
                    </div>
                    <div class="profile-item">
                        <span class="profile-label">Birthdate:</span>
                        <span class="profile-value" id="displayBirthdate"><?= $birthdate ?: 'Not set' ?></span>
                    </div>
                    <div class="profile-item">
                        <span class="profile-label">Zodiac:</span>
                        <span class="profile-value" id="displayZodiac"><?= $zodiac ?></span>
                    </div>
                </div>
            </div>
            <!-- 🌟 Enhanced Astro Section -->
            <div class="astro-section">
                <div class="section-title">Astro Insights</div>
                <div class="zodiac-header">
                    <div class="zodiac-name">Your Zodiac Sign: <?= $zodiac ?></div>
                </div>
                <div class="zodiac-details">
                    <div class="zodiac-detail-item">
                        <span class="zodiac-label">Personality:</span>
                        <span class="zodiac-value"><?= $zodiacData['personality'] ?></span>
                    </div>
                    <div class="zodiac-detail-item">
                        <span class="zodiac-label">Element:</span>
                        <span class="zodiac-value"><?= $zodiacData['element'] ?></span>
                    </div>
                    <div class="zodiac-detail-item">
                        <span class="zodiac-label">Ruling Planet:</span>
                        <span class="zodiac-value"><?= $zodiacData['planet'] ?></span>
                    </div>
                    <div class="zodiac-detail-item">
                        <span class="zodiac-label">Lucky Numbers:</span>
                        <span class="zodiac-value"><?= $zodiacData['lucky_numbers'] ?></span>
                    </div>
                    <div class="zodiac-detail-item">
                        <span class="zodiac-label">Strengths:</span>
                        <span class="zodiac-value"><?= $zodiacData['strengths'] ?></span>
                    </div>
                    <div class="zodiac-detail-item">
                        <span class="zodiac-label">Weaknesses:</span>
                        <span class="zodiac-value"><?= $zodiacData['weaknesses'] ?></span>
                    </div>
                    <div class="zodiac-detail-item">
                        <span class="zodiac-label">Traits:</span>
                        <span class="zodiac-value"><?= $zodiacData['traits'] ?></span>
                    </div>
                    <div class="zodiac-detail-item">
                        <span class="zodiac-label">Compatibility:</span>
                        <span class="zodiac-value"><?= $zodiacData['compatibility'] ?></span>
                    </div>
                </div>
            </div>

            <!-- 🎨 Color Analysis - SIMPLE VERSION -->
            <div class="color-section">
                <div class="section-title mb-3">Color Analysis</div>
                
                <div class="mb-3">
                    <p class="mb-1"><strong>Zodiac:</strong> <?= $zodiac ?></p>
                    <p class="mb-1"><strong>Undertone:</strong> <?= $undertone ?></p>
                    <p class="mb-1"><strong>Season:</strong> <?= $season ?></p>
                </div>
                
                <?php if ($undertone && $undertone !== 'Not set'): ?>
                    <div class="text-center mb-3">
                        <?php
                        $undertone_lower = strtolower($undertone);
                        $image_path = $base_path . $undertone_lower . "_wheel.png";
                        ?>
                        <img src="<?= $image_path ?>" 
                            alt="<?= $undertone ?> color wheel" 
                            class="color-wheel">
                    </div>
                    
                    <div class="text-center mb-3">
                        <?php
                        $palettes = [
                            "Warm" => ["#E69A5B", "#F5C16C", "#D76A03", "#C25B02", "#FFD27F"],
                            "Cool" => ["#5B7BE6", "#A3C1F7", "#7089E3", "#4059C2", "#9EB8FF"],
                            "Neutral" => ["#D7BFAE", "#C1B3A4", "#A8988B", "#8B7C6F", "#BFA98B"]
                        ];
                        
                        if (isset($palettes[$undertone])) {
                            foreach ($palettes[$undertone] as $color) {
                                echo '<div class="color-swatch d-inline-block" style="background: ' . $color . '"></div>';
                            }
                        }
                        ?>
                    </div>
                    
                    <div class="text-center">
                        <a href="../undertone/undertone_result.php" class="btn-style">View Full Analysis</a>
                    </div>
                <?php else: ?>
                    <div class="text-center py-3">
                        <p class="text-muted mb-3">Complete color analysis to see your personal color palette</p>
                        <a href="../setup/get_to_know.php" class="btn-style">I want to know!</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- 🌸 Aesthetic Section - ENHANCED VERSION -->
            <div class="aesthetic-section text-center">
                <div class="section-title mb-2">Aesthetics</div>
                <?php if (!empty($aesthetic_result)) : ?>
                    <?php
                    // Get aesthetic color from your aesthetics array
                    $aesthetic_color = $aesthetics[$aesthetic_result]['color'] ?? '#8B5FBF';
                    $aesthetic_name = ucfirst($aesthetic_result);
                    ?>
                    
                    <!-- Enhanced Aesthetic Badge -->
                    <div class="enhanced-badge aesthetic-badge" style="background: <?= $aesthetic_color ?>; color: #ffffff;">
                        Your Aesthetic: <?= $aesthetic_name ?>
                    </div>
                    
                    <?php if (!empty($aesthetic_image)): ?>
                        <img src="<?= $aesthetic_image ?>" alt="<?= $aesthetic_result ?> aesthetic" class="enhanced-aesthetic-image">
                    <?php endif; ?>
                    
                    <a href="../quizzes/aesthetic_result.php" class="btn-style">View Result</a>
                    
                <?php else : ?>
                    <p>Want to know your Aesthetic?</p>
                    <a href="../quizzes/aesthetic_welcome.php" class="btn-style">I want to know!</a>
                <?php endif; ?>
            </div>

            <!-- 👗 Style Section - ENHANCED VERSION -->
            <div class="outfit-section text-center">
                <div class="section-title mb-2">Your Style</div>
                <?php if (!empty($style_result)) : ?>
                    <?php
                    // Enhanced style information with personalized colors
                    $styleInfo = [
                        'minimalist' => [
                            'name' => 'Minimalist Elegance',
                            'color' => 'linear-gradient(135deg, #f8fafc, #e2e8f0)',
                            'text_color' => '#374151'
                        ],
                        'businesswear' => [
                            'name' => 'Professional Businesswear',
                            'color' => 'linear-gradient(135deg, #1e3a8a, #3730a3)',
                            'text_color' => '#ffffff'
                        ],
                        'elegant' => [
                            'name' => 'Classic Elegance',
                            'color' => 'linear-gradient(135deg, #7e22ce, #c084fc)',
                            'text_color' => '#ffffff'
                        ],
                        'creative' => [
                            'name' => 'Creative Expression',
                            'color' => 'linear-gradient(135deg, #ea580c, #f59e0b)',
                            'text_color' => '#ffffff'
                        ],
                        'soft' => [
                            'name' => 'Soft Elegance',
                            'color' => 'linear-gradient(135deg, #f9a8d4, #f472b6)',
                            'text_color' => '#ffffff'
                        ],
                        'rough' => [
                            'name' => 'Rough Edge',
                            'color' => 'linear-gradient(135deg, #4b5563, #6b7280)',
                            'text_color' => '#ffffff'
                        ],
                        'streetwear' => [
                            'name' => 'Urban Streetwear',
                            'color' => 'linear-gradient(135deg, #000000, #374151)',
                            'text_color' => '#ffffff'
                        ]
                    ];
                    
                    $styleData = $styleInfo[$style_result] ?? [
                        'name' => ucfirst($style_result),
                        'color' => 'linear-gradient(135deg, #8B5FBF, #6D28D9)',
                        'text_color' => '#ffffff'
                    ];
                    ?>
                    
                    <!-- Enhanced Style Badge -->
                    <div class="enhanced-badge" style="background: <?= $styleData['color'] ?>; color: <?= $styleData['text_color'] ?>;">
                        <?= $styleData['name'] ?>
                    </div>
                    
                    <!-- Enhanced Clothing Display -->
                    <?php 
                    if (!empty($outfit_json)) {
                        $outfit_items = json_decode($outfit_json, true);
                        if (!empty($outfit_items)) {
                            echo '<div class="enhanced-outfit-preview mt-3 mb-3">';
                            // Display first 3 items in a nice grid
                            $display_items = array_slice($outfit_items, 0, 3);
                            foreach ($display_items as $item) {
                                echo '<div class="enhanced-clothing-item">';
                                echo '<img src="' . htmlspecialchars($item['src']) . '" ';
                                echo 'alt="' . htmlspecialchars($item['category'] ?? 'Style item') . '" ';
                                echo 'class="enhanced-clothing-img" ';
                                echo 'title="' . htmlspecialchars($item['category'] ?? 'Item') . '">';
                                echo '</div>';
                            }
                            echo '</div>';
                            
                            // Show "more items" indicator if there are more
                            if (count($outfit_items) > 3) {
                                echo '<div class="more-items-indicator">';
                                echo '<span class="more-badge">+' . (count($outfit_items) - 3) . ' more items</span>';
                                echo '</div>';
                            }
                        }
                    }
                    ?>
                    
                    <a href="../quizzes/style_result.php" class="btn-style">View Full Analysis</a>
                    
                <?php else : ?>
                    <div class="py-4">
                        <p class="mb-3">Discover your personal style through our style quiz!</p>
                        <a href="../quizzes/style_welcome.php" class="btn-style">Find My Style</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- 🎨 Moodboard Section - SUBTLE VERSION -->
            <div class="moodboard-section">
                <?php 
                // Check if user has completed both aesthetic and style quizzes
                $hasMoodboard = !empty($aesthetic_result) && !empty($style_result);
                
                if ($hasMoodboard): 
                ?>
                    <!-- Complete State - Colorful and Simple -->
                    <div class="moodboard-complete">
                        <div class="moodboard-icon">🎨</div>
                        <div class="moodboard-title">Your Moodboard is Ready!</div>
                        <div class="moodboard-subtitle">Explore your personalized style visualization</div>
                        <a href="/Celesticare/moodboard/moodboard_result.php" class="btn-style" style="background: #fff; color: #8B5FBF;">View Moodboard</a>
                    </div>
                <?php else: ?>
                    <!-- Incomplete State - Simple and Uniform -->
                    <div class="moodboard-incomplete">
                        <div class="moodboard-incomplete-icon">🎨</div>
                        <div class="moodboard-incomplete-title">Moodboard</div>
                        <div class="moodboard-incomplete-subtitle">Complete your style journey to unlock</div>
                        <div class="action-buttons mt-3">
                            <?php if (empty($aesthetic_result)): ?>
                                <a href="../quizzes/aesthetic_welcome.php" class="btn-style">Start Aesthetic Quiz</a>
                            <?php endif; ?>
                            <?php if (empty($style_result)): ?>
                                <a href="../quizzes/style_welcome.php" class="btn-style">Start Style Quiz</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profile</button>
            <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background-color: #2e2e2e; color: #ffffff; border-radius: 20px; border: none;">
            <div class="modal-header border-0">
                <h5 class="modal-title" style="color: #ffffff;">Edit Profile</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editProfileForm">
                    <!-- Basic Information -->
                    <div class="mb-4">
                        <h6 class="mb-3" style="color: #6b5b95;"><i class="fas fa-user"></i> Basic Information</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label" style="color: #cfcfcf;">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= $name ?>" 
                                       placeholder="Enter your full name"
                                       style="background-color: #4b4b4b; border: none; color: #fff; border-radius: 30px; padding: 12px 20px;">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label" style="color: #cfcfcf;">Gender</label>
                                <select class="form-control" id="gender" name="gender"
                                        style="background-color: #4b4b4b; border: none; color: #fff; border-radius: 30px; padding: 12px 20px;">
                                    <option value="">Select Gender</option>
                                    <option value="Masculine" <?= $gender == 'Masculine' ? 'selected' : '' ?>>Masculine</option>
                                    <option value="Feminine" <?= $gender == 'Feminine' ? 'selected' : '' ?>>Feminine</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="birthdate" class="form-label" style="color: #cfcfcf;">Date of Birth</label>
                                <input type="date" class="form-control" id="birthdate" name="birthdate" value="<?= $birthdate ?>"
                                       style="background-color: #4b4b4b; border: none; color: #fff; border-radius: 30px; padding: 12px 20px;">
                            </div>
                        </div>
                    </div>

                    <!-- Password Change Section -->
                    <div class="mb-4">
                        <h6 class="mb-3" style="color: #6b5b95;"><i class="fas fa-lock"></i> Change Password</h6>
                        <div class="alert alert-dark mb-3" style="background-color: #4b4b4b; border: none; color: #cfcfcf; border-radius: 15px;">
                            <small><i class="fas fa-info-circle"></i> Leave password fields blank if you don't want to change your password.</small>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="current_password" class="form-label" style="color: #cfcfcf;">Current Password</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" 
                                       placeholder="Enter current password"
                                       style="background-color: #4b4b4b; border: none; color: #fff; border-radius: 30px; padding: 12px 20px;">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="new_password" class="form-label" style="color: #cfcfcf;">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" 
                                       placeholder="Enter new password"
                                       style="background-color: #4b4b4b; border: none; color: #fff; border-radius: 30px; padding: 12px 20px;">
                                <div class="form-text" style="color: #8c77c5; padding-left: 15px;">Must be at least 6 characters long</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label" style="color: #cfcfcf;">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                       placeholder="Confirm new password"
                                       style="background-color: #4b4b4b; border: none; color: #fff; border-radius: 30px; padding: 12px 20px;">
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="zodiac_sign" name="zodiac_sign" value="<?= $zodiac ?>">
                    
                    <button type="submit" class="btn mt-3 w-100" 
                            style="background-color: #6b5b95; color: #fff; border: none; border-radius: 30px; padding: 12px 20px; transition: 0.3s;"
                            onmouseover="this.style.backgroundColor='#8c77c5'" 
                            onmouseout="this.style.backgroundColor='#6b5b95'">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </form>
            </div>
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

// 💾 Save via AJAX with better error handling
document.getElementById("editProfileForm").addEventListener("submit", async function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  const submitButton = this.querySelector('button[type="submit"]');
  const originalText = submitButton.textContent;
  
  // Show loading state
  submitButton.textContent = 'Saving...';
  submitButton.disabled = true;
  
  try {
    const response = await fetch("save_profile_ajax.php", { 
      method: "POST", 
      body: formData 
    });
    
    let result;
    
    // Try to parse JSON response
    try {
      result = await response.json();
    } catch (jsonError) {
      console.error('JSON parse error:', jsonError);
      throw new Error('Invalid response from server');
    }
    
    if (result.success) {
        // Update displayed values immediately
        if (result.data) {
            document.getElementById("displayName").textContent = result.data.name || "Not set";
            document.getElementById("displayGender").textContent = result.data.gender || "Not set";
            document.getElementById("displayBirthdate").textContent = result.data.birthdate || "Not set";
            document.getElementById("displayZodiac").textContent = result.data.zodiac_sign || "Not set";
        }
        
        // Show success message
        showAlert('Profile updated successfully!', 'success');
        
        // Close modal after a short delay
        setTimeout(() => {
            const modal = bootstrap.Modal.getInstance(document.getElementById("editProfileModal"));
            modal.hide();
            
            // ✅ Reload to ensure everything is synchronized
            setTimeout(() => {
                location.reload();
            }, 1000);
        }, 1500);
        
    } else {
        // Show specific error message from server
        const errorMsg = result.message || "Failed to update profile";
        showAlert('Error: ' + errorMsg, 'error');
        console.error('Server error:', result);
    }
  } catch (error) {
    console.error("Network error:", error);
    showAlert('Network error: ' + error.message, 'error');
  } finally {
    // Restore button state
    submitButton.textContent = originalText;
    submitButton.disabled = false;
  }
});

// Helper function to show alerts with theme styling
function showAlert(message, type) {
  // Remove any existing alerts
  const existingAlert = document.querySelector('.custom-alert');
  if (existingAlert) {
    existingAlert.remove();
  }
  
  const alertDiv = document.createElement('div');
  alertDiv.className = `custom-alert alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed`;
  alertDiv.style.cssText = `
    top: 20px;
    right: 20px;
    z-index: 9999;
    min-width: 300px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border: none;
    border-radius: 15px;
    color: ${type === 'success' ? '#155724' : '#721c24'};
    background-color: ${type === 'success' ? '#d4edda' : '#f8d7da'};
  `;
  alertDiv.textContent = message;
  
  document.body.appendChild(alertDiv);
  
  // Auto remove after 5 seconds
  setTimeout(() => {
    alertDiv.remove();
  }, 5000);
}

// Password validation with matching styling
// Password validation
function validatePassword() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const currentPassword = document.getElementById('current_password').value;
    
    // If any password field is filled, all must be filled
    if (newPassword || confirmPassword || currentPassword) {
        if (!currentPassword) {
            showAlert('Current password is required to change password', 'error');
            return false;
        }
        if (!newPassword) {
            showAlert('New password is required', 'error');
            return false;
        }
        if (!confirmPassword) {
            showAlert('Please confirm your new password', 'error');
            return false;
        }
        if (newPassword !== confirmPassword) {
            showAlert('New passwords do not match', 'error');
            return false;
        }
        if (newPassword.length < 6) {
            showAlert('New password must be at least 6 characters long', 'error');
            return false;
        }
    }
    return true;
}

// Real-time password matching indicator
document.getElementById('confirm_password').addEventListener('input', function() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = this.value;
    
    if (confirmPassword && newPassword) {
        if (newPassword === confirmPassword) {
            this.style.border = '2px solid #28a745';
            this.style.boxShadow = '0 0 0 0.2rem rgba(40, 167, 69, 0.25)';
        } else {
            this.style.border = '2px solid #dc3545';
            this.style.boxShadow = '0 0 0 0.2rem rgba(220, 53, 69, 0.25)';
        }
    } else {
        this.style.border = 'none';
        this.style.boxShadow = 'none';
    }
});

// Add focus effects to match login style
document.querySelectorAll('#editProfileForm .form-control').forEach(input => {
    input.addEventListener('focus', function() {
        this.style.border = '2px solid #6b5b95';
        this.style.boxShadow = '0 0 0 0.2rem rgba(107, 91, 149, 0.25)';
    });
    
    input.addEventListener('blur', function() {
        this.style.border = 'none';
        this.style.boxShadow = 'none';
    });
});

// Update form submission to include password validation START
// 💾 Save via AJAX with password support
document.getElementById("editProfileForm").addEventListener("submit", async function(e) {
  e.preventDefault();

  if (!validatePassword()) {
  return;
  }
  
  const formData = new FormData(this);
  const submitButton = this.querySelector('button[type="submit"]');
  const originalText = submitButton.textContent;
  
  // Show loading state
  submitButton.textContent = 'Saving...';
  submitButton.disabled = true;
  submitButton.style.backgroundColor = '#8c77c5';
  
  try {
    const response = await fetch("save_profile_ajax.php", { 
      method: "POST", 
      body: formData 
    });
    
    const responseText = await response.text();
    console.log("Server Response:", responseText);
    
    let result;
    try {
      result = JSON.parse(responseText);
    } catch (e) {
      console.error("JSON Parse Error:", e, "Response:", responseText);
      showAlert('Server error: Invalid response format', 'error');
      return;
    }
    
    console.log("Parsed Result:", result);
    
    // Check for success
    if (result.success === true) {
      // Update displayed values
      if (result.data) {
        document.getElementById("displayName").textContent = result.data.name || "Not set";
        document.getElementById("displayGender").textContent = result.data.gender || "Not set";
        document.getElementById("displayBirthdate").textContent = result.data.birthdate || "Not set";
        document.getElementById("displayZodiac").textContent = result.data.zodiac_sign || "Not set";
      }
      
      // Show success message
      showAlert(result.message, 'success');
      
      // Clear password fields
      document.getElementById('current_password').value = '';
      document.getElementById('new_password').value = '';
      document.getElementById('confirm_password').value = '';
      
      // Close modal after delay
      setTimeout(() => {
        const modal = bootstrap.Modal.getInstance(document.getElementById("editProfileModal"));
        if (modal) modal.hide();
      }, 1500);
      
    } else {
      // Show error message
      showAlert(result.message, 'error');
    }
    
  } catch (error) {
    console.error("Network error:", error);
    showAlert('Network error: ' + error.message, 'error');
  } finally {
    // Restore button state
    submitButton.textContent = originalText;
    submitButton.disabled = false;
    submitButton.style.backgroundColor = '#6b5b95';
  }
});

// Also make sure your save_profile_ajax.php file is returning proper JSON
</script>
</body>
</html>