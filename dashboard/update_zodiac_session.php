<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['zodiac_sign'])) {
    $zodiac_sign = trim($_POST['zodiac_sign']);
    
    // Update session
    $_SESSION['zodiac_sign'] = $zodiac_sign;
    
    // Update cookie (30 days)
    setcookie('zodiac_sign', $zodiac_sign, time() + (86400 * 30), "/");
    
    echo json_encode([
        'success' => true, 
        'message' => 'Session updated',
        'new_zodiac' => $zodiac_sign
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>