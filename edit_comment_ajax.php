<?php
session_start();

// 로그인 여부 확인
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => '로그인 필요']);
    exit();
}

require_once 'config/db.php';

// 댓글 수정 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment_id = $_POST['id'];
    $content = $_POST['content'];

    // 댓글 정보 가져오기
    $stmt = $pdo->prepare("SELECT * FROM comments WHERE id = ?");
    $stmt->execute([$comment_id]);
    $comment = $stmt->fetch();

    if (!$comment) {
        echo json_encode(['success' => false, 'message' => '존재하지 않는 댓글']);
        exit();
    }

    // 작성자 또는 관리자 여부 확인
    if ($comment['user_id'] != $_SESSION['user_id'] && $_SESSION['role'] !== 'admin') {
        echo json_encode(['success' => false, 'message' => '권한 없음']);
        exit();
    }

    // 댓글 수정
    $stmt = $pdo->prepare("UPDATE comments SET content = ? WHERE id = ?");
    $stmt->execute([$content, $comment_id]);

    echo json_encode(['success' => true, 'message' => '댓글 수정 성공']);
    exit();
} else {
    echo json_encode(['success' => false, 'message' => '잘못된 요청']);
    exit();
}
?>
