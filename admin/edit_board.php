<?php
session_start();

// 관리자 여부 확인
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

require_once '../config/db.php';

// 게시판 정보 가져오기
if (isset($_GET['id'])) {
    $board_id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM boards WHERE id = ?");
    $stmt->execute([$board_id]);
    $board = $stmt->fetch();

    if (!$board) {
        echo "<script>alert('존재하지 않는 게시판입니다.'); window.location.href='dashboard.php';</script>";
        exit();
    }
} else {
    header("Location: dashboard.php");
    exit();
}

// 게시판 수정 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];

    // 게시판 이름 중복 체크 // 자신은 제외
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM boards WHERE name = ? AND id != ?");
    $stmt->execute([$name, $board_id]);
    if ($stmt->fetchColumn() > 0) {
        echo "<script>alert('이미 존재하는 게시판 이름입니다. 다른 이름을 선택하세요.'); window.history.back();</script>";
    } else {
        // 게시판 업데이트
        $stmt = $pdo->prepare("UPDATE boards SET name = ?, description = ? WHERE id = ?");
        $stmt->execute([$name, $description, $board_id]);

        echo "<script>alert('게시판이 성공적으로 수정되었습니다.'); window.location.href='dashboard.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>게시판 수정</title>
</head>
<body>
    <h1>게시판 수정</h1>

    <form method="post" action="edit_board.php?id=<?php echo htmlspecialchars($board_id); ?>">
        <label for="name">게시판 이름</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($board['name']); ?>" required><br>

        <label for="description">게시판 설명</label>
        <textarea id="description" name="description" required><?php echo htmlspecialchars($board['description']); ?></textarea><br>

        <button type="submit">수정하기</button>
    </form>

    <button onclick="location.href='dashboard.php'">관리자 대시보드로 돌아가기</button>
</body>
</html>
