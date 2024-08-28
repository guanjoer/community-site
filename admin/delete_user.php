<?php
session_start();

// 관리자 여부 확인
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once '../config/db.php';

// 게시판 삭제
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if ($user) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);

        echo "<script>alert('사용자가 성공적으로 삭제되었습니다.'); window.location.href='users.php';</script>";
    } else {
        echo "<script>alert('존재하지 않는 사용자입니다.'); history.back();</script>";
    }
} else {
    header("Location: users.php");
    exit();
}
?>
