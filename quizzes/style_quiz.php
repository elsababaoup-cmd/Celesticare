<?php
session_start();    
include("../includes/navbar.php");
include("../config/dbconfig.php");

// --- Fetch user gender from DB ---
$user_id = $_SESSION['user_id'] ?? null;
$gender = null;

if ($user_id) {
    $stmt = $conn->prepare("SELECT gender FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($gender);
    $stmt->fetch();
    $stmt->close();
}

// --- Normalize gender + default ---
$gender = strtolower(trim($gender ?? ''));
if (!$gender || !in_array($gender, ['feminine', 'masculine'])) {
    $gender = 'feminine'; // default
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Build Your Look | CelestiCare</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    html, body { height:100%; background: linear-gradient(135deg,#615564 0%,#3c314b 100%); font-family:'Poppins',sans-serif; color:#fff; overflow-x:hidden; }
    .container-stylequiz { padding:60px 40px 100px; position:relative; z-index:2; }
    h1 { text-align:center; font-weight:700; font-size:2.5rem; color:#d4c6ff; text-shadow:0 0 10px rgba(255,255,255,0.2); margin-bottom:40px; }

    .quiz-layout { display:flex; justify-content:center; align-items:flex-start; gap:80px; flex-wrap:wrap; }
    .canvas-section { display:flex; flex-direction:column; align-items:center; gap:15px; }
    .canvas { position:relative; width:340px; height:800px; background: rgba(255,255,255,0.05); border-radius:25px; box-shadow:0 8px 25px rgba(255,255,255,0.1); overflow:hidden; }
    .mannequin { width:100%; height:680px; object-fit:contain; position:absolute; top:0; left:0; pointer-events:none; z-index:1; }
    .dropped-item { position:absolute; z-index:2; cursor:grab; width:320px; user-select:none; transition:transform 0.15s ease; }
    .dropped-item:active { cursor:grabbing; transform:scale(1.05); }

    .clothing-section { display:flex; flex-direction:column; align-items:stretch; width:100%; max-width:950px; }
    .clothing-container { width:100%; background: rgba(20,10,30,0.9); padding:25px 35px; border-radius:25px; box-shadow:0 8px 25px rgba(255,255,255,0.1); overflow:hidden; }
    .clothing-container h2 { text-align:center; font-weight:600; font-size:1.5rem; color:#dcd3ff; margin-bottom:25px; }
    .category { margin-bottom:25px; }
    .category h3 { background: rgba(255,255,255,0.08); padding:10px 15px; border-radius:15px; color:#cbbfff; font-size:1.1rem; display:flex; justify-content:space-between; align-items:center; cursor:pointer; }
    .category h3 .arrow { display:inline-block; transition:transform 0.3s ease; }
    .category-content { display:grid; grid-template-columns:repeat(auto-fill,minmax(100px,1fr)); gap:15px; max-height:0; overflow:hidden; transition:max-height 0.3s ease,padding 0.3s ease; padding:0; }
    .category-content.open { max-height:300px; padding:15px 0 0 0; overflow-y:auto; }
    .category-content::-webkit-scrollbar { width:6px; }
    .category-content::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius:3px; }
    .category-content::-webkit-scrollbar-track { background:transparent; }

    .item { 
      width:auto; /* allow natural width */
      height:120px; /* give them a taller container for bottoms */
      padding:5px; /* optional */
      display:flex; 
      justify-content:center; 
      align-items:center; 
    }

    .item img { 
      max-width:100%; 
      max-height:100%; 
      object-fit:contain; 
    }

    .item:hover { transform:scale(1.1); background: rgba(255,255,255,0.15); }

    button.toggle-btn, #finishBtn, #resetBtn { background:#bba6ff; color:#222; font-weight:600; border:none; border-radius:30px; transition:all 0.3s ease; font-size:1rem; padding:10px 25px; cursor:pointer; }
    button.toggle-btn:hover, #finishBtn:hover, #resetBtn:hover { background:#d4c6ff; transform:translateY(-3px); }
    #finishBtn { display:block; margin:60px auto 0; padding:12px 35px; font-size:1.05rem; }
    #resetBtn { margin-top:10px; }

    .bg-accent { position:absolute; border-radius:50%; filter:blur(120px); opacity:0.3; z-index:0; animation:float 10s ease-in-out infinite alternate; }
    .bg-accent.one { background:#bba6ff; width:400px; height:400px; top:-80px; left:-80px; }
    .bg-accent.two { background:#d2b0ff; width:500px; height:500px; bottom:-100px; right:-100px; }
    @keyframes float { from{transform:translateY(0);} to{transform:translateY(25px);} }
    html::-webkit-scrollbar, body::-webkit-scrollbar { width:0!important; height:0!important; background:transparent; }
  </style>
</head>
<body>
  <div class="bg-accent one"></div>
  <div class="bg-accent two"></div>

  <div class="container-stylequiz">
    <h1> Dress Your Model </h1>

    <div class="quiz-layout">
      <!-- Left: mannequin -->
      <div class="canvas-section">
        <button id="toggleGender" class="toggle-btn">Switch to Masculine</button>
        <div class="canvas" id="canvas">
          <img id="mannequin" class="mannequin" src="">
        </div>
        <button id="resetBtn">Reset</button>
      </div>

      <!-- Right: clothing -->
      <div class="clothing-section">
        <button id="toggleClothes" class="toggle-btn">Show Masculine Clothes</button>
        <div class="clothing-container">
          <h2>Choose Your Pieces</h2>

          <div class="category">
            <h3>Tops <span class="arrow">▶</span></h3>
            <div class="category-content" id="tops"></div>
          </div>
          <div class="category">
            <h3>Bottoms <span class="arrow">▶</span></h3>
            <div class="category-content" id="bottoms"></div>
          </div>
          <div class="category" id="dresses-category" style="display:none;">
            <h3>Dresses <span class="arrow">▶</span></h3>
            <div class="category-content" id="dresses"></div>
          </div>
          <div class="category">
            <h3>Shoes <span class="arrow">▶</span></h3>
            <div class="category-content" id="shoes"></div>
          </div>
          <div class="category">
            <h3>Accessories <span class="arrow">▶</span></h3>
            <div class="category-content" id="accessories"></div>
          </div>
        </div>
      </div>
    </div>

    <button id="finishBtn">See My Style</button>
  </div>

<script>
const mannequin = document.getElementById('mannequin');
const canvas = document.getElementById('canvas');
const toggleGenderBtn = document.getElementById('toggleGender');
const toggleClothesBtn = document.getElementById('toggleClothes');
const resetBtn = document.getElementById('resetBtn');

let currentGender = "<?php echo $gender === 'masculine' ? 'masc' : 'fem'; ?>";
let currentClothingSet = currentGender;

const limits = { tops: 2, bottoms: 2, shoes: 1, accessories: 4, dresses: 1 };

const clothingSets = {
  fem: {
    tops: [
      { src: 'assets/fem/bluetop1.png', style: 'minimalist' },
      { src: 'assets/fem/businesstop1.png', style: 'businesswear' },
      { src: 'assets/fem/basict.png', style: 'minimalist' },
      { src: 'assets/fem/redelegant.png', style: 'elegant' },
      { src: 'assets/fem/creativepink.png', style: 'creative' },
      { src: 'assets/fem/businesscasual.png', style: 'businesswear' },
      { src: 'assets/fem/pinksoft.png', style: 'soft' },
      { src: 'assets/fem/elegantwhite.png', style: 'elegant' },
      { src: 'assets/fem/businessblack.png', style: 'businesswear' },
      { src: 'assets/fem/polo.png', style: 'rough' },
      { src: 'assets/fem/brownelegant.png', style: 'elegant' },
      { src: 'assets/fem/whitesoft.png', style: 'soft' },
      { src: 'assets/fem/blackpunk.png', style: 'rough' },
      { src: 'assets/fem/blackcreative.png', style: 'creative' },
      { src: 'assets/fem/blackgoth.png', style: 'soft' },
      { src: 'assets/fem/blacksexy.png', style: 'elegant' },
      { src: 'assets/fem/creativepolo.png', style: 'businesswear' },
      { src: 'assets/fem/cutet.png', style: 'rough' },
      { src: 'assets/fem/blueelegant.png', style: 'elegant' },
      { src: 'assets/fem/creativesexy.png', style: 'soft' },
      { src: 'assets/fem/greenelegant.png', style: 'elegant' },
    ],
    bottoms: [
      { src: 'assets/fem/Untitled18_20251019095525.png', style: 'elegant' },
      { src: 'assets/fem/Untitled18_20251019095553.png', style: 'businesswear' },
      { src: 'assets/fem/Untitled18_20251019095630.png', style: 'soft' },
      { src: 'assets/fem/Untitled18_20251019102742.png', style: 'businesswear' },
      { src: 'assets/fem/Untitled18_20251019102842.png', style: 'businesswear' },
      { src: 'assets/fem/Untitled18_20251019102847.png', style: 'rough' },
      { src: 'assets/fem/Untitled18_20251019102851.png', style: 'businesswear' },
      { src: 'assets/fem/Untitled18_20251019102901.png', style: 'minimalist' },
      { src: 'assets/fem/Untitled18_20251019102907.png', style: 'rough' },
      { src: 'assets/fem/Untitled18_20251019102912.png', style: 'creative' },
      { src: 'assets/fem/Untitled18_20251019102916.png', style: 'creative' },
      { src: 'assets/fem/Untitled18_20251019102923.png', style: 'minimalist' },
      { src: 'assets/fem/Untitled18_20251019102929.png', style: 'elegant' },
      { src: 'assets/fem/Untitled18_20251019102936.png', style: 'elegant' },
      { src: 'assets/fem/Untitled18_20251019102942.png', style: 'soft' },
      { src: 'assets/fem/Untitled18_20251019102949.png', style: 'soft' },
      { src: 'assets/fem/Untitled18_20251019102956.png', style: 'creative' },
      { src: 'assets/fem/Untitled18_20251019103008.png', style: 'creative' },
      { src: 'assets/fem/Untitled18_20251019103016.png', style: 'soft' },
      { src: 'assets/fem/Untitled18_20251019103025.png', style: 'soft' },
      { src: 'assets/fem/Untitled18_20251019103109.png', style: 'creative' },
    ],
    dresses: [
      { src: 'assets/fem/Untitled18_20251019111152.png', style: 'rough' },
      { src: 'assets/fem/Untitled18_20251019111202.png', style: 'rough' },
      { src: 'assets/fem/Untitled18_20251019111209.png', style: 'elegant' },
      { src: 'assets/fem/Untitled18_20251019111214.png', style: 'soft' },
      { src: 'assets/fem/Untitled18_20251019111218.png', style: 'elegant' },
      { src: 'assets/fem/Untitled18_20251019111226.png', style: 'minimalist' },
      { src: 'assets/fem/Untitled18_20251019111232.png', style: 'businesswear' },
      { src: 'assets/fem/Untitled18_20251019111157.png', style: 'minimalist' },
      { src: 'assets/fem/Untitled18_20251019115538.png', style: 'rough' },
      { src: 'assets/fem/Untitled18_20251019115546.png', style: 'businesswear' },
      { src: 'assets/fem/Untitled18_20251019115557.png', style: 'creative' },
      { src: 'assets/fem/Untitled18_20251019115604.png', style: 'businesswear' },
      { src: 'assets/fem/Untitled18_20251019115608.png', style: 'cr' },
      { src: 'assets/fem/Untitled18_20251019115615.png', style: 'soft' },
    ],
    shoes: [
      { src: 'assets/fem/etc/shoe.png', style: 'elegant' },
      { src: 'assets/fem/etc/shoe1.png', style: 'businesswear' },
      { src: 'assets/fem/etc/shoe2.png', style: 'rough' },
      { src: 'assets/fem/etc/shoe3.png', style: 'minimalist' },
      { src: 'assets/fem/etc/shoe4.png', style: 'elegant' },
      { src: 'assets/fem/etc/shoe5.png', style: 'creative' },
      { src: 'assets/fem/etc/shoe6.png', style: 'rough' },
      { src: 'assets/fem/etc/shoe7.png', style: 'soft' },
      { src: 'assets/fem/etc/shoe8.png', style: 'elegant' },
      { src: 'assets/fem/etc/shoe9.png', style: 'creative' },
      { src: 'assets/fem/etc/shoe10.png', style: 'businesswear' },
      { src: 'assets/fem/etc/shoe11.png', style: 'businesswear' },
      { src: 'assets/fem/etc/shoe12.png', style: 'soft' },
      { src: 'assets/fem/etc/shoe13.png', style: 'minimalist' },
      { src: 'assets/fem/etc/shoe14.png', style: 'creative' },
      { src: 'assets/fem/etc/shoe15.png', style: 'businesswear' },
      { src: 'assets/fem/etc/shoe16.png', style: 'minimalist' },
      { src: 'assets/fem/etc/shoe17.png', style: 'rough' },
      { src: 'assets/fem/etc/shoe18.png', style: 'minmalist' },
      { src: 'assets/fem/etc/shoe19.png', style: 'minimalist' },
      { src: 'assets/fem/etc/shoe20.png', style: 'minimalist' },                       
    ],
    accessories: [
      { src: 'assets/fem/etc/Untitled16_20251019104102.png', style: 'elegant' },
      { src: 'assets/fem/etc/Untitled16_20251019104120.png', style: 'creative' },
      { src: 'assets/fem/etc/Untitled16_20251019123320.png', style: 'creative' },
      { src: 'assets/fem/etc/Untitled16_20251019123402.png', style: 'rough' },
      { src: 'assets/fem/etc/Untitled16_20251019123406.png', style: 'elegant' },
      { src: 'assets/fem/etc/Untitled16_20251019123410.png', style: 'minimalist' },
      { src: 'assets/fem/etc/Untitled16_20251019123415.png', style: 'creative' },
      { src: 'assets/fem/etc/Untitled16_20251019123422.png', style: 'creative' },
      { src: 'assets/fem/etc/Untitled16_20251019123429.png', style: 'creative' },
      { src: 'assets/fem/etc/Untitled16_20251019123437.png', style: 'soft' },
      { src: 'assets/fem/etc/Untitled16_20251019123443.png', style: 'elegant' },
      { src: 'assets/fem/etc/Untitled16_20251019123517.png', style: 'creative' },
      { src: 'assets/fem/etc/Untitled16_20251019123527.png', style: 'businesswear' },
      { src: 'assets/fem/etc/Untitled16_20251019123531.png', style: 'creative' },
      { src: 'assets/fem/etc/Untitled16_20251019123535.png', style: 'creative' },
      { src: 'assets/fem/etc/Untitled16_20251019123543.png', style: 'minimalist' },
      { src: 'assets/fem/etc/Untitled16_20251019123549.png', style: 'rough' },
      { src: 'assets/fem/etc/Untitled16_20251019123555.png', style: 'elegant' },
      { src: 'assets/fem/etc/Untitled16_20251019123606.png', style: 'elegant' },
      { src: 'assets/fem/etc/Untitled16_20251019123633.png', style: 'rough' },
      { src: 'assets/fem/etc/Untitled19_20251019123901.png', style: 'minmalist' },
      { src: 'assets/fem/etc/Untitled19_20251019123905.png', style: 'rough' },
      { src: 'assets/fem/etc/Untitled19_20251019123909.png', style: 'soft' },
      { src: 'assets/fem/etc/Untitled19_20251019123915.png', style: 'rough' },
      { src: 'assets/fem/etc/Untitled19_20251019123931.png', style: 'elegant' },
      { src: 'assets/fem/etc/Untitled19_20251019123936.png', style: 'elegant' },                 

    ]
  },
  masc: {
    tops: [
      { src: 'assets/masc_tops/streetwear_top.png', style: 'streetwear' }
    ],
    bottoms: [
      { src: 'assets/masc_bottoms/pants.png', style: 'minimalist' }
    ],
    shoes: [
      { src: 'assets/masc_shoes/sneakers.png', style: 'streetwear' },
      { src: 'assets/masc_shoes/formal_shoes.png', style: 'elegant' }
    ],
    accessories: []
  }
};

// Load mannequin
function loadMannequin() {
  mannequin.src = currentGender === 'fem' ? '../quizzes/assets/fem_mannequin.png' : '../quizzes/assets/masc_mannequin.png';
  toggleGenderBtn.textContent = currentGender === 'fem' ? 'Switch to Masculine' : 'Switch to Feminine';
}

// Load clothing items
function loadClothing() {
  ['tops','bottoms','shoes','accessories','dresses'].forEach(cat => {
    const container = document.getElementById(cat);
    container.innerHTML = '';
    const items = clothingSets[currentClothingSet][cat] || [];
    items.forEach(item => {
      const img = document.createElement('img');
      img.src = item.src;
      img.dataset.style = item.style;
      img.className = 'item';
      img.draggable = true;
      container.appendChild(img);
    });
  });

  enableDrag();

  toggleClothesBtn.textContent = currentClothingSet === 'fem' ? 'Show Masculine Clothes' : 'Show Feminine Clothes';
  document.getElementById('dresses-category').style.display = currentClothingSet === 'fem' ? 'block' : 'none';
}

// Enable drag for clothing items
function enableDrag() {
  document.querySelectorAll('.item').forEach(item => {
    item.addEventListener('dragstart', e => {
      e.dataTransfer.setData('src', item.src);
      e.dataTransfer.setData('style', item.dataset.style);
    });
  });
}

// Get category from src
function getCategoryFromSrc(src) {
  for (const cat in clothingSets[currentClothingSet]) {
    if (clothingSets[currentClothingSet][cat].some(i => i.src === src)) return cat;
  }
  return null;
}

// Drop clothing on mannequin
canvas.addEventListener('dragover', e => e.preventDefault());
canvas.addEventListener('drop', e => {
  e.preventDefault();
  const src = e.dataTransfer.getData('src');
  const style = e.dataTransfer.getData('style');
  if (!src) return;

  const category = getCategoryFromSrc(src);
  const count = canvas.querySelectorAll(`.dropped-item[data-category="${category}"]`).length;

  if (count >= limits[category]) {
    alert(`You can only add ${limits[category]} ${category}`);
    return;
  }

  const img = document.createElement('img');
  img.src = src;
  img.className = 'dropped-item';
  img.style.left = `${e.offsetX - 90}px`;
  img.style.top = `${e.offsetY - 90}px`;
  img.dataset.style = style;
  img.dataset.category = category;

  canvas.appendChild(img);

  // Right-click to remove
  img.addEventListener('contextmenu', e => { e.preventDefault(); img.remove(); });

  // Drag within canvas
  let isDragging = false;
  img.addEventListener('mousedown', e => {
    e.preventDefault();
    isDragging = true;
    const shiftX = e.clientX - img.getBoundingClientRect().left;
    const shiftY = e.clientY - img.getBoundingClientRect().top;

    function moveAt(pageX, pageY) {
      if (!isDragging) return;
      img.style.left = pageX - shiftX - canvas.getBoundingClientRect().left + 'px';
      img.style.top = pageY - shiftY - canvas.getBoundingClientRect().top + 'px';
    }

    function onMouseMove(e) { moveAt(e.pageX, e.pageY); }
    document.addEventListener('mousemove', onMouseMove);
    img.addEventListener('mouseup', () => {
      isDragging = false;
      document.removeEventListener('mousemove', onMouseMove);
    }, { once: true });
  });
});

// Reset button
resetBtn.addEventListener('click', () => {
  canvas.querySelectorAll('.dropped-item').forEach(i => i.remove());
});

// Toggle mannequin gender
toggleGenderBtn.addEventListener('click', () => {
  currentGender = currentGender === 'fem' ? 'masc' : 'fem';
  currentClothingSet = currentGender;
  loadMannequin();
  loadClothing();
});

// Toggle clothing set
toggleClothesBtn.addEventListener('click', () => {
  currentClothingSet = currentClothingSet === 'fem' ? 'masc' : 'fem';
  loadClothing();
});

// Collapsible categories
document.querySelectorAll('.category h3').forEach(header => {
  const content = header.nextElementSibling;
  content.classList.remove('open');
  header.addEventListener('click', () => {
    content.classList.toggle('open');
    const arrow = header.querySelector('.arrow');
    arrow.style.transform = content.classList.contains('open') ? 'rotate(90deg)' : 'rotate(0deg)';
  });
});

// Finish button
document.getElementById('finishBtn').addEventListener('click', () => {
  const items = canvas.querySelectorAll('.dropped-item');
  if (!items.length) return alert("Try dressing your mannequin first!");

  const styleCounter = {};
  items.forEach(i => { styleCounter[i.dataset.style] = (styleCounter[i.dataset.style] || 0) + 1; });
  const topStyle = Object.entries(styleCounter).sort((a, b) => b[1] - a[1])[0]?.[0] || items[0].dataset.style;

  localStorage.setItem('styleResult', topStyle);

  const droppedData = [];
  items.forEach(i => {
    droppedData.push({ src: i.src, style: i.dataset.style, category: i.dataset.category });
  });

  fetch('save_outfit.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      outfit_data: droppedData,
      gender: currentGender,
      clothing_style: topStyle
    })
  })
  .then(async res => {
    try {
      const data = await res.json();
      if (data.success) {
        // Redirect to style result page with style query
        window.location.href = `style_result.php?style=${topStyle}`;
      } else {
        alert('Failed to save your outfit.');
      }
    } catch (err) {
      alert('Server error: could not save outfit.');
      console.error(err);
    }
  })
  .catch(err => {
    alert('Network error.');
    console.error(err);
  });
});

// Initial load
loadMannequin();
loadClothing();
</script>
</body>
</html>