<?php
session_start();
header('Content-Type: application/json');
date_default_timezone_set('Asia/Seoul');


$conn = new mysqli("[end-point]", "[db-user]", "[db-password]", "[database-name]");

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "DB Connection Failed"]);
    exit;
}

if (!isset($_SESSION['user_id']) || !isset($_POST['seconds'])) {
    echo json_encode(["status" => "error", "message" => "No session or data"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$add_sec = (int)$_POST['seconds'];
$now = date('Y-m-d H:i:s');

if ($add_sec <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid time"]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO study_sessions (user_id, total_seconds, created_at) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $user_id, $add_sec, $now);

if($stmt->execute()) {
    echo json_encode(["status" => "success", "saved_seconds" => $add_sec]);
} else {
    echo json_encode(["status" => "error", "message" => $conn->error]);
}

$stmt->close();
$conn->close();
?>
