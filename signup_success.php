<?php
// success.php
session_start();

// 회원가입 시 username 세션에 저장
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=New+Amsterdam&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="styles/base.css"> 
    <link rel="stylesheet" href="styles/signup_success.css">
</head>
<body>
    <?php require_once 'header.php' ?>
    
    <div id="main-container">
        <h1><?php echo htmlspecialchars($username); ?>님, 회원가입을 축하합니다!</h1>
        <p>이제 로그인하여 사이트를 이용할 수 있습니다.</p>
        
        <button onclick="location.href='login.php'">로그인 하기</button>
        <button onclick="location.href='index.php'">홈으로 가기</button>
    </div>

</body>
</html>
