<?php
session_start();
header('Content-Type: application/json');
date_default_timezone_set('Asia/Seoul');

// DB 연결
$conn = new mysqli("ung-db.cpmsuo4eyfec.ap-northeast-2.rds.amazonaws.com", "admin", "indionce", "ung_db");

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "DB Connection Failed"]);
    exit;
}

// 세션 체크 및 데이터 존재 확인
if (!isset($_SESSION['user_id']) || !isset($_POST['seconds'])) {
    echo json_encode(["status" => "error", "message" => "No session or data"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$add_sec = (int)$_POST['seconds'];
$now = date('Y-m-d H:i:s');

// 1초 미만의 의미 없는 데이터는 저장하지 않음
if ($add_sec <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid time"]);
    exit;
}

// 새로운 공부 세션 기록 추가 (INSERT)
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
