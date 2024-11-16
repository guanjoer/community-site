<?php
$base_dir = 'C:/xampp/htdocs/community_site/uploads/'; // 파일이 저장된 디렉토리
$file = $_GET['file'] ?? '';


$safe_file = basename($file);

$file_path = str_replace('\\', '/', realpath($base_dir . $safe_file));

if (!$file_path || strpos($file_path, $base_dir) !== 0 || !file_exists($file_path)) {
    // error_log("File path: $file_path"); 
    // error_log("Base directory: $base_dir");
    die("Unauthorized access");
}

// 파일 다운로드 처리
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
readfile($file_path);
exit;
?>
