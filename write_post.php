<?php
session_start();

// 로그인 여부 확인
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// DB 연결 설정
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
$stmt = $pdo->query("SELECT id, name FROM boards ORDER BY name ASC");
$boards = $stmt->fetchAll();

// 게시글 작성 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $board_id = $_POST['board_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    // 게시글 저장
    $stmt = $pdo->prepare("INSERT INTO posts (user_id, board_id, title, content) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $board_id, $title, $content]);

    echo "<script>alert('게시글이 성공적으로 작성되었습니다.'); window.location.href='index.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>글쓰기</title>
</head>
<body>
    <h1>글쓰기</h1>

    <form method="post" action="write_post.php">
        <label for="board_id">게시판 선택</label>
        <select id="board_id" name="board_id" required>
            <?php foreach ($boards as $board): ?>
                <option value="<?php echo $board['id']; ?>"><?php echo htmlspecialchars($board['name']); ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="title">제목</label>
        <input type="text" id="title" name="title" required><br>

        <label for="content">내용</label>
        <textarea id="content" name="content" rows="10" required></textarea><br>

        <button type="submit">글 작성</button>
    </form>

    <button onclick="location.href='index.php'">홈으로 돌아가기</button>
</body>
</html>
