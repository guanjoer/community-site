<?php
session_start();

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

// 게시글 정보 가져오기
if (isset($_GET['id'])) {
    $post_id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();

    if (!$post) {
        echo "<script>alert('존재하지 않는 게시글입니다.'); window.location.href='index.php';</script>";
        exit();
    }

    // 작성자 또는 관리자 여부 확인
    if ($post['user_id'] != $_SESSION['user_id'] && $_SESSION['role'] !== 'admin') {
        echo "<script>alert('게시글을 수정할 권한이 없습니다.'); window.location.href='index.php';</script>";
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}

// 게시글 수정 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    // 게시글 업데이트
    $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
    $stmt->execute([$title, $content, $post_id]);

    echo "<script>alert('게시글이 성공적으로 수정되었습니다.'); window.location.href='post.php?id=$post_id';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>게시글 수정</title>
</head>
<body>
    <h1>게시글 수정</h1>

    <form method="post" action="edit_post.php?id=<?php echo htmlspecialchars($post_id); ?>">
        <label for="title">제목</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required><br>

        <label for="content">내용</label>
        <textarea id="content" name="content" rows="10" required><?php echo htmlspecialchars($post['content']); ?></textarea><br>

        <button type="submit">수정하기</button>
    </form>

    <button onclick="location.href='post.php?id=<?php echo $post_id; ?>'">게시글로 돌아가기</button>
</body>
</html>
