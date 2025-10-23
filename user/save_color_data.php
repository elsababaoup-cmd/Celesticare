<?php
// save_color_data.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');

include("../config/dbconfig.php");

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(["status"=>"error", "message"=>"Not logged in", "session_user_id" => $_SESSION['user_id'] ?? null]);
    exit;
}

$user_id = intval($_SESSION['user_id']);
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!is_array($data)) {
    echo json_encode(["status"=>"error", "message"=>"Invalid JSON payload", "raw" => substr($raw,0,1000)]);
    exit;
}

$zodiac   = $data['zodiac']   ?? null;
$undertone= $data['undertone']?? null;
$season   = $data['season']   ?? null;

// Basic validation
if ($undertone === null) {
    echo json_encode(["status"=>"error", "message"=>"Missing required field (undertone)", "received"=>$data]);
    exit;
}

// ✅ CRITICAL FIX: Only update undertone and season, NOT zodiac_sign
// ✅ Get current zodiac from database first to preserve it
$check_query = "SELECT zodiac_sign FROM users WHERE id = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("i", $user_id);
$check_stmt->execute();
$result = $check_stmt->get_result();
$user = $result->fetch_assoc();
$check_stmt->close();

// Use the actual zodiac from database, not from the request
$current_zodiac = $user['zodiac_sign'] ?? $zodiac;

$query = "UPDATE users SET undertone = ?, season = ? WHERE id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(["status"=>"error", "message"=>"Prepare failed", "error"=>$conn->error]);
    exit;
}

$bind = $stmt->bind_param("ssi", $undertone, $season, $user_id);
if (!$bind) {
    echo json_encode(["status"=>"error", "message"=>"Bind failed", "error"=>$stmt->error]);
    exit;
}

$exec = $stmt->execute();
if (!$exec) {
    echo json_encode(["status"=>"error", "message"=>"Execute failed", "error"=>$stmt->error]);
    exit;
}

$affected = $stmt->affected_rows;
$stmt->close();

echo json_encode([
    "status"=>"success",
    "message"=>"Update executed",
    "affected_rows"=>$affected,
    "payload"=>$data,
    "user_id"=>$user_id,
    "current_zodiac"=>$current_zodiac // For debugging
]);
exit;