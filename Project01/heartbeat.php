<?php
session_start();
// 불필요한 에러 출력 억제
error_reporting(0);

$conn = new mysqli("ung-db.cpmsuo4eyfec.ap-northeast-2.rds.amazonaws.com", "admin", "indionce", "ung_db");

$sid = $_POST['session_id'] ?? null;

if ($sid) {
    // 1초씩 누적 (정수 연산이므로 매우 빠름)
    $stmt = $conn->prepare("UPDATE study_sessions SET total_seconds = total_seconds + 1 WHERE id = ?");
    $stmt->bind_param("i", $sid);
    $stmt->execute();
}
$conn->close();
?>
