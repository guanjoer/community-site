<?php
// success.php
session_start();

// 회원가입 시 전달된 username이 세션에 저장되어 있다고 가정
if (!isset($_SESSION['username'])) {
    // 만약 세션에 username이 없으면, 메인 페이지로 리다이렉트
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>회원가입 성공</title>
</head>
<body>
    <h1><?php echo htmlspecialchars($username); ?>님, 회원가입을 축하합니다!</h1>
    <p>이제 로그인하여 사이트를 이용할 수 있습니다.</p>
    
    <button onclick="location.href='login.php'">로그인 하기</button>
    <button onclick="location.href='index.php'">홈으로 가기</button>
</body>
</html>
