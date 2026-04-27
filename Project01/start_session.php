<?php
session_start();
header('Content-Type: application/json');

$conn = new mysqli("ung-db.cpmsuo4eyfec.ap-northeast-2.rds.amazonaws.com", "admin", "indionce", "ung_db");

$uid = $_SESSION['user_id'] ?? null;

// 테이블에 user_id를 넣을 때 유효한 값인지 확인
$stmt = $conn->prepare("INSERT INTO study_sessions (user_id, total_seconds, created_at) VALUES (?, 0, NOW())");
$stmt->bind_param("i", $uid);

if ($stmt->execute()) {
    echo json_encode(['session_id' => $conn->insert_id]);
} else {
    echo json_encode(['error' => $stmt->error]);
}
$conn->close();
?>
