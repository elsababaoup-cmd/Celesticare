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
if ($zodiac === null || $undertone === null) {
    echo json_encode(["status"=>"error", "message"=>"Missing required fields (zodiac or undertone)", "received"=>$data]);
    exit;
}

// Use the actual column name in your DB: zodiac_sign
$query = "UPDATE users SET zodiac_sign = ?, undertone = ?, season = ? WHERE id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(["status"=>"error", "message"=>"Prepare failed", "error"=>$conn->error]);
    exit;
}

$bind = $stmt->bind_param("sssi", $zodiac, $undertone, $season, $user_id);
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
    "user_id"=>$user_id
]);
exit;
