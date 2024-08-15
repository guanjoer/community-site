<?php
session_start();

require_once 'config/db.php';

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
    <title>GuanJoer' Community</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/base.css"> <!-- 외부 CSS 파일로 디자인을 관리 -->
</head>
<body>
    <header id="main-header">
        <div id="logo">
            <a href="index.php">GuanJoer' Community</a>
        </div>
        <nav>
            <ul class="nav-items">
                <?php foreach ($boards as $board): ?>
                    <li><a href="board.php?id=<?php echo $board['id']; ?>"><?php echo htmlspecialchars($board['name']); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <img id="profile-preview" src="uploads/<?php echo !empty($user['profile_image']) ? htmlspecialchars($user['profile_image']) : 'default.png'; ?>" alt="프로필 이미지">
                <span><?php echo htmlspecialchars($user['username']); ?>님, 환영합니다!</span>
                <a href="index.php?logout=true">로그아웃</a>
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <a href="admin/dashboard.php">관리자 대시보드로 이동</a>
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
                <li>
                    <a href="post.php?id=<?php echo $post['id']."&board=".$post['board_id']; ?>">
                        <?php echo htmlspecialchars($post['title']); ?>
                    </a>
                    <span>작성자: <?php echo htmlspecialchars($post['username']); ?> | 작성일: <?php echo $post['created_at']; ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
</body>
</html>
