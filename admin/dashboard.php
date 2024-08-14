<?php
session_start();

// 관리자 여부 확인
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// DB 연결 설정 및 게시판 목록 가져오기
$host = getenv('DB_HOST');
$db = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

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
