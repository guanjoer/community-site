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
    <title>로그인</title>
</head>
<body>
    <h2>로그인</h2>
    <?php if (isset($error)): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="post" action="login.php">
        <label for="username">아이디</label>
        <input type="text" id="username" name="username" required><br>

        <label for="password">비밀번호</label>
        <input type="password" id="password" name="password" required><br>

        <button type="submit">로그인</button>
    </form>
    <p>회원이 아니신가요? <a href="signup.php">회원가입</a></p>
</body>
</html>
