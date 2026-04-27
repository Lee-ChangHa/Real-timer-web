<?php
ini_set('session.cookie_lifetime', 60 * 60 * 24 * 30);
session_start();

$db_host = "--";
$db_user = "--";
$db_pass = "--";
$db_name = "--";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) die("Connection failed");

$action = isset($_GET['action']) ? $_GET['action'] : '';

// register
if ($action == 'register') {
    $pw = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, password, nickname) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $_POST['username'], $pw, $_POST['nickname']);
    if($stmt->execute()) {
        echo "<script>alert('가입 완료! 로그인 해주세요.'); location.href='login_page.php';</script>";
    } else {
        echo "<script>alert('아이디 중복 혹은 오류'); history.back();</script>";
    }
}

// login
if ($action == 'login') {
    $stmt = $conn->prepare("SELECT id, password, nickname FROM users WHERE username = ?");
    $stmt->bind_param("s", $_POST['username']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    
    if ($user && password_verify($_POST['password'], $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nickname'] = $user['nickname'];
        header("Location: index.php");
        exit;
    } else {
        echo "<script>alert('정보 불일치'); history.back();</script>";
    }
}

// logout
if ($action == 'logout') {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }
    session_destroy();
    header("Location: index.php");
    exit;
}
?>
