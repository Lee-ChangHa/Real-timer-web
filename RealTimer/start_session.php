<?php
session_start();
header('Content-Type: application/json');

$conn = new mysqli("[end-point]", "[db-user]", "[db-password]", "[database-name]");

$uid = $_SESSION['user_id'] ?? null;


$stmt = $conn->prepare("INSERT INTO study_sessions (user_id, total_seconds, created_at) VALUES (?, 0, NOW())");
$stmt->bind_param("i", $uid);

if ($stmt->execute()) {
    echo json_encode(['session_id' => $conn->insert_id]);
} else {
    echo json_encode(['error' => $stmt->error]);
}
$conn->close();
?>
