<?php
session_start();

// 관리자 여부 확인
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once '../config/db.php';

// 게시판 생성 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];

    // 게시판 중복 체크
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM boards WHERE name = ?");
    $stmt->execute([$name]);
    if ($stmt->fetchColumn() > 0) {
        echo "<script>alert('이미 존재하는 게시판 이름입니다. 다른 이름을 선택하세요.'); window.history.back();</script>";
    } else {
        // 게시판 생성
        $stmt = $pdo->prepare("INSERT INTO boards (name, description) VALUES (?, ?)");
        $stmt->execute([$name, $description]);

        echo "<script>alert('게시판이 성공적으로 생성되었습니다.'); window.location.href='dashboard.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>게시판 생성</title>
</head>
<body>
    <h1>게시판 생성</h1>

    <form method="post" action="create_board.php">
        <label for="name">게시판 이름</label>
        <input type="text" id="name" name="name" required><br>

        <label for="description">게시판 설명</label>
        <textarea id="description" name="description" required></textarea><br>

        <button type="submit">생성하기</button>
    </form>

    <button onclick="location.href='dashboard.php'">관리자 대시보드로 돌아가기</button>
</body>
</html>
