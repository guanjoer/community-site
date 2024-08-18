<?php
session_start();

require_once 'config/db.php';

require_once 'queries.php';

// 로그인 여부 확인
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


// 게시글 작성 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $board_id = $_POST['board_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    // 파일 업로드 처리
    $upload_success = true;  // 파일 업로드 성공 여부를 추적하는 변수

    if (isset($_FILES['uploaded_file']) && $_FILES['uploaded_file']['error'] == 0) {
        $allowed_extensions = ['png', 'jpg', 'pdf', 'xlsx'];
        $file_extension = pathinfo($_FILES['uploaded_file']['name'], PATHINFO_EXTENSION);

        if (in_array($file_extension, $allowed_extensions)) {
            $upload_dir = 'uploads/';
            $file_name =  uniqid() . '.' . $file_extension;
            $file_path = $upload_dir . $file_name;

            if (!move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $file_path)) {
                $upload_success = false;
                echo "<script>alert('파일 업로드 중 오류가 발생했습니다.');</script>";
            }
        } else {
            $upload_success = false;
            echo "<script>alert('허용되지 않은 파일 형식입니다.');</script>";
        }
    }

    // 파일 업로드가 성공한 경우에만 게시글 저장
    if ($upload_success) {
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, board_id, title, content) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $board_id, $title, $content]);

        // 게시글 ID 가져오기
        $post_id = $pdo->lastInsertId();

        // 파일 정보 데이터베이스에 저장 (파일 업로드가 성공했을 경우)
        if (isset($file_path)) {
            $stmt = $pdo->prepare("INSERT INTO uploads (post_id, file_name, file_path) VALUES (?, ?, ?)");
            $stmt->execute([$post_id, $_FILES['uploaded_file']['name'], $file_path]);
        }

        echo "<script>alert('게시글이 성공적으로 작성되었습니다.'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('게시글 작성이 취소되었습니다. 허용된 파일 형식을 사용해주세요.'); window.history.back();;</script>";
    }

    exit();
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>글쓰기</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=New+Amsterdam&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="styles/base.css"> 
</head>
<body>
    <?php require_once 'header.php' ?>

    <h1>글쓰기</h1>

    <form method="post" action="write_post.php" enctype="multipart/form-data">
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

        <label for="uploaded_file">파일 업로드 (PNG, JPG, PDF, XLSX)</label>
        <input type="file" id="uploaded_file" name="uploaded_file"><br>

        <button type="submit">글 작성</button>
    </form>

    <button onclick="location.href='index.php'">홈으로 돌아가기</button>
</body>
</html>
