<?php
// about.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

include("config/dbconfig.php");

// Handle form submit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];
    $experience = intval($_POST['experience'] ?? 0);
    $fashionMatch = $conn->real_escape_string($_POST['fashion_match'] ?? '');
    $favoriteFeature = $conn->real_escape_string($_POST['favorite_feature'] ?? '');
    $vibe = $conn->real_escape_string($_POST['vibe'] ?? '');
    $suggestions = $conn->real_escape_string($_POST['suggestions'] ?? '');

    // Check if feedback table exists, if not create it
    $table_check = mysqli_query($conn, "SHOW TABLES LIKE 'user_feedback'");
    if (mysqli_num_rows($table_check) == 0) {
        // Create the table if it doesn't exist
        $create_table = "CREATE TABLE user_feedback (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            experience INT NOT NULL,
            fashion_match VARCHAR(50) NOT NULL,
            favorite_feature VARCHAR(100) NOT NULL,
            vibe VARCHAR(100) NOT NULL,
            suggestions TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )";
        mysqli_query($conn, $create_table);
    }

    // Insert feedback into database
    $stmt = $conn->prepare("INSERT INTO user_feedback (user_id, experience, fashion_match, favorite_feature, vibe, suggestions) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissss", $user_id, $experience, $fashionMatch, $favoriteFeature, $vibe, $suggestions);
    
    if ($stmt->execute()) {
        $message = "✨ Thank you for your feedback!";
    } else {
        $message = "❌ Error submitting feedback. Please try again.";
    }
    $stmt->close();
}

include("includes/navbar.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Celesticare | About Us</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

  <!-- Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

  <style>
    :root {
      --bg-gradient-start: #3b3b80;
      --bg-gradient-end: #7d66b9;
      --page-bg: #f7f7fa;
      --panel-bg: #ffffff;
      --panel-border: rgba(0,0,0,0.08);
      --headline: #1c1c27;
      --bodytext: #2f2f38;
      --subtext: #575767;
      --accent-pill-bg: #e8e1f6;
      --accent-pill-text: #3b2d61;
      --accent-pill-border: #7e61c9;
      --inactive-tab-text: #6a6a75;
      --radius-card: 18px;
      --radius-lg: 12px;
      --radius-sm: 8px;
      --shadow-card: 0 24px 40px rgba(16, 9, 69, 0.18);
      --shadow-inner-card: 0 20px 32px rgba(0,0,0,0.08);
      --logout: #c5416c;
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      min-height: 100vh;
      background:
        radial-gradient(circle at 20% 20%, rgba(255,255,255,0.08) 0%, rgba(0,0,0,0) 60%),
        linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-end) 100%);
      font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", sans-serif;
      color: var(--headline);
      display: flex;
      flex-direction: column;
    }

    /* PAGE WRAPPER */
    .page-wrapper {
      max-width: 980px;
      width: 100%;
      margin: 2rem auto 4rem auto;
      padding: 0 1.25rem 4rem;
    }

    /* MAIN CARD */
    .about-card {
      background: var(--page-bg);
      border-radius: var(--radius-card);
      box-shadow: var(--shadow-card);
      overflow: hidden;
      border: 1px solid rgba(255,255,255,0.18);
    }

    /* PURPLE HEADER STRIP */
    .about-header {
      background:
        radial-gradient(circle at 50% 20%, rgba(255,255,255,0.12) 0%, rgba(0,0,0,0) 70%),
        linear-gradient(135deg, #4a3a80 0%, #7a5dad 60%, #5a4a92 100%);
      color: #fff;
      padding: 2rem 1rem 1.5rem;
      border-top-left-radius: var(--radius-card);
      border-top-right-radius: var(--radius-card);
      position: relative;
      border: 1px solid rgba(255,255,255,0.35);
      border-bottom: none;
    }

    .about-header-inner {
      max-width: 800px;
      margin: 0 auto;
      text-align: center;
    }

    .about-header h1 {
      margin: 0;
      font-family: 'Playfair Display', serif;
      font-size: 1.8rem;
      line-height: 1.2;
      font-weight: 600;
      letter-spacing: -0.02em;
      color: #fff;
    }

    .about-header .subtitle {
      margin-top: 0.4rem;
      font-size: 0.8rem;
      font-weight: 500;
      letter-spacing: 0.15em;
      text-transform: uppercase;
      color: rgba(255,255,255,0.85);
    }

    /* BODY AREA */
    .about-body {
      padding: 2rem 1.25rem 2.5rem;
      background: var(--page-bg);
    }
    @media(min-width: 600px) {
      .about-body {
        padding: 2rem 2rem 2.5rem;
      }
    }

    /* TAB BAR under header */
    .info-tabs {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 2rem;
      margin-bottom: 2rem;
    }

    .tab-btn {
      border: none;
      background: transparent;
      cursor: pointer;
      padding: 0;
      font-family: 'Playfair Display', serif;
      font-size: 1.1rem;
      line-height: 1.2;
      font-weight: 500;
      color: var(--inactive-tab-text);
      display: inline-flex;
      align-items: center;
    }

    .tab-btn.active {
      background: var(--accent-pill-bg);
      color: var(--accent-pill-text);
      border-radius: var(--radius-lg);
      padding: 0.9rem 1rem;
      position: relative;
      box-shadow: 0 18px 24px rgba(80,43,141,0.18);
    }

    .tab-btn.active::after {
      content: "";
      position: absolute;
      left: 0.75rem;
      right: 0.75rem;
      bottom: 0.4rem;
      height: 3px;
      border-radius: 999px;
      background: var(--accent-pill-border);
    }

    /* CONTENT SECTIONS */
    .about-section {
      display: none;
      animation: fade 0.3s ease;
    }
    .about-section.active {
      display: block;
    }

    @keyframes fade {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    /* Title + text blocks */
    .section-headline {
      text-align: center;
      max-width: 820px;
      margin: 0 auto 2rem;
    }

    .section-headline h2 {
      margin: 0 0 1rem;
      font-family: 'Playfair Display', serif;
      font-size: clamp(1.9rem, 1vw + 1.6rem, 2.2rem);
      font-weight: 600;
      line-height: 1.25;
      color: var(--headline);
    }

    .section-headline .subline {
      font-size: 1rem;
      line-height: 1.5;
      font-weight: 400;
      color: var(--subtext);
    }

    /* Inner white cards */
    .info-card {
      background: var(--panel-bg);
      border-radius: 14px;
      box-shadow: var(--shadow-inner-card);
      border: 1px solid var(--panel-border);
      padding: 1.5rem 1.25rem 2rem;
      max-width: 90%;
      margin: 0 auto 2rem;
    }
    @media(min-width: 600px) {
      .info-card {
        padding: 2rem 2rem 2.25rem;
      }
    }

    .info-card h3 {
      margin-top: 0;
      margin-bottom: 1.25rem;
      font-family: 'Playfair Display', serif;
      font-size: clamp(1.4rem, 0.4vw + 1.2rem, 1.6rem);
      line-height: 1.3;
      font-weight: 600;
      color: var(--headline);
      border-bottom: 2px solid var(--accent-pill-border);
      padding-bottom: 0.75rem;
    }

    .subsection-title {
      font-family: 'Playfair Display', serif;
      font-size: 1.05rem;
      font-weight: 600;
      color: var(--accent-pill-text);
      margin-bottom: 0.75rem;
    }

    .info-card p {
      margin: 0 0 1rem;
      font-size: 0.95rem;
      line-height: 1.55;
      color: var(--bodytext);
    }

    .info-card p:last-child {
      margin-bottom: 0;
    }

    /* Team layout */
    .team-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 1.5rem;
      margin-top: 1.5rem;
    }
    @media(min-width:700px){
      .team-grid {
        grid-template-columns: repeat(3, 1fr);
      }
    }

    .team-card {
      background: #fff;
      border: 1px solid var(--panel-border);
      border-radius: var(--radius-sm);
      padding: 1rem 1rem 1.25rem;
      box-shadow: 0 16px 28px rgba(0,0,0,0.06);
      text-align: left;
    }

    .member-role {
      font-size: .8rem;
      font-weight: 600;
      color: var(--accent-pill-text);
      margin-bottom: .4rem;
      text-transform: uppercase;
      letter-spacing: .06em;
    }

    .member-name {
      font-family: 'Playfair Display', serif;
      font-size: 1.05rem;
      font-weight: 600;
      color: var(--headline);
      line-height: 1.3;
      margin-bottom: .5rem;
    }

    .member-desc {
      font-size: .9rem;
      line-height: 1.5;
      color: var(--bodytext);
    }

    .spacer-xl {
      height: 3rem;
    }

    /* FEEDBACK BUTTON */
    .feedback-btn {
      position: fixed;
      bottom: 30px;
      right: 30px;
      background: linear-gradient(135deg, var(--accent-pill-border), #9d7eff);
      color: white;
      border: none;
      border-radius: 50px;
      padding: 15px 25px;
      font-weight: 600;
      font-size: 0.9rem;
      cursor: pointer;
      box-shadow: 0 8px 25px rgba(122, 97, 173, 0.4);
      transition: all 0.3s ease;
      z-index: 1000;
    }

    .feedback-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 30px rgba(122, 97, 173, 0.6);
    }

    /* MODAL STYLES */
    .modal-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.7);
      backdrop-filter: blur(5px);
      z-index: 2000;
      animation: fadeIn 0.3s ease;
    }

    .modal-content {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: var(--page-bg);
      border-radius: 20px;
      box-shadow: var(--shadow-card);
      width: 90%;
      max-width: 600px;
      max-height: 90vh;
      overflow-y: auto;
      animation: slideUp 0.3s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @keyframes slideUp {
      from { 
        opacity: 0;
        transform: translate(-50%, -40%);
      }
      to { 
        opacity: 1;
        transform: translate(-50%, -50%);
      }
    }

    .modal-header {
      background: linear-gradient(135deg, #4a3a80, #7a5dad);
      color: white;
      padding: 1.5rem 2rem;
      border-top-left-radius: 20px;
      border-top-right-radius: 20px;
      text-align: center;
    }

    .modal-header h2 {
      margin: 0;
      font-family: 'Playfair Display', serif;
      font-size: 1.5rem;
    }

    .modal-body {
      padding: 2rem;
    }

    .close-modal {
      position: absolute;
      top: 15px;
      right: 20px;
      background: none;
      border: none;
      color: white;
      font-size: 1.5rem;
      cursor: pointer;
      padding: 0;
      width: 30px;
      height: 30px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .close-modal:hover {
      color: #ffd700;
    }

    /* Form styles for modal */
    .field-group {
      margin-bottom: 1.5rem;
    }

    .field-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: var(--headline);
    }

    .field-group select,
    .field-group input,
    .field-group textarea {
      width: 100%;
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-family: inherit;
      font-size: 0.9rem;
      transition: border 0.3s ease;
    }

    .field-group select:focus,
    .field-group input:focus,
    .field-group textarea:focus {
      outline: none;
      border-color: var(--accent-pill-border);
      box-shadow: 0 0 0 3px rgba(122, 97, 173, 0.1);
    }

    .submit-btn {
      width: 100%;
      background: linear-gradient(135deg, var(--accent-pill-border), #9d7eff);
      color: white;
      border: none;
      border-radius: 8px;
      padding: 15px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .submit-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(122, 97, 173, 0.4);
    }

    .message {
      text-align: center;
      background: #e8f5e8;
      color: #2d5016;
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 1rem;
      font-weight: 600;
    }

        /* Hide scrollbars */
    body::-webkit-scrollbar {
        display: none;
    }
  </style>
</head>
<body>

  <!-- Your existing navbar is included via PHP -->

  <!-- PAGE CONTENT -->
  <main class="page-wrapper">

    <section class="about-card">
      <!-- Top purple band -->
      <div class="about-header">
        <div class="about-header-inner">
          <h1>CELESTICARE</h1>
          <div class="subtitle">ABOUT US</div>
        </div>
      </div>

      <!-- Body -->
      <div class="about-body">

        <!-- Tabs -->
        <div class="info-tabs">
          <button class="tab-btn active" data-tab="who">
            Who We Are
          </button>
          <button class="tab-btn" data-tab="team">
            Our Team
          </button>
        </div>

        <!-- SECTION: WHO WE ARE -->
        <section id="who" class="about-section active">
          <div class="section-headline">
            <h2>Style Written in the Stars</h2>
            <div class="subline">
              Celesticare blends astrology and fashion to help you express who you are —
              not just what's trending. We create personalized style guidance based on your
              zodiac traits, energy, and aesthetic identity.
            </div>
          </div>

          <article class="info-card">
            <h3>Our Purpose</h3>

            <div class="subsection-title">Fashion Meets Self-Discovery</div>
            <p>
              We believe style should feel personal. Instead of asking
              "What's popular right now?" we ask "What feels like you?"
              Celesticare turns your birth details and sign traits into wearable
              inspiration — outfits, palettes, and aesthetics you actually connect with.
            </p>

            <p>
              Through our platform, users can create a profile, share birth information,
              and receive curated outfit and beauty suggestions inspired by their sign.
              From daily looks to special moments, we aim to make getting dressed feel
              intentional, confident, and meaningful.
            </p>

            <div class="subsection-title" style="margin-top:2rem;">Mission</div>
            <p>
              To empower individuality through a thoughtful blend of astrology and fashion —
              helping people express their real selves, not just follow trends.
            </p>

            <div class="subsection-title" style="margin-top:2rem;">Vision</div>
            <p>
              To become the go-to space for cosmic styling:
              a platform where identity, creativity, and wardrobe come together.
            </p>
          </article>
        </section>

        <!-- SECTION: TEAM -->
        <section id="team" class="about-section">
          <div class="section-headline">
            <h2>Meet the Team</h2>
            <div class="subline">
              The people shaping Celesticare and building the experience.
            </div>
          </div>

          <article class="info-card">
            <h3>Core Team</h3>

            <div class="team-grid">
              <!-- Member 1 -->
              <div class="team-card">
                <div class="member-role">Project Manager, UI Designer</div>
                <div class="member-name">Ma. Monica Laca Deloa</div>
                <div class="member-desc">
                  Leads vision, user experience, and delivery. Aligns timelines,
                  coordinates the team, and shapes the interface so Celesticare
                  feels intuitive, elegant, and emotionally resonant.
                </div>
              </div>

              <!-- Member 2 -->
              <div class="team-card">
                <div class="member-role">Systems Analyst, Main Developer, UI/UX Programmer, Database Admin</div>
                <div class="member-name">Elle Skye Babao</div>
                <div class="member-desc">
                  Turns ideas into working features. Designs system logic,
                  builds the functionality, and makes sure astrology-driven
                  style suggestions actually come to life in the product.
                </div>
              </div>

              <!-- Member 3 -->
              <div class="team-card">
                <div class="member-role">Documentation Specialist</div>
                <div class="member-name">Bryan Josef Sarmiento</div>
                <div class="member-desc">
                  Maintains clarity across the project. Creates and organizes
                  documentation, diagrams, and reports so the system
                  stays understandable, scalable, and future-ready.
                </div>
              </div>
            </div>
          </article>
        </section>

      </div>
    </section>

    <div class="spacer-xl"></div>
  </main>

  <!-- FEEDBACK BUTTON -->
  <button class="feedback-btn" onclick="openFeedbackModal()">
    <i class="fas fa-comment-dots"></i> Give Feedback
  </button>

  <!-- FEEDBACK MODAL -->
  <div id="feedbackModal" class="modal-overlay">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Share Your Experience</h2>
        <button class="close-modal" onclick="closeFeedbackModal()">&times;</button>
      </div>
      <div class="modal-body">
        <?php if (isset($message)): ?>
          <div class="message"><?= $message ?></div>
        <?php endif; ?>
        
        <form method="POST" action="about.php">
          <div class="field-group">
            <label>Overall Experience</label>
            <select name="experience" required>
              <option value="">Select your experience</option>
              <option value="5">Excellent ⭐⭐⭐⭐⭐</option>
              <option value="4">Great ⭐⭐⭐⭐</option>
              <option value="3">Average ⭐⭐⭐</option>
              <option value="2">Fair ⭐⭐</option>
              <option value="1">Poor ⭐</option>
            </select>
          </div>

          <div class="field-group">
            <label>Did the fashion suggestions match your vibe?</label>
            <select name="fashion_match" required>
              <option value="">Select an option</option>
              <option>Very accurate</option>
              <option>Somewhat</option>
              <option>Not really</option>
            </select>
          </div>

          <div class="field-group">
            <label>Favorite Feature</label>
            <select name="favorite_feature" required>
              <option value="">Select your favorite</option>
              <option>Color Analysis</option>
              <option>Fashion Forecast</option>
              <option>Aesthetic Quiz</option>
              <option>Virtual Style Studio</option>
            </select>
          </div>

          <div class="field-group">
            <label>Describe the website's vibe in one word</label>
            <input type="text" name="vibe" placeholder="e.g., Magical, Chic, Inspiring" required>
          </div>

          <div class="field-group">
            <label>Any suggestions or wishes?</label>
            <textarea name="suggestions" placeholder="Tell us what you'd love to see..."></textarea>
          </div>

          <button type="submit" class="submit-btn">Submit Feedback</button>
        </form>
      </div>
    </div>
  </div>

  <script>
    // Tab functionality
    const tabButtons = document.querySelectorAll('.tab-btn');
    const sections = document.querySelectorAll('.about-section');

    tabButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        tabButtons.forEach(b => b.classList.remove('active'));
        sections.forEach(sec => sec.classList.remove('active'));

        btn.classList.add('active');
        const targetId = btn.getAttribute('data-tab');
        const targetSection = document.getElementById(targetId);
        if (targetSection) {
          targetSection.classList.add('active');
        }
      });
    });

    // Modal functionality
    function openFeedbackModal() {
      document.getElementById('feedbackModal').style.display = 'block';
      document.body.style.overflow = 'hidden';
    }

    function closeFeedbackModal() {
      document.getElementById('feedbackModal').style.display = 'none';
      document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside
    document.getElementById('feedbackModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeFeedbackModal();
      }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeFeedbackModal();
      }
    });

    // Auto-open modal if there's a message (after form submission)
    <?php if (isset($message)): ?>
      setTimeout(openFeedbackModal, 500);
    <?php endif; ?>
  </script>

</body>
</html>