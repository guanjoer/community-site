<?php
session_start();

// 관리자 여부 확인
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once '../config/db.php';

require_once '../queries.php';

// 게시판
$stmt = $pdo->query("SELECT * FROM boards ORDER BY created_at DESC");
$boards = $stmt->fetchAll();

// 게시글
$stmt = $pdo->query("SELECT posts.id, posts.title, posts.created_at, users.username, posts.board_id FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC LIMIT 10");
$posts = $stmt->fetchAll();
?>


<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>게시글 관리</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=New+Amsterdam&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../styles/base.css"> 
	<link rel="stylesheet" href="../styles/main.css"> 
	<link rel="stylesheet" href="styles/boards.css">
	<link rel="stylesheet" href="styles/posts.css">
</head>
<body>
    <?php require_once 'admin_header.php' ?>

    <div id="main-container">
        <?php require_once 'admin_sidebar.php'?>
        <section id="content">
    <h1>관리자 > 게시글 관리</h1>
	
    <!-- <button onclick="location.href='../write_post.php'">글쓰기</button> -->

    <!-- <h2>게시판 목록</h2> -->

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
						<a href="../post.php?id=<?php echo $post['id']."&board=".$post['board_id']; ?>">
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
