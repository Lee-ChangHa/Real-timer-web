<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>JOIN : 엉덩이.STORE</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@900&display=swap" rel="stylesheet">
    <style>
        body { background: #000; color: #fff; font-family: 'Inter', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .join-box { width: 400px; border: 1px solid #222; padding: 40px; background: #0a0a0a; }
        h1 { font-size: 32px; font-weight: 900; letter-spacing: -2px; border-bottom: 4px solid #fff; padding-bottom: 10px; margin-bottom: 30px; }
        .input-group { margin-bottom: 20px; }
        label { display: block; font-size: 12px; color: #666; margin-bottom: 5px; text-transform: uppercase; }
        input { width: 100%; background: #111; border: 1px solid #333; color: #fff; padding: 12px; box-sizing: border-box; font-size: 16px; }
        input:focus { border-color: #00ff41; outline: none; }
        .join-btn { width: 100%; background: #fff; color: #000; border: none; padding: 15px; font-weight: 900; font-size: 16px; cursor: pointer; margin-top: 10px; }
        .join-btn:hover { background: #00ff41; }
    </style>
</head>
<body>
    <div class="join-box">
        <h1>JOIN US</h1>
        <form action="auth.php?action=register" method="POST">
            <div class="input-group">
                <label>Username (ID)</label>
                <input type="text" name="username" required>
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="input-group">
                <label>Nickname (Display Name)</label>
                <input type="text" name="nickname" required>
            </div>
            <button type="submit" class="join-btn">CREATE ACCOUNT</button>
        </form>
    </div>
</body>
</html>
