<?php
session_start();

// 관리자 여부 확인
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once '../config/db.php';

require_once '../queries.php';


// 사용자
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>사용자 관리</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=New+Amsterdam&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../styles/base.css"> 
	<link rel="stylesheet" href="../styles/main.css"> 
	<link rel="stylesheet" href="styles/boards.css">
	<link rel="stylesheet" href="styles/users.css">
</head>
<body>
    <?php require_once 'admin_header.php' ?>

    <div id="main-container">
        <?php require_once 'admin_sidebar.php'?>
        <section id="content">
    <h1>관리자 > 사용자 관리</h1>
	
    <!-- <button onclick="location.href='create_board.php'">새 게시판 생성</button> -->

    <h2>사용자 목록</h2>

	<table>
		<thead>
			<tr>
				<th>이름</th>
				<th>이메일</th>
				<th>가입일</th>
				<th>권한</th>
				<th>삭제</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($users as $user): ?>
			<tr>
				<td class="board-name">
					<?php echo htmlspecialchars($user['username']); ?>
				</td>
				<td>
					<?php echo htmlspecialchars($user['email']); ?>
				</td>
				<td>
					<?php echo date('Y-m-d', strtotime($user['created_at'])); ?>
				</td>
				<td>
					<?php echo htmlspecialchars($user['role']); ?>
					<!-- <a class="board-btn" href="edit_board.php?id=<?php echo $board['id']; ?>">수정</a> -->
				</td>
				<td>
					<a class="board-btn" href="delete_user.php?id=<?php echo $user['id']; ?>" onclick="return confirm('해당 사용자를 삭제하시겠습니까?')">삭제</a>
				</td>
			</tr>
			<?php endforeach; ?>
        </section>
	</div>
	</tbody>
	</table>
</body>
</html>
