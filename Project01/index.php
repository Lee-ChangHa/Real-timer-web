<?php
// 세션 쿠키 수명 30일 설정
ini_set('session.cookie_lifetime', 60 * 60 * 24 * 30);
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$nickname = $is_logged_in ? $_SESSION['nickname'] : "GUEST";
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>엉덩이.STORE</title>
    <link rel="icon" type="image/png" href="favicon.png"> 
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@900&display=swap" rel="stylesheet">
    <style>
        :root { --neon: #00ff41; }
        body { background: #000; color: #fff; font-family: 'Inter', sans-serif; margin: 0; padding: 0; overflow-x: hidden; }
        nav { display: flex; justify-content: space-between; align-items: center; padding: 20px 40px; border-bottom: 1px solid #222; }
        .logo { display: flex; align-items: center; font-size: 24px; font-weight: 900; letter-spacing: -1.5px; cursor: pointer; }
        .logo img { height: 32px; margin-right: 12px; image-rendering: pixelated; }
        .user-meta { font-size: 14px; font-weight: 700; color: #888; }
        .user-meta b { color: #fff; }
        .auth-btn { margin-left: 15px; text-decoration: none; padding: 4px 12px; font-size: 12px; font-weight: 900; }
        .logout-btn { color: #ff003c; border: 1px solid #ff003c; }
        .login-btn { color: var(--neon); border: 1px solid var(--neon); }
        main { padding: 40px; max-width: 1200px; margin: 0 auto; }
        .layout-grid { display: grid; grid-template-columns: 1.5fr 1fr; gap: 20px; margin-top: 20px; }
        .action-card { background: #fff; color: #000; padding: 60px 40px; cursor: pointer; transition: 0.2s; display: flex; flex-direction: column; justify-content: center; }
        .action-card:hover { background: var(--neon); }
        .action-card h2 { font-size: 70px; font-weight: 900; margin: 0; line-height: 0.85; letter-spacing: -5px; }
        .action-card p { font-weight: 900; margin-top: 25px; font-size: 14px; }
        .rank-board { background: #0a0a0a; border: 1px solid #222; padding: 30px; }
        .rank-header { font-size: 16px; font-weight: 900; color: var(--neon); margin-bottom: 25px; display: flex; align-items: center; }
        .rank-header::before { content: ''; width: 4px; height: 16px; background: var(--neon); margin-right: 10px; }
        .rank-row { display: flex; justify-content: space-between; padding: 18px 0; border-bottom: 1px solid #1a1a1a; font-size: 15px; }
        .rank-row:last-child { border: none; }
        .name { font-weight: 800; }
        .time { font-family: monospace; color: #666; }
        .stats-link { grid-column: span 2; background: #111; border: 1px solid #222; color: #fff; padding: 25px; text-align: center; text-decoration: none; font-weight: 900; font-size: 18px; transition: 0.3s; margin-top: 10px; }
        .stats-link:hover { background: #fff; color: #000; }
    </style>
</head>
<body>
    <nav>
        <div class="logo" onclick="location.href='index.php'">
            <img src="favicon.png" alt="Logo">
            엉덩이.STORE
        </div>
        <div class="user-meta">
            <b><?= htmlspecialchars($nickname) ?></b> 님
            <?php if($is_logged_in): ?>
                <a href="auth.php?action=logout" class="auth-btn logout-btn">LOGOUT</a>
            <?php else: ?>
                <a href="login_page.php" class="auth-btn login-btn">LOGIN</a>
            <?php endif; ?>
        </div>
    </nav>

    <main>
        <div class="layout-grid">
            <div class="action-card" onclick="handleAction('timer.php')">
                <h2>START<br>STUDYING<br>NOW _</h2>
                <p>> CLICK TO OPEN CAMERA SYSTEM</p>
            </div>

            <div class="rank-board">
                <div class="rank-header">TOP OPERATORS</div>
                <div id="rank-content">
                    <div style="color:#444;">LOADING...</div>
                </div>
            </div>

            <a href="javascript:void(0)" onclick="handleAction('analysis.php')" class="stats-link">
                VIEW STUDY STATISTICS & ANALYSIS REPORT
            </a>
        </div>
    </main>

    <script>
        const isLoggedIn = <?= $is_logged_in ? 'true' : 'false' ?>;

        function handleAction(targetUrl) {
            if (!isLoggedIn) {
                alert('로그인이 필요한 서비스입니다.');
                location.href = 'login_page.php';
            } else {
                location.href = targetUrl;
            }
        }

        async function getRank() {
            try {
                // 캐시 방지를 위해 타임스탬프 추가
                const res = await fetch('get_ranking.php?t=' + Date.now());
                const data = await res.json();
                const target = document.getElementById('rank-content');
                
                if(!data || data.length === 0) {
                    target.innerHTML = '<div style="color:#333; padding:20px 0;">기록이 없습니다.</div>';
                    return;
                }

                let html = '';
                data.slice(0, 5).forEach((u, i) => {
                    html += `
                        <div class="rank-row">
                            <span class="name">${i+1}. ${u.nickname}</span>
                            <span class="time">${u.time}</span>
                        </div>`;
                });
                target.innerHTML = html;
            } catch(e) { 
                console.error("Ranking Load Error:", e); 
            }
        }
        
        getRank();
        // 30초마다 갱신
        setInterval(getRank, 30000);
    </script>
</body>
</html>
