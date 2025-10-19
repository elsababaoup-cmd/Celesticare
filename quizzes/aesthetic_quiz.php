<?php
session_start();
include("../includes/navbar.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Aesthetic Quiz - CelestiCare</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      margin: 0;
      min-height: 100vh;
      background: linear-gradient(135deg, #d3cce3 0%, #ad83e8ff 100%);
      font-family: 'Poppins', sans-serif;
      overflow-x: hidden;
      opacity: 0;
      filter: blur(15px);
      transition: opacity 0.6s ease, filter 0.6s ease;
    }
    body.fade-in { opacity: 1; filter: blur(0); }

    .quiz-wrapper {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 40px;
      margin-top: 70px;
    }

    .black-card {
      background: rgba(0, 0, 0, 0.85);
      border-radius: 25px;
      box-shadow: 0 0 30px rgba(0,0,0,0.4);
      max-width: 1000px;
      width: 90%;
      height: 600px;
      perspective: 1200px;
      transition: all 0.6s ease;
    }

    .card-inner {
      position: relative;
      width: 100%;
      height: 100%;
      transition: transform 1s ease;
      transform-style: preserve-3d;
    }
    .black-card.flipped .card-inner { transform: rotateY(180deg); }

    .card-front, .card-back {
      position: absolute;
      width: 100%;
      height: 100%;
      backface-visibility: hidden;
      border-radius: 25px;
      padding: 30px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      color: #fff;
    }
    .card-back { transform: rotateY(180deg); }

    h2 {
      color: white;
      font-weight: 600;
      margin-bottom: 25px;
      text-align: center;
    }

    .quiz-container {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 25px;
      justify-items: center;
      align-items: center;
      margin-top: 20px;
    }

    .quiz-option {
      width: 230px;
      aspect-ratio: 1 / 1;
      border-radius: 20px;
      overflow: hidden;
      transition: all 0.3s ease;
      cursor: pointer;
      border: 3px solid transparent;
      background-color: #1a1a1a;
      box-shadow: 0 0 12px rgba(255,255,255,0.05);
    }

    .quiz-option img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .quiz-option:hover {
      transform: scale(1.07);
      border-color: #8c77c5;
      box-shadow: 0 0 15px rgba(140,119,197,0.4);
    }

    .quiz-option.selected {
      transform: scale(1.1);
      border-color: #6b5b95;
      box-shadow: 0 0 20px rgba(107,91,149,0.6);
    }

    .btn-next {
      margin-top: 30px;
      background-color: #6b5b95;
      color: #fff;
      border: none;
      border-radius: 35px;
      padding: 14px 50px;
      font-weight: 500;
      font-size: 1.1rem;
      transition: 0.3s ease;
    }

    .btn-next:hover { background-color: #8c77c5; transform: scale(1.05); }
    .btn-next:disabled { opacity: 0.6; transform: none; cursor: not-allowed; }

    .modal-content {
      background: linear-gradient(135deg, #2e2e2e, #1a1a1a);
      color: #fff;
      border-radius: 25px;
      text-align: center;
      padding: 35px;
      box-shadow: 0 0 25px rgba(0,0,0,0.6);
    }
    .modal-header { border-bottom: none; justify-content: center; }
    .modal-footer { border-top: none; justify-content: center; }
    .btn-results {
      background-color: #6b5b95;
      color: #fff;
      border: none;
      border-radius: 35px;
      padding: 12px 40px;
      font-size: 1.1rem;
      transition: 0.3s;
    }
    .btn-results:hover { background-color: #8c77c5; transform: scale(1.05); }

    #custom-backdrop.show {
      position: fixed;
      top: 0; left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.8);
      backdrop-filter: blur(4px);
      opacity: 1;
      transition: opacity 0.4s ease-in-out;
      z-index: 2000;
    }

    .modal.show { z-index: 3001 !important; }
    body.modal-open { overflow: hidden !important; }

    html::-webkit-scrollbar,
    body::-webkit-scrollbar {
      width: 0 !important;
      height: 0 !important;
      background: transparent;
    }
  </style>
</head>

<body>
  <div class="quiz-wrapper">
    <div class="black-card" id="quizCard">
      <div class="card-inner">
        <div class="card-front">
          <h2>Pick the Image that Speaks to You</h2>
          <div class="quiz-container" id="quizContainer"></div>
          <button class="btn-next" id="nextBtn" disabled>Next</button>
        </div>
        <div class="card-back">
          <h2>Pick the Image that Speaks to You</h2>
          <div class="quiz-container" id="quizContainerBack"></div>
          <button class="btn-next" id="nextBtnBack" disabled>Next</button>
        </div>
      </div>
    </div>
  </div>

  <div id="custom-backdrop" aria-hidden="true"></div>

  <canvas id="confetti-canvas" style="position:fixed; top:0; left:0; width:100%; height:100%; pointer-events:none; z-index:3000;"></canvas>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

<script>
const allImages = [
  { src: "acad1.jpg", aesthetic: "academia" },
  { src: "acad2.jpg", aesthetic: "academia" },
  { src: "acad3.jpg", aesthetic: "academia" },

  { src: "boho1.jpg", aesthetic: "boho" },
  { src: "boho2.jpg", aesthetic: "boho" },
  { src: "boho3.jpg", aesthetic: "boho" },

  { src: "coquette12.jpg", aesthetic: "coquette" },
  { src: "coquette2.jpg", aesthetic: "coquette" },
  { src: "coquette3.jpg", aesthetic: "coquette" },

  { src: "glam1.jpg", aesthetic: "luxurious" },
  { src: "glam2.jpg", aesthetic: "luxurious" },
  { src: "glam3.jpg", aesthetic: "luxurious" },

  { src: "grunge1.jpg", aesthetic: "grunge" },
  { src: "grunge2.jpg", aesthetic: "grunge" },
  { src: "grunge3.jpg", aesthetic: "grunge" },

  { src: "punk1.jpg", aesthetic: "punk" },
  { src: "punk2.jpg", aesthetic: "punk" },
  { src: "punk3.jpg", aesthetic: "punk" },

  { src: "y2k1.jpg", aesthetic: "y2k" },
  { src: "y2k2.jpg", aesthetic: "y2k" },
  { src: "y2k3.jpg", aesthetic: "y2k" },
].map(img => ({ src: `../quizzes/assets/${img.src}`, aesthetic: img.aesthetic }));

// Shuffle array randomly
function shuffle(array) {
  for (let i = array.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [array[i], array[j]] = [array[j], array[i]];
  }
  return array;
}

// ✅ Create 6 questions (each with 3 options)
const shuffledImages = shuffle([...allImages]);
const imageSets = [];
for (let i = 0; i < 6; i++) {
  imageSets.push(shuffledImages.slice(i * 3, i * 3 + 3));
}

let currentSet = 0;
let flipped = false;
let aestheticCounts = {
  boho: 0,
  academia: 0,
  coquette: 0,
  glam: 0,
  grunge: 0,
  punk: 0,
  y2k: 0
};

const quizCard = document.getElementById("quizCard");
const quizContainer = document.getElementById("quizContainer");
const quizContainerBack = document.getElementById("quizContainerBack");
const nextBtn = document.getElementById("nextBtn");
const nextBtnBack = document.getElementById("nextBtnBack");

function renderImages(container, setIndex, button) {
  container.innerHTML = "";
  const images = imageSets[setIndex];
  images.forEach(obj => {
    const option = document.createElement("div");
    option.classList.add("quiz-option");
    option.innerHTML = `<img src="${obj.src}" alt="${obj.aesthetic}">`;
    option.addEventListener("click", () => {
      container.querySelectorAll(".quiz-option").forEach(opt => opt.classList.remove("selected"));
      option.classList.add("selected");
      button.disabled = false;
      aestheticCounts[obj.aesthetic] = (aestheticCounts[obj.aesthetic] || 0) + 1;
    });
    container.appendChild(option);
  });
}

function flipCard() {
  flipped = !flipped;
  quizCard.classList.toggle("flipped");
}

function getFinalAesthetic() {
  const maxCount = Math.max(...Object.values(aestheticCounts));
  const topAesthetics = Object.keys(aestheticCounts).filter(a => aestheticCounts[a] === maxCount);

  // 🎯 Randomly break ties if multiple
  return topAesthetics[Math.floor(Math.random() * topAesthetics.length)];
}

function completeQuiz() {
  const finalAesthetic = getFinalAesthetic();
  localStorage.setItem("aestheticResult", finalAesthetic);
  window.location.href = `aesthetic_result.php?aesthetic=${encodeURIComponent(finalAesthetic)}`;
}

function handleNext(button) {
  button.disabled = true;
  const nextSet = currentSet + 1;

  if (nextSet < imageSets.length) {
    flipCard();
    setTimeout(() => {
      currentSet++;
      if (!flipped) renderImages(quizContainer, currentSet, nextBtn);
      else renderImages(quizContainerBack, currentSet, nextBtnBack);
    }, 300);
  } else {
    completeQuiz();
  }
}

nextBtn.addEventListener("click", () => handleNext(nextBtn));
nextBtnBack.addEventListener("click", () => handleNext(nextBtnBack));

window.addEventListener("load", () => {
  document.body.classList.add("fade-in");
  renderImages(quizContainer, currentSet, nextBtn);
});
</script>

</body>
</html>
