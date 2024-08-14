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
        echo "<script>alert('게시글을 삭제할 권한이 없습니다.'); window.location.href='index.php';</script>";
        exit();
    }

    // 게시글 삭제 처리
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);

    echo "<script>alert('게시글이 성공적으로 삭제되었습니다.'); window.location.href='index.php';</script>";
    exit();
} else {
    header("Location: index.php");
    exit();
}
?>
