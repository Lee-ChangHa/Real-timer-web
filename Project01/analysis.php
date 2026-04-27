<?php
error_reporting(0); // 운영 단계에서는 에러 숨김
date_default_timezone_set('Asia/Seoul'); 
session_start();

if (!isset($_SESSION['user_id'])) { header("Location: login_page.php"); exit; }

$conn = new mysqli("ung-db.cpmsuo4eyfec.ap-northeast-2.rds.amazonaws.com", "admin", "indionce", "ung_db");
$user_id = $_SESSION['user_id'];
$nickname = $_SESSION['nickname'];

// 1. Weekly Chart Data
$graph_query = "SELECT DATE(created_at) as study_date, SUM(total_seconds) as total 
                FROM study_sessions 
                WHERE user_id = ? AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                GROUP BY study_date ORDER BY study_date ASC";
$stmt1 = $conn->prepare($graph_query);
$stmt1->bind_param("i", $user_id);
$stmt1->execute();
$graph_result = $stmt1->get_result();

$labels = []; $data_values = [];
while($row = $graph_result->fetch_assoc()) {
    $labels[] = date('m/d', strtotime($row['study_date']));
    $data_values[] = round($row['total'] / 60); 
}

// 2. Monthly Grass Data
$Y = date('Y'); $m = date('m'); $today = date('Y-m-d');
$grass_query = "SELECT DATE(created_at) as study_date, SUM(total_seconds) as total 
                FROM study_sessions 
                WHERE user_id = ? AND YEAR(created_at) = ? AND MONTH(created_at) = ?
                GROUP BY study_date";
$stmt2 = $conn->prepare($grass_query);
$stmt2->bind_param("iii", $user_id, $Y, $m);
$stmt2->execute();
$grass_result = $stmt2->get_result();

$study_data = [];
while($row = $grass_result->fetch_assoc()) {
    $study_data[date('Y-m-d', strtotime($row['study_date']))] = (float)$row['total'];
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>REPORT : 엉덩이.STORE</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="favicon.png" >
    <style>
        :root { --neon: #00ff41; }
        body { background: #000; color: #fff; font-family: 'Inter', sans-serif; padding: 40px; margin: 0; }
        .container { max-width: 900px; margin: 0 auto; }
        nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
        .logo { font-size: 24px; font-weight: 900; color: #fff; text-decoration: none; display: flex; align-items: center; }
        .logo img { height: 30px; margin-right: 12px; }
        .header-title { font-size: 50px; font-weight: 900; letter-spacing: -3px; line-height: 1; margin-bottom: 30px; }
        .section-card { background: #0a0a0a; border: 1px solid #222; padding: 25px; margin-bottom: 20px; }
        .section-header { font-size: 13px; font-weight: 900; color: var(--neon); margin-bottom: 20px; display: flex; align-items: center; letter-spacing: 1px; }
        .section-header::before { content: ''; width: 3px; height: 13px; background: var(--neon); margin-right: 10px; }
        .month-label { margin-left: auto; color: #fff; font-size: 18px; }
        .grass-grid { display: grid; grid-template-columns: repeat(7, 35px); gap: 8px; width: fit-content; }
        .cell { width: 35px; height: 35px; border-radius: 2px; position: relative; background: #111; }
        .cell.empty { background: transparent; }
        .cell.is-today { outline: 2px solid #fff; outline-offset: 1px; z-index: 5; }
        .cell:hover { transform: scale(1.15); z-index: 10; outline: 2px solid #fff; cursor: pointer; }

        /* Neon Color Levels */
        .g-1 { background-color: #00641a !important; } 
        .g-2 { background-color: #26e600 !important; } 
        .g-3 { background-color: #00ff41 !important; opacity: 0.8; } 
        .g-4 { background-color: var(--neon) !important; box-shadow: 0 0 15px rgba(0, 255, 65, 0.7); }

        .cell:hover::after {
            content: attr(data-info); position: absolute; bottom: 45px; left: 50%; transform: translateX(-50%);
            background: #fff; color: #000; padding: 5px 10px; font-size: 10px; font-weight: 900; white-space: nowrap; z-index: 100;
        }
        .back-btn { display: inline-block; margin-top: 30px; color: #444; text-decoration: none; font-weight: 900; }
    </style>
</head>
<body>
<div class="container">
    <nav>
        <a href="index.php" class="logo"><img src="favicon.png" alt="L"> 엉덩이.STORE</a>
        <div style="font-weight: 900; font-size: 13px; color: #444;">ID: <?= htmlspecialchars($nickname) ?></div>
    </nav>
    <div class="header-title">PERFORMANCE REPORT _</div>

    <div class="section-card">
        <div class="section-header">WEEKLY INTENSITY</div>
        <div style="height: 250px;"><canvas id="studyChart"></canvas></div>
    </div>

    <div class="section-card">
        <div class="section-header">OPERATOR PERSISTENCE <span class="month-label"><?= strtoupper(date('F')) ?></span></div>
        <div class="grass-grid">
            <?php
            $first_day = "$Y-$m-01";
            $start_dow = date('w', strtotime($first_day));
            $total_days = date('t', strtotime($first_day));

            for ($i = 0; $i < $start_dow; $i++) echo "<div class='cell empty'></div>";

            for ($d = 1; $d <= $total_days; $d++) {
                $check_date = date('Y-m-d', strtotime("$Y-$m-$d"));
                $sec = $study_data[$check_date] ?? 0;
                $hrs = round($sec / 3600, 1);
                $is_today = ($check_date === $today) ? "is-today" : "";
                
                $lvl = "";
                if ($sec > 0) {
                    if ($sec < 3600) $lvl = "g-1";
                    else if ($sec < 10800) $lvl = "g-2";
                    else if ($sec < 18000) $lvl = "g-3";
                    else $lvl = "g-4";
                }
                echo "<div class='cell $lvl $is_today' data-info='[$check_date] $hrs hours'></div>";
            }
            ?>
        </div>
    </div>
    <a href="index.php" class="back-btn">← RETURN TO TERMINAL</a>
</div>

<script>
    const ctx = document.getElementById('studyChart').getContext('2d');
    const grad = ctx.createLinearGradient(0, 0, 0, 300);
    grad.addColorStop(0, 'rgba(0, 255, 65, 0.3)');
    grad.addColorStop(1, 'rgba(0, 255, 65, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                data: <?= json_encode($data_values) ?>,
                borderColor: '#00ff41',
                backgroundColor: grad,
                borderWidth: 3,
                pointBackgroundColor: '#00ff41',
                pointRadius: 4,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#111' }, ticks: { color: '#444' } },
                x: { grid: { display: false }, ticks: { color: '#444' } }
            }
        }
    });
</script>
</body>
</html>
