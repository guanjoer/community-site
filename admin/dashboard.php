<?php
session_start();

// 관리자 여부 확인
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once '../config/db.php';

// 게시판 목록 가져오기
$stmt = $pdo->query("SELECT * FROM boards ORDER BY created_at DESC");
$boards = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>관리자 대시보드</title>
</head>
<body>
    <h1>관리자 대시보드</h1>

    <button onclick="location.href='create_board.php'">새 게시판 생성</button>

    <h2>게시판 목록</h2>
    <ul>
        <?php foreach ($boards as $board): ?>
            <li>
                <?php echo htmlspecialchars($board['name']); ?> - <?php echo htmlspecialchars($board['description']); ?>
                <a href="edit_board.php?id=<?php echo $board['id']; ?>">수정</a> |
                <a href="delete_board.php?id=<?php echo $board['id']; ?>" onclick="return confirm('이 게시판을 삭제하시겠습니까?')">삭제</a>
            </li>
        <?php endforeach; ?>
    </ul>

    <button onclick="location.href='../index.php'">홈으로 돌아가기</button>
</body>
</html>
