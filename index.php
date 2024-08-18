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
    <link href="https://fonts.googleapis.com/css2?family=New+Amsterdam&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="styles/base.css"> 
    <link rel="stylesheet" href="styles/main.css"> 
</head>
<body>
    <?php require_once 'header.php' ?>

    <nav id="search-bar">
        <form method="get" action="search.php">
            <input type="text" name="query" placeholder="검색어를 입력하세요" required>
            <button type="submit">검색</button>
        </form>
    </nav>

    <div id="main-container">
        <!-- 사이드바: 프로필 및 게시판 목록 -->
        <?php require_once 'sidebar.php'?>

        <!-- 메인 콘텐츠: 전체 글 목록 -->
        <!-- <section id="content">
            <h2 class="header-2"><a href="board.php">전체글 보기</a></h2>
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
        </section> -->

        <!-- 메인 콘텐츠: 전체 글 목록 -->
        <section id="content">
            <h2 class="header-2"><a href="board.php">전체글 보기</a></h2>
            <table>
                <thead>
                    <tr>
                        <th>번호</th>
                        <th>제목</th>
                        <th>글쓴이</th>
                        <th>작성일</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $counter = count($posts); 
                    foreach ($posts as $post): 
                    ?>
                        <tr>
                            <td><?php echo $counter; ?></td>
                            <td>
                                <a href="post.php?id=<?php echo $post['id']."&board=".$post['board_id']; ?>">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($post['username']); ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($post['created_at'])); ?></td>
                        </tr>
                    <?php 
                    $counter--;
                    endforeach; 
                    ?>
                </tbody>
            </table>
        </section>

    </div>
</body>
</html>
