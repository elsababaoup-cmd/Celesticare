<?php
include("../config/dbconfig.php");
session_start();

// Only allow if logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit("Not logged in");
}

// Read JSON from fetch()
$data = json_decode(file_get_contents("php://input"), true);

if ($data && isset($data['zodiac']) && isset($data['undertone'])) {
    $user_id = $_SESSION['user_id'];
    $zodiac = $data['zodiac'];
    $undertone = $data['undertone'];
    $season = $data['season'] ?? null;

    // Add columns to `users` table if not yet existing:
    // ALTER TABLE users ADD COLUMN zodiac VARCHAR(50), ADD COLUMN undertone VARCHAR(50), ADD COLUMN season VARCHAR(50);

    $query = "UPDATE users SET zodiac = ?, undertone = ?, season = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $zodiac, $undertone, $season, $user_id);
    $stmt->execute();
}

echo json_encode(["status" => "success"]);
?>
