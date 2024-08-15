<?php
session_start();

require_once 'config/db.php';

// 검색어 가져오기
$query = isset($_GET['query']) ? $_GET['query'] : '';

if (empty($query)) {
    echo "<script>alert('검색어를 입력하세요.'); window.location.href='index.php';</script>";
    exit();
}

// 검색어에 대한 게시글 검색
$stmt = $pdo->prepare("
    SELECT posts.id, posts.title, posts.created_at, users.username, posts.board_id 
    FROM posts 
    JOIN users ON posts.user_id = users.id 
    WHERE posts.title LIKE ? OR posts.content LIKE ?
    ORDER BY posts.created_at DESC
");
$search_query = "%" . $query . "%";
$stmt->execute([$search_query, $search_query]);
$posts = $stmt->fetchAll();

// 게시판 목록 가져오기 (검색 결과에 포함된 게시글의 게시판)
$board_ids = array_unique(array_column($posts, 'board_id')); // board_id 필드의 값들만 새로운 배열로 생성 및 중복 제거
if (!empty($board_ids)) {
    $in  = str_repeat('?,', count($board_ids) - 1) . '?'; // board_id의 개수만큼 ? 자리표시자 생성
    $stmt = $pdo->prepare("SELECT id, name FROM boards WHERE id IN ($in)");
    $stmt->execute($board_ids);
    $boards = $stmt->fetchAll();
} else {
    $boards = [];
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>검색 결과</title>
</head>
<body>
    <header>
        <h1>검색 결과</h1>
        <a href="index.php">메인 페이지로 돌아가기</a>
    </header>

    <section>
        <h2>검색어: "<?php echo htmlspecialchars($query); ?>"</h2>
        <?php if (count($posts) > 0): ?>
            <ul>
                <?php foreach ($posts as $post): ?>
                    <?php
                    // 게시글의 게시판 이름을 가져오기
                    $board_name = '알 수 없음';
                    foreach ($boards as $board) {
                        if ($board['id'] == $post['board_id']) {
                            $board_name = $board['name'];
                            break;
                        }
                    }
                    ?>
                    <li>
                        <a href="post.php?id=<?php echo $post['id']; ?>&board=<?php echo $post['board_id']; ?>">
                            <?php echo htmlspecialchars($post['title']); ?>
                        </a><br>
                        <span>게시판: <?php echo htmlspecialchars($board_name); ?> | 작성자: <?php echo htmlspecialchars($post['username']); ?> | 작성일: <?php echo $post['created_at']; ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>검색 결과가 없습니다.</p>
        <?php endif; ?>
    </section>
</body>
</html>
