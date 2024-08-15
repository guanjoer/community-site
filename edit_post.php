<?php
session_start();

require_once 'config/db.php';

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

    // 기존 파일 정보 가져오기
    $stmt = $pdo->prepare("SELECT * FROM uploads WHERE post_id = ?");
    $stmt->execute([$post_id]);
    $file = $stmt->fetch();
} else {
    header("Location: index.php");
    exit();
}

// 게시글 수정 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    // 파일 수정 처리
    $upload_success = true;
    $new_file_name = null;

    if (isset($_FILES['uploaded_file']) && $_FILES['uploaded_file']['error'] == 0) {
        $allowed_extensions = ['png', 'PNG', 'jpg', 'pdf', 'xlsx'];
        $file_extension = pathinfo($_FILES['uploaded_file']['name'], PATHINFO_EXTENSION);

        if (in_array($file_extension, $allowed_extensions)) {
            $upload_dir = 'uploads/';
            $new_file_name = uniqid() . '.' . $file_extension;
            $new_file_path = $upload_dir . $new_file_name;

            if (!move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $new_file_path)) {
                $upload_success = false;
                echo "<script>alert('파일 업로드 중 오류가 발생했습니다.');</script>";
            } else {
                // 기존 파일이 있으면 삭제
                if ($file && file_exists($file['file_path'])) {
                    unlink($file['file_path']);
                }
            }
        } else {
            $upload_success = false;
            echo "<script>alert('허용되지 않은 파일 형식입니다.');</script>";
        }
    }

    if ($upload_success) {
        // 게시글 업데이트
        $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
        $stmt->execute([$title, $content, $post_id]);

        // 파일 정보 업데이트 또는 삽입
        if ($new_file_name) {
            if ($file) {
                // 기존 파일 정보 업데이트
                $stmt = $pdo->prepare("UPDATE uploads SET file_name = ?, file_path = ? WHERE id = ?");
                $stmt->execute([$_FILES['uploaded_file']['name'], $new_file_path, $file['id']]);
            } else {
                // 새로운 파일 정보 삽입
                $stmt = $pdo->prepare("INSERT INTO uploads (post_id, file_name, file_path) VALUES (?, ?, ?)");
                $stmt->execute([$post_id, $_FILES['uploaded_file']['name'], $new_file_path]);
            }
        }

        echo "<script>alert('게시글이 성공적으로 수정되었습니다.'); window.location.href='post.php?id=$post_id';</script>";
    }

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

    <form method="post" action="edit_post.php?id=<?php echo htmlspecialchars($post_id); ?>" enctype="multipart/form-data">
        <label for="title">제목</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required><br>

        <label for="content">내용</label>
        <textarea id="content" name="content" rows="10" required><?php echo htmlspecialchars($post['content']); ?></textarea><br>

        <label for="uploaded_file">파일 업로드 (PNG, JPG, PDF, XLSX)</label><br>
        <?php if ($file): ?>
            <p>현재 파일: <a href="<?php echo htmlspecialchars($file['file_path']); ?>" download><?php echo htmlspecialchars($file['file_name']); ?></a></p>
            <input type="file" id="uploaded_file" name="uploaded_file"><br>
            <p>* 파일을 새로 업로드하면 기존 파일이 교체됩니다.</p>
        <?php else: ?>
            <input type="file" id="uploaded_file" name="uploaded_file"><br>
        <?php endif; ?>

        <button type="submit">수정하기</button>
    </form>

    <button onclick="location.href='post.php?id=<?php echo $post_id; ?>'">게시글로 돌아가기</button>
</body>
</html>
