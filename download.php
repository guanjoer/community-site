<?php
session_start();
require_once 'config/db.php';

// 게시글 ID와 파일 ID가 있는지 확인
if (isset($_GET['post_id']) && isset($_GET['file_id'])) {
    $post_id = (int) $_GET['post_id'];   // 게시글 ID
    $file_id = (int) $_GET['file_id'];   // 파일 ID

    // 파일 정보 가져오기 (post_id와 file_id가 일치하는 파일 찾기)
    $stmt = $pdo->prepare("SELECT * FROM uploads WHERE post_id = ? AND id = ?");
    $stmt->execute([$post_id, $file_id]);
    $file = $stmt->fetch();

    if ($file) {
        // 파일 경로 설정
		$base_dir = 'C:/xampp/htdocs/community_site/';

		$combined_path = $base_dir . $file['file_path'];
		// var_dump($combined_path);
		
		// var_dump(file_exists($combined_path));  // 파일이 실제로 존재하는지 확인
        $file_path = realpath($base_dir . $file['file_path']);
		// var_dump($file_path);
		// echo $file_path;

        // 경로 검증 및 파일 존재 확인
        if (file_exists($combined_path) && strpos($combined_path, $base_dir) === 0 && file_exists($file_path)) {
            // 파일 다운로드를 위한 헤더 설정
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
            header('Content-Length: ' . filesize($file_path));

            // 파일을 읽어서 클라이언트로 전송
            readfile($file_path);
            exit;
        } else {
            // 파일이 존재하지 않으면 404 에러
            header("HTTP/1.0 404 Not Found");
			include('./errors/404.php');
            exit;
        }
    } else {
        // 파일이 존재하지 않거나 post_id에 속하지 않을 경우 경고
        echo "<script>alert('파일을 찾을 수 없습니다.'); window.location.href='post.php?id=$post_id';</script>";
        exit;
    }
} else {
    // 파라미터가 없을 경우 404 처리
    header("HTTP/1.0 404 Not Found");
    exit;
}
?>
