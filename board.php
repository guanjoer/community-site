<?php
session_start();

require_once 'config/db.php';

require_once 'queries.php';

// 게시판 정보 가져오기
if (isset($_GET['id'])) {
    $board_id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM boards WHERE id = ?");
    $stmt->execute([$board_id]);
    $board = $stmt->fetch();

    if (!$board) {
        echo "<script>alert('존재하지 않는 게시판입니다.'); window.location.href='index.php';</script>";
        exit();
    }

    // 게시판에 속하는 글 목록 가져오기
    $stmt = $pdo->prepare("SELECT posts.id, posts.title, posts.created_at, users.username 
                           FROM posts 
                           JOIN users ON posts.user_id = users.id 
                           WHERE posts.board_id = ? 
                           ORDER BY posts.created_at DESC");
    $stmt->execute([$board_id]);
    $posts = $stmt->fetchAll();
} else {
    $stmt = $pdo->prepare("SELECT posts.id, posts.title, posts.created_at, users.username 
                           FROM posts 
                           JOIN users ON posts.user_id = users.id 
                           ORDER BY posts.created_at DESC");
    $stmt->execute();
    $all_posts = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=New+Amsterdam&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="styles/base.css"> 
    <link rel="stylesheet" href="styles/main.css">

    <?php if(isset($_GET['id'])): ?>
        <title><?php echo htmlspecialchars($board['name']); ?> - 게시판</title>
    <?php else: ?>
        <title>전체글</title>
    <?php endif; ?>
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
        <?php require_once 'sidebar.php'?>

        <section id="content">
            <?php if(isset($_GET['id'])): ?>
                <h1><?php echo htmlspecialchars($board['name']); ?></h1>
            <?php else: ?>
                <h2>전체글</h2>
            <?php endif; ?>
            
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
                    <?php if (isset($_GET['id']) && $posts): ?>
                    <?php $counter = count($posts); ?>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td><?php echo $counter; ?></td>
                            <td>
                                <a href="post.php?id=<?php echo $post['id']; ?>">
                                        <?php echo htmlspecialchars($post['title']); ?>
                                </a>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($post['username']); ?>
                            </td>
                            <td>
                                <?php echo date('Y-m-d H:i', strtotime($post['created_at'])); ?>
                            </td>
                        </tr>
                        <?php $counter--; ?>
                    <?php endforeach; ?>
                    </tbody>

                    <tbody>
                        <?php elseif (!isset($_GET['id']) && $all_posts): ?>
                            <?php $counter = count($all_posts); ?>
                            <?php foreach ($all_posts as $post): ?>
                                <tr>
                                    <td><?php echo $counter ?></td>
                                    <td>
                                        <a href="post.php?id=<?php echo $post['id']; ?>">
                                            <?php echo htmlspecialchars($post['title']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($post['username']); ?>
                                    </td>
                                    <td>
                                        <?php echo date('Y-m-d H:i', strtotime($post['created_at'])); ?>
                                    </td>
                                </tr>
                                <?php $counter--; ?>
                            <?php endforeach; ?>
                    </tbody>
                <?php else: ?>
                    <p>게시글이 없습니다.</p>
                <?php endif; ?>
                </table>
            </section>
        </div>
</body>
</html>
