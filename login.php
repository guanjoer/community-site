<?php
session_start();

require_once 'config/db.php';

// 로그인 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 사용자 정보 확인
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // 세션에 사용자 정보 저장
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        // 로그인 성공 시 리다이렉트
        header("Location: index.php");
        exit();
    } else {
        $error = "사용자명 또는 비밀번호가 잘못되었습니다.";
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>Login</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=New+Amsterdam&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="styles/base.css"> 
    <link rel="stylesheet" href="styles/sign.css">
</head>
<body>
    <?php require_once 'header.php' ?>

    <div id="login-container">
        <h1>Login</h1>
        <?php if (isset($error)): ?>
            <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="post" action="login.php">
            <!-- <label for="username">아이디</label> -->
            <input type="text" id="username" name="username" placeholder="USER NAME" required><br>
    
            <!-- <label for="password">비밀번호</label> -->
            <input type="password" id="password" name="password" placeholder="PASSWORD" required><br>
    
            <button type="submit">LOGIN</button>
        </form>
        <p id="account-message">
            <a href="signup.php">Do you not have an account yet?</a>
        </p>
    </div>
</body>
</html>
