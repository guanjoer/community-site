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

// 사용자 정보 가져오기
if (isset($_SESSION['user_id'])) {
	$user_id = $_SESSION['user_id'];
	$stmt = $pdo->prepare("SELECT username, profile_image FROM users WHERE id = ?");
	$stmt->execute([$user_id]);
	$user = $stmt->fetch();
}

// 게시판 목록 가져오기
$stmt = $pdo->query("SELECT id, name FROM boards ORDER BY name ASC");
$boards = $stmt->fetchAll();

// 전체 글 목록 가져오기 (간단히 최근 10개 글을 가져오는 예시)
$stmt = $pdo->query("SELECT posts.id, posts.title, posts.created_at, users.username, posts.board_id FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC LIMIT 10");
$posts = $stmt->fetchAll();

// 로그아웃 처리
if (isset($_GET['logout'])) {
    session_destroy(); // 모든 세션 데이터 삭제
    header("Location: index.php"); // 메인 페이지로 리다이렉트
    exit();
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>메인 페이지</title>
</head>
<body>
    <header>
        <h1>커뮤니티 사이트</h1>
        <div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <img id="profile-preview" src="uploads/<?php echo !empty($user['profile_image']) ? htmlspecialchars($user['profile_image']) : 'default.png'; ?>" alt="프로필 이미지" width="100" height="100"><br>
                <span><?php echo htmlspecialchars($user['username']); ?>님, 환영합니다!</span>
                <a href="index.php?logout=true">로그아웃</a><br>
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <a href="admin/dashboard.php">관리자 대시보드로 이동</a><br>
                    <a href="admin/create_board.php">새로운 게시판 생성</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="login.php">로그인</a>
                <a href="signup.php">회원가입</a>
            <?php endif; ?>
        </div>
    </header>

    <nav>
        <form method="get" action="search.php">
            <input type="text" name="query" placeholder="검색어를 입력하세요" required>
            <button type="submit">검색</button>
        </form>
        <?php if (isset($_SESSION['user_id'])): ?>
            <button onclick="location.href='write_post.php'">글쓰기</button>
            <button onclick="location.href='mypage.php'">마이페이지</button>
        <?php endif; ?>
    </nav>

    <section>
        <h2>게시판</h2>
        <ul>
            <?php foreach ($boards as $board): ?>
                <li><a href="board.php?id=<?php echo $board['id']; ?>"><?php echo htmlspecialchars($board['name']); ?></a></li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section>
        <h2>최근 게시글</h2>
        <ul>
            <?php foreach ($posts as $post): ?>
                <?php foreach ($boards as $board): ?>
                    <?php if ($post['board_id'] == $board['id']): ?>
                        <li>
                            <a href="post.php?id=<?php echo $post['id']."&board=".$board['id']; ?>">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </a>
                            <br>
                            <span>작성자: <?php echo htmlspecialchars($post['username']); ?> | 작성일: <?php echo $post['created_at']; ?></span>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </ul>
    </section>
</body>
</html>
