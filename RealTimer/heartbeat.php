<?php
session_start();
error_reporting(0);

$conn = new mysqli("[end-point]", "[db-user]", "[db-password]", "[database-name]");

$sid = $_POST['session_id'] ?? null;

if ($sid) {
    $stmt = $conn->prepare("UPDATE study_sessions SET total_seconds = total_seconds + 1 WHERE id = ?");
    $stmt->bind_param("i", $sid);
    $stmt->execute();
}
$conn->close();
?>
