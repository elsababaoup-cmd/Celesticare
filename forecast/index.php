<?php
session_start();
include("../includes/navbar.php");

$season = isset($_GET['season']) && $_GET['season'] === 'ss' ? 'ss' : 'fw';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>CelestiCare — Fashion Forecast</title>
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

    * { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { 
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, var(--light-blue) 0%, var(--lavender) 100%);
      color: #333;
      overflow-x: hidden;
      min-height: 100vh;
    }

    .editorial-container {
      max-width: 1200px;
      margin: 2rem auto;
      background: rgba(255, 255, 255, 0.95);
      border-radius: 25px;
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
      overflow: hidden;
    }

    .magazine-header {
      background: linear-gradient(135deg, var(--primary-purple), var(--primary-dark));
      color: white;
      padding: 1.5rem 2rem;
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
      font-size: 3.5rem;
      font-weight: 800;
      margin-bottom: 0.5rem;
      text-transform: uppercase;
      letter-spacing: 2px;
    }

    .magazine-subtitle {
      font-size: 1.2rem;
      font-weight: 300;
      letter-spacing: 4px;
      color: #eaeaeaff;
      text-transform: uppercase;
      opacity: 0.9;
    }

    .main-content {
      padding: 1.5rem;
    }

    .season-tabs {
      display: flex;
      justify-content: center;
      gap: 4rem;
      margin: 2rem auto 3rem;
      user-select: none;
    }

    .season-tab {
      background: none;
      border: none;
      cursor: pointer;
      font-family: 'Playfair Display', serif;
      font-size: 1.8rem;
      font-weight: 600;
      color: var(--muted, #7a7a7a);
      padding: 1rem 2rem;
      border-radius: 12px;
      transition: all 0.3s ease;
      position: relative;
    }

    .season-tab[aria-selected="true"] {
      color: var(--primary-purple);
      background: rgba(145, 119, 192, 0.1);
    }

    .season-tab[aria-selected="true"]::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 80%;
      height: 3px;
      background: var(--primary-purple);
      border-radius: 2px;
    }

    .season-tab:hover {
      color: var(--primary-dark);
      transform: translateY(-2px);
    }

    .title-section {
      text-align: center;
      margin-bottom: 3rem;
      padding: 0 2rem;
    }

    .title {
      font-family: 'Playfair Display', serif;
      font-size: 3rem;
      font-weight: 700;
      line-height: 1.2;
      margin-bottom: 1rem;
      color: var(--dark);
    }

    .subtitle {
      font-size: 1.3rem;
      font-weight: 300;
      color: #666;
      line-height: 1.6;
    }

    .season {
      display: none;
      opacity: 0;
      transform: translateY(20px);
      transition: opacity 0.4s ease, transform 0.4s ease;
    }

    .season.is-active {
      display: block;
      opacity: 1;
      transform: none;
    }

    .region-section {
      background: white;
      border-radius: 15px;
      padding: 2.5rem;
      margin-bottom: 2.5rem;
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
      border-left: 4px solid var(--primary-purple);
    }

    .region-title {
      font-family: 'Playfair Display', serif;
      font-size: 2.2rem;
      font-weight: 700;
      color: var(--dark);
      margin-bottom: 2rem;
      border-bottom: 2px solid var(--accent);
      padding-bottom: 0.5rem;
    }

    h3 {
      font-family: 'Playfair Display', serif;
      font-size: 1.5rem;
      font-weight: 600;
      color: var(--primary-dark);
      margin: 2rem 0 1rem;
    }

    p {
      font-size: 1.1rem;
      line-height: 1.7;
      color: #555;
      margin-bottom: 1.5rem;
    }

    .color-highlight {
      color: var(--primary-purple);
      font-weight: 500;
    }

    @media (max-width: 1024px) {
      .editorial-container {
        margin: 1rem;
      }
      
      .magazine-title {
        font-size: 2.8rem;
      }
      
      .title {
        font-size: 2.5rem;
      }
      
      .season-tabs {
        gap: 2rem;
      }
      
      .season-tab {
        font-size: 1.5rem;
        padding: 0.8rem 1.5rem;
      }
    }

    @media (max-width: 768px) {
      .main-content {
        padding: 2rem;
      }
      
      .magazine-header {
        padding: 2rem 1rem;
      }
      
      .magazine-title {
        font-size: 2.2rem;
      }
      
      .magazine-subtitle {
        font-size: 1rem;
        letter-spacing: 2px;
      }
      
      .season-tabs {
        flex-direction: column;
        gap: 1rem;
      }
      
      .season-tab {
        font-size: 1.3rem;
        width: 100%;
        text-align: center;
      }
      
      .title {
        font-size: 2rem;
      }
      
      .subtitle {
        font-size: 1.1rem;
      }
      
      .region-section {
        padding: 2rem;
      }
      
      .region-title {
        font-size: 1.8rem;
      }
      
      h3 {
        font-size: 1.3rem;
      }
      
      p {
        font-size: 1rem;
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
  <div class="editorial-container">
    <!-- Magazine Header -->
    <header class="magazine-header">
      <h1 class="magazine-title">CelestiCare</h1>
      <p class="magazine-subtitle">Fashion Forecast</p>
    </header>

    <div class="main-content">
      <!-- Tabs -->
      <div class="season-tabs" role="tablist" aria-label="Choose season">
        <button class="season-tab" role="tab" id="tab-fw" data-target="fw" aria-selected="true">
          Fall-Winter 2025-2026
        </button>
        <button class="season-tab" role="tab" id="tab-ss" data-target="ss" aria-selected="false">
          Spring-Summer 2026-2027
        </button>
      </div>

      <!-- Title Section -->
      <div class="title-section">
        <h1 class="title" id="pageTitle">Fashion Forecast for Fall–Winter 2025–2026</h1>
        <p class="subtitle" id="pageSubtitle">Consumer Retail Trends for Menswear &amp; Womenswear</p>
      </div>

      <!-- FALL-WINTER -->
      <section id="season-fw" class="season is-active" aria-labelledby="tab-fw" role="tabpanel">
        <!-- Southeast Asia Section -->
        <div class="region-section">
          <h2 class="region-title">Southeast Asia: Modern Warmth Meets Heritage</h2>
          
          <h3>Color Palette &amp; Materials</h3>
          <p>Soft neutrals like <span class="color-highlight">khaki, rust, and navy</span> dominate, accented by <span class="color-highlight">teal, mustard, and terracotta</span> for festive vibrance. Breathable blends—cotton, bamboo knit, and upcycled cupro—stay practical for mild tropical winters. Sustainability continues to grow as local designers favor eco-conscious fabrics over synthetics.</p>

          <h3>Silhouettes &amp; Key Pieces</h3>
          <p>Outerwear remains light: <span class="color-highlight">linen trench coats, quilted vests, and airy cardigans</span> replace heavy coats. Womenswear leans on maxi dresses, draped ponchos, and belted wraps, while menswear emphasizes technical blazers and relaxed cargo jackets. Heritage influences appear through modernized batik, kebaya, and sarong-inspired cuts.</p>

          <h3>Cultural &amp; Retail Influences</h3>
          <p>Fashion drops align with <span class="color-highlight">Ramadan, Lunar New Year, and Christmas</span>, introducing color-rich capsules and detailed embroidery. TikTok Live and Shopee dominate festival shopping events, merging entertainment and retail.</p>

          <h3>Consumer &amp; Subculture Trends</h3>
          <p>Eco-conscious Gen Z shoppers embrace slow-fashion labels, though fast fashion remains strong. <span class="color-highlight">"Techno-traditional"</span> looks—like batik shirts with cooling tech—emerge, blending modern innovation with cultural pride.</p>
        </div>

        <!-- East Asia Section -->
        <div class="region-section">
          <h2 class="region-title">East Asia: Quiet Luxury &amp; Smart Tailoring</h2>
          
          <h3>Color Palette &amp; Materials</h3>
          <p>Rich yet restrained shades—<span class="color-highlight">Transformative Teal, Cocoa Brown, and Wax Paper Beige</span>—set a calm, futuristic tone. Fabrics lean toward recycled fleece, technical wool, and stretch cotton blends, marrying performance with elegance.</p>

          <h3>Silhouettes &amp; Key Pieces</h3>
          <p>Relaxed tailoring dominates: <span class="color-highlight">oversized blazers, wool coats, jogger trousers, and bias-cut midi dresses</span>. Layering is key—slip dresses over turtlenecks and sheer blouses under suits deliver seasonless versatility.</p>

          <h3>Cultural &amp; Retail Influences</h3>
          <p><span class="color-highlight">Hanbok-style coats and kimono jackets</span> appear in luxe modern fabrics. Retail tech evolves with AR fitting rooms and virtual showrooms, showcasing fashion's merge with digital innovation.</p>

          <h3>Consumer &amp; Subculture Trends</h3>
          <p>A rising preference for <span class="color-highlight">"quiet luxury"</span> drives sales of timeless, durable pieces. Gender-fluid outerwear and local-designer collabs thrive, supported by strong online communities on Weibo and LINE.</p>
        </div>
      </section>

      <!-- SPRING-SUMMER -->
      <section id="season-ss" class="season" aria-labelledby="tab-ss" role="tabpanel">
        <!-- Southeast Asia Section -->
        <div class="region-section">
          <h2 class="region-title">Southeast Asia: Tropical Ease &amp; Cultural Revival</h2>
          
          <h3>Color Palette &amp; Materials</h3>
          <p>Tropical and joyful tones—<span class="color-highlight">coral, mango, jade, and jelly mint</span>—lead the season. Lightweight linen, rayon, and organic cotton dominate. Designers refresh batik, ikat, and songket with modern patterns and airy textures.</p>

          <h3>Silhouettes &amp; Key Pieces</h3>
          <p>Fluid comfort rules: <span class="color-highlight">wrap dresses, culottes, wide-leg trousers, and loose jumpsuits</span>. Menswear features short-sleeve utility shirts, safari jackets, and coordinated sets. Gender-neutral cuts keep silhouettes breezy and versatile.</p>

          <h3>Cultural &amp; Retail Influences</h3>
          <p>Local crafts meet digital retail. <span class="color-highlight">Instagram shops and TikTok capsules</span> launch limited-edition festival collections. Fashion increasingly centers on everyday adaptability, rather than seasonal novelty.</p>

          <h3>Consumer &amp; Subculture Trends</h3>
          <p>Shoppers mix affordability with conscious consumption. <span class="color-highlight">Slow-fashion microbrands and artisanal studios</span> rise, while youth subcultures—K-pop fashion, surfwear, and street-inspired minimalism—drive everyday aesthetics.</p>
        </div>

        <!-- East Asia Section -->
        <div class="region-section">
          <h2 class="region-title">East Asia: Digital Romance &amp; Sustainable Luxury</h2>
          
          <h3>Color Palette &amp; Materials</h3>
          <p>A soft yet high-energy palette: <span class="color-highlight">Electric Fuchsia, Cherish Peach, Amber Haze, and Blue Aura</span>. Fabrics flow with silk, linen, organic cotton, and tech-infused mesh for lightness and sheen.</p>

          <h3>Silhouettes &amp; Key Pieces</h3>
          <p>The look is elegant and fluid—<span class="color-highlight">slip dresses, tunics, relaxed suits, and wide trousers</span> dominate. Menswear focuses on roomy tailoring and utility-inspired pieces with gentle structure.</p>

          <h3>Cultural &amp; Environmental Influences</h3>
          <p>Traditional motifs—<span class="color-highlight">qipao details, kabuki prints, kimono sleeves</span>—are modernized with minimalist refinement. Brands lean on vegan materials and natural dyes, highlighting sustainability as luxury.</p>

          <h3>Consumer &amp; Subculture Trends</h3>
          <p>Shoppers embrace <span class="color-highlight">gender-fluid dressing, livestream shopping, and digital lookbooks</span>. Brands like Li-Ning, Feng Chen Wang, and Auralee lead regional pride, merging heritage with high-tech modernity.</p>
        </div>
      </section>
    </div>
  </div>

  <script>
    (function(){
      const art = document.getElementById('artboard');
      const defaultSeason = '<?php echo $season; ?>' || 'fw';
      const tabs = Array.from(document.querySelectorAll('.season-tab'));
      const panels = {
        fw: document.getElementById('season-fw'),
        ss: document.getElementById('season-ss')
      };
      const pageTitle = document.getElementById('pageTitle');
      const pageSubtitle = document.getElementById('pageSubtitle');
      const titles = {
        fw: 'Fashion Forecast for Fall–Winter 2025–2026',
        ss: 'Fashion Forecast for Spring–Summer 2026–2027'
      };
      const subtitles = {
        fw: 'Consumer Retail Trends for Menswear & Womenswear',
        ss: 'Consumer Retail Trends for Menswear & Womenswear'
      };
      
      function activate(which){
        tabs.forEach(b => b.setAttribute('aria-selected', String(b.dataset.target === which)));
        Object.entries(panels).forEach(([k, el]) => el.classList.toggle('is-active', k === which));
        pageTitle.textContent = titles[which] || '';
        pageSubtitle.textContent = subtitles[which] || '';
        const url = new URL(window.location);
        url.searchParams.set('season', which);
        window.history.replaceState({}, '', url);
      }
      
      tabs.forEach(b => b.addEventListener('click', () => activate(b.dataset.target)));
      activate(defaultSeason);
    })();
  </script>
</body>
</html>