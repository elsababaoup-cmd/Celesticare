<?php
session_start();
include("../includes/navbar.php");
include("../config/dbconfig.php");

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

// Fetch latest outfit
$stmt = $conn->prepare("SELECT outfit_data, gender FROM user_outfits WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($outfit_json, $gender);
$stmt->fetch();
$stmt->close();

if (!$outfit_json) {
    echo "<p>No outfit found. Please dress your mannequin first!</p>";
    exit;
}

// Decode outfit JSON
$outfit = json_decode($outfit_json, true);

// Count styles
$styleCounter = [];
foreach ($outfit as $item) {
    $style = $item['style'] ?? 'minimalist';
    $styleCounter[$style] = ($styleCounter[$style] ?? 0) + 1;
}

// Determine top style
arsort($styleCounter);
$topStyle = array_key_first($styleCounter);

// Style information
$styleInfo = [
    'minimalist' => [
        'name' => 'Minimalist Elegance',
        'description' => 'Clean, simple, and timeless pieces. Less is more and comfort meets elegance.',
        'tips' => 'Focus on neutral colors and classic cuts. Keep accessories minimal.',
        'colors' => 'White, black, beige, grey',
        'occasions' => 'Everyday, casual, office'
    ],
    'businesswear' => [
        'name' => 'Professional Businesswear',
        'description' => 'Polished, professional, and structured looks perfect for work and meetings.',
        'tips' => 'Stick to tailored pieces. Use subtle patterns.',
        'colors' => 'Navy, black, grey, white',
        'occasions' => 'Work, meetings, interviews'
    ],
    'elegant' => [
        'name' => 'Classic Elegance',
        'description' => 'Sophisticated, classy, and luxurious styles with refined silhouettes.',
        'tips' => 'Add statement accessories and elegant shoes.',
        'colors' => 'Pastels, jewel tones, black, white',
        'occasions' => 'Formal events, parties, dinners'
    ],
    'creative' => [
        'name' => 'Creative Expression',
        'description' => 'Bold, artistic, and expressive looks that show your personality.',
        'tips' => 'Mix textures, patterns, and colors. Be fearless.',
        'colors' => 'Bright, contrasting, eclectic',
        'occasions' => 'Art events, casual outings, fashion-forward settings'
    ],
    'soft' => [
        'name' => 'Soft Elegance',
        'description' => 'Gentle, pastel, and cozy pieces that create a soft, approachable vibe.',
        'tips' => 'Layer soft fabrics. Stick to calming colors.',
        'colors' => 'Pastels, cream, light pink, baby blue',
        'occasions' => 'Casual outings, coffee dates, indoor events'
    ],
    'rough' => [
        'name' => 'Rough Edge',
        'description' => 'Edgy, casual, and street-inspired styles that stand out with attitude.',
        'tips' => 'Use denim, leather, and layered streetwear.',
        'colors' => 'Black, grey, earthy tones',
        'occasions' => 'Street, casual outings, concerts'
    ],
    'streetwear' => [
        'name' => 'Urban Streetwear',
        'description' => 'Urban, trendy, and casual styles that combine comfort with flair.',
        'tips' => 'Layer hoodies, jackets, and sneakers. Use bold logos.',
        'colors' => 'Black, white, bold bright accents',
        'occasions' => 'Casual hangouts, street style, city walks'
    ]
];

$info = $styleInfo[$topStyle] ?? [
    'name' => 'Unique Style',
    'description' => 'A unique style that reflects your personality!',
    'tips' => 'Mix and match your favorite pieces.',
    'colors' => 'Various',
    'occasions' => 'Everywhere'
];

// SAVE STYLE RESULT TO USERS TABLE (since you already have style_result column)
$update_stmt = $conn->prepare("UPDATE users SET style_result = ? WHERE id = ?");
$update_stmt->bind_param("si", $topStyle, $user_id);
$update_stmt->execute();
$update_stmt->close();

// NEW APPROACH: Grid-based positioning with uniform layout
function createItemLayout($items) {
    $layout = [];
    $count = count($items);
    
    // Categorize items
    $tops = [];
    $bottoms = [];
    $shoes = [];
    $accessories = [];
    
    foreach ($items as $item) {
        $category = strtolower($item['category'] ?? '');
        if (strpos($category, 'top') !== false || strpos($category, 'shirt') !== false || strpos($category, 'blouse') !== false) {
            $tops[] = $item;
        } elseif (strpos($category, 'bottom') !== false || strpos($category, 'pant') !== false || strpos($category, 'skirt') !== false) {
            $bottoms[] = $item;
        } elseif (strpos($category, 'shoe') !== false || strpos($category, 'footwear') !== false) {
            $shoes[] = $item;
        } else {
            $accessories[] = $item;
        }
    }
    
    // Position tops and bottoms to avoid overlap
    $topPositions = [];
    $bottomPositions = [];
    
    if (count($tops) > 0) {
        $topPositions = [
            ['x' => 0, 'y' => -0.25, 'rotation' => 0, 'scale' => 0.9, 'zIndex' => 20]
        ];
    }
    
    if (count($bottoms) > 0) {
        $bottomPositions = [
            ['x' => 0, 'y' => 0.15, 'rotation' => 0, 'scale' => 0.85, 'zIndex' => 10]
        ];
    }
    
    // Position shoes at bottom
    $shoePositions = [];
    $shoeCount = count($shoes);
    if ($shoeCount > 0) {
        if ($shoeCount === 1) {
            $shoePositions[] = ['x' => 0, 'y' => 0.35, 'rotation' => 0, 'scale' => 0.7, 'zIndex' => 5];
        } else {
            $shoePositions[] = ['x' => -0.15, 'y' => 0.35, 'rotation' => -5, 'scale' => 0.65, 'zIndex' => 5];
            $shoePositions[] = ['x' => 0.15, 'y' => 0.35, 'rotation' => 5, 'scale' => 0.65, 'zIndex' => 5];
        }
    }
    
    // Position accessories around the edges
    $accessoryPositions = [];
    $accessoryCount = count($accessories);
    if ($accessoryCount > 0) {
        $positions = [
            ['x' => -0.3, 'y' => -0.3, 'rotation' => -8, 'scale' => 0.6, 'zIndex' => 30],
            ['x' => 0.3, 'y' => -0.3, 'rotation' => 8, 'scale' => 0.6, 'zIndex' => 30],
            ['x' => -0.35, 'y' => 0, 'rotation' => -12, 'scale' => 0.55, 'zIndex' => 25],
            ['x' => 0.35, 'y' => 0, 'rotation' => 12, 'scale' => 0.55, 'zIndex' => 25],
            ['x' => -0.25, 'y' => 0.3, 'rotation' => -5, 'scale' => 0.5, 'zIndex' => 15],
            ['x' => 0.25, 'y' => 0.3, 'rotation' => 5, 'scale' => 0.5, 'zIndex' => 15]
        ];
        
        for ($i = 0; $i < min($accessoryCount, count($positions)); $i++) {
            $accessoryPositions[] = $positions[$i];
        }
    }
    
    // Assign positions to items
    $index = 0;
    
    // Assign tops
    foreach ($tops as $top) {
        if (isset($topPositions[$index])) {
            $layout[] = array_merge(['item' => $top], $topPositions[$index]);
            $index++;
        }
    }
    
    // Assign bottoms
    foreach ($bottoms as $bottom) {
        if (isset($bottomPositions[$index % count($bottomPositions)])) {
            $layout[] = array_merge(['item' => $bottom], $bottomPositions[$index % count($bottomPositions)]);
            $index++;
        }
    }
    
    // Assign shoes
    foreach ($shoes as $shoe) {
        if (isset($shoePositions[$index % count($shoePositions)])) {
            $layout[] = array_merge(['item' => $shoe], $shoePositions[$index % count($shoePositions)]);
            $index++;
        }
    }
    
    // Assign accessories
    foreach ($accessories as $accessory) {
        if (isset($accessoryPositions[$index % count($accessoryPositions)])) {
            $layout[] = array_merge(['item' => $accessory], $accessoryPositions[$index % count($accessoryPositions)]);
            $index++;
        }
    }
    
    return $layout;
}

$itemLayout = createItemLayout($outfit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Style Profile | CelestiCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* CSS Variables */
        :root {
            --primary: #8B5FBF;
            --primary-dark: #6D28D9;
            --accent: #C084FC;
            --light: #F8FAFC;
            --dark: #2c3e50;
            --gray: #64748B;
            --success: #10B981;
            --border-radius: 20px;
        }

        /* Reset & Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #495482ff 0%, #9b83d3ff 100%);
            min-height: 100vh;
            color: var(--dark);
            overflow-x: hidden;
        }

        /* Main Container */
        .style-result-container {
            max-width: 1400px;
            margin: 2rem auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: var(--border-radius);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            display: flex;
            flex-wrap: wrap;
            min-height: 80vh;
        }

        /* Left Side - Fashion Display */
        .fashion-display {
            flex: 1.2;
            min-width: 400px;
            background: linear-gradient(135deg, #f3e8ff, #fdf4ff);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
        }

        .display-wrapper {
            width: 100%;
            height: 100%;
            max-width: 700px;
            max-height: 700px;
            position: relative;
            background: 
                radial-gradient(circle at 50% 50%, rgba(255,255,255,0.9) 0%, transparent 70%),
                linear-gradient(135deg, rgba(248,240,255,0.4) 0%, transparent 50%);
            border-radius: var(--border-radius);
            border: 2px solid rgba(255, 255, 255, 0.5);
            box-shadow: 
                inset 0 2px 10px rgba(255,255,255,0.6),
                0 8px 32px rgba(0,0,0,0.1);
        }

        .items-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .style-item {
            position: absolute;
            width: var(--item-size, 70%);
            height: var(--item-size, 70%);
            border-radius: 12px;
            box-shadow: 
                0 12px 40px rgba(0,0,0,0.15),
                0 6px 20px rgba(0,0,0,0.1),
                0 3px 10px rgba(0,0,0,0.05);
            transition: all 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            object-fit: contain;
            cursor: pointer;
            border: 2px solid rgba(255, 255, 255, 0.8);
            background: rgba(155, 155, 155, 0.54);
            backdrop-filter: blur(5px);
            transform-origin: center center;
        }

        .style-item:hover {
            transform: scale(1.2) rotate(0deg) !important;
            z-index: 1000 !important;
            box-shadow: 
                0 20px 60px rgba(0,0,0,0.25),
                0 12px 40px rgba(0,0,0,0.15);
            border-color: rgba(255, 255, 255, 1);
        }

        /* Right Side - Style Info */
        .style-info {
            flex: 1;
            min-width: 350px;
            padding: 3rem 2.5rem;
            background: white;
            position: relative;
        }

        .style-info::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--accent), var(--primary-dark));
        }

        .style-badge {
            display: inline-block;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 1rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            box-shadow: 0 4px 15px rgba(139, 95, 191, 0.3);
        }

        .style-title {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 1.5rem;
            line-height: 1.1;
            background: linear-gradient(135deg, var(--dark), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .style-description {
            font-size: 1.1rem;
            color: var(--gray);
            line-height: 1.7;
            margin-bottom: 2rem;
        }

        .info-card {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--primary);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }

        .info-card:hover {
            transform: translateX(8px);
        }

        .info-card strong {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--dark);
            font-size: 1rem;
        }

        .info-card p {
            color: var(--gray);
            margin: 0;
            line-height: 1.6;
        }

        /* Action Buttons */
        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 1rem;
            margin-top: 2.5rem;
        }

        .action-btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
        }

        .action-btn.primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
        }

        .action-btn.secondary {
            background: linear-gradient(135deg, #6babff, #2475ee);
            color: white;
        }

        .action-btn.success {
            background: linear-gradient(135deg, var(--success), #059669);
            color: white;
        }

        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-in {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .style-result-container {
                flex-direction: column;
                margin: 1rem;
            }
            
            .fashion-display {
                min-height: 400px;
            }
            
            .style-title {
                font-size: 2.5rem;
            }
            
            .action-buttons {
                grid-template-columns: 1fr;
            }
        }

        /* Hide scrollbars */
        body::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>
<body>
    <div class="style-result-container">
        <!-- Fashion Display Section -->
        <section class="fashion-display">
            <div class="display-wrapper">
                <div class="items-container">
                    <?php foreach ($itemLayout as $index => $data): ?>
                        <img 
                            src="<?= htmlspecialchars($data['item']['src']) ?>" 
                            class="style-item"
                            style="
                                transform: 
                                    translate(calc(<?= $data['x'] ?> * 100%), calc(<?= $data['y'] ?> * 100%))
                                    scale(<?= $data['scale'] ?>)
                                    rotate(<?= $data['rotation'] ?>deg);
                                z-index: <?= $data['zIndex'] ?>;
                                --item-size: <?= $data['scale'] * 85 ?>%;
                                animation-delay: <?= $index * 0.1 ?>s;
                            "
                            alt="Fashion item"
                        >
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Style Information Section -->
        <section class="style-info">
            <div class="style-badge">Style Analysis</div>
            <h1 class="style-title"><?= $info['name'] ?></h1>
            <p class="style-description"><?= $info['description'] ?></p>

            <div class="info-card animate-in">
                <strong>Style Tips</strong>
                <p><?= $info['tips'] ?></p>
            </div>

            <div class="info-card animate-in" style="animation-delay: 0.1s">
                <strong>Color Palette</strong>
                <p><?= $info['colors'] ?></p>
            </div>

            <div class="info-card animate-in" style="animation-delay: 0.2s">
                <strong>Perfect For</strong>
                <p><?= $info['occasions'] ?></p>
            </div>

            <div class="info-card animate-in" style="animation-delay: 0.3s">
                <strong>Outfit Details</strong>
                <p><?= count($outfit) ?> carefully curated pieces</p>
            </div>

            <div class="action-buttons">
                <a href="../dashboard/index.php" class="action-btn primary">
                    Dashboard
                </a>
                <a href="../moodboard/moodboard_result.php" class="action-btn secondary">
                    Moodboard
                </a>
                <a href="../quizzes/style_quiz.php" class="action-btn success">
                    Try Again
                </a>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const items = document.querySelectorAll('.style-item');
            
            items.forEach(item => {
                // Store original transform
                const originalTransform = item.style.transform;
                
                item.addEventListener('mouseenter', function() {
                    this.style.transform = originalTransform.replace(/scale\([^)]+\)/, 'scale(1.2)').replace(/rotate\([^)]+\)/, 'rotate(0deg)');
                    this.style.transition = 'all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                });
                
                item.addEventListener('mouseleave', function() {
                    this.style.transform = originalTransform;
                    this.style.transition = 'all 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                });
            });

            // Add scroll animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animationPlayState = 'running';
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.animate-in').forEach(el => {
                observer.observe(el);
            });
        });
    </script>
</body>
</html>