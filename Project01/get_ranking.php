<?php
header('Content-Type: application/json');
date_default_timezone_set('Asia/Seoul');

$conn = new mysqli("ung-db.cpmsuo4eyfec.ap-northeast-2.rds.amazonaws.com", "admin", "indionce", "ung_db");

// 1. 오늘 날짜(KST) 기준으로 유저별 총 공부 시간 합산
$query = "SELECT u.nickname, SUM(s.total_seconds) as total_sec 
          FROM users u 
          JOIN study_sessions s ON u.id = s.user_id 
          WHERE DATE(s.created_at) = CURDATE() 
          GROUP BY u.id 
          ORDER BY total_sec DESC LIMIT 5";

$result = $conn->query($query);
$ranking = [];

while($row = $result->fetch_assoc()) {
    $sec = $row['total_sec'];
    // 초를 00:00:00 포맷으로 변경
    $h = floor($sec / 3600);
    $m = floor(($sec % 3600) / 60);
    $s = $sec % 60;
    $formatted_time = sprintf("%02d:%02d:%02d", $h, $m, $s);
    
    $ranking[] = [
        'nickname' => $row['nickname'],
        'time' => $formatted_time
    ];
}

echo json_encode($ranking);
?>
