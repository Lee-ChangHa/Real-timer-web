<?php
ini_set('session.cookie_lifetime', 60 * 60 * 24 * 30);
session_start();

if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>LOGIN : 엉덩이.STORE</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@900&display=swap" rel="stylesheet">
    <style>
        body { 
            background: #000; color: #fff; font-family: 'Inter', sans-serif; 
            margin: 0; display: flex; justify-content: center; align-items: center; 
            height: 100vh; overflow: hidden;
        }
        .login-box { 
            width: 100%; max-width: 400px; padding: 40px; 
            background: #0a0a0a; border: 1px solid #222; 
        }
        h1 { 
            font-size: 40px; font-weight: 900; letter-spacing: -3px; 
            margin: 0 0 40px 0; border-bottom: 5px solid #fff; padding-bottom: 10px;
        }
        .input-group { margin-bottom: 20px; }
        label { 
            display: block; font-size: 11px; color: #666; 
            margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px;
        }
        input { 
            width: 100%; background: #111; border: 1px solid #333; 
            color: #fff; padding: 15px; box-sizing: border-box; 
            font-size: 16px; transition: 0.3s;
        }
        input:focus { border-color: #00ff41; outline: none; }
        
        .login-btn { 
            width: 100%; background: #fff; color: #000; border: none; 
            padding: 18px; font-weight: 900; font-size: 16px; 
            cursor: pointer; margin-top: 10px; transition: 0.2s;
        }
        .login-btn:hover { background: #00ff41; }
        
        .footer-links { 
            margin-top: 25px; text-align: center; font-size: 13px; 
            display: flex; justify-content: center; gap: 20px;
        }
        .footer-links a { color: #444; text-decoration: none; }
        .footer-links a:hover { color: #fff; }
    </style>
</head>
<body>

    <div class="login-box">
        <h1>LOGIN</h1>
        <form action="auth.php?action=login" method="POST">
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="아이디를 입력하세요" required>
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="비밀번호를 입력하세요" required>
            </div>
            <button type="submit" class="login-btn">SIGN IN</button>
        </form>

        <div class="footer-links">
            <a href="register.php">CREATE ACCOUNT</a>
            <a href="index.php">BACK TO HOME</a>
        </div>
    </div>

</body>
</html>
