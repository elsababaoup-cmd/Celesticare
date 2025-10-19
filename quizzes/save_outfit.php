<?php
session_start();
include("../config/dbconfig.php");

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);

$outfit_data = json_encode($data['outfit_data'] ?? []);
$gender = $data['gender'] ?? null;
$clothing_style = $data['clothing_style'] ?? null;  // 👈 add this line

// --- Check if user already has an outfit ---
$stmt = $conn->prepare("SELECT id FROM user_outfits WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // --- Update latest outfit instead of inserting ---
    $update = $conn->prepare("
        UPDATE user_outfits 
        SET outfit_data = ?, gender = ?, clothing_style = ?, created_at = NOW()
        WHERE user_id = ?
    ");
    $update->bind_param("sssi", $outfit_data, $gender, $clothing_style, $user_id);
    $update->execute();
} else {
    // --- Insert if no previous record ---
    $insert = $conn->prepare("
        INSERT INTO user_outfits (user_id, outfit_data, gender, clothing_style, created_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $insert->bind_param("isss", $user_id, $outfit_data, $gender, $clothing_style);
    $insert->execute();
}

// ✅ Also update style_result in users table
if (!empty($clothing_style)) {
    $updateUser = $conn->prepare("UPDATE users SET style_result = ? WHERE id = ?");
    $updateUser->bind_param("si", $clothing_style, $user_id);
    $updateUser->execute();
    $updateUser->close();
}

echo json_encode(["success" => true]);

?>
