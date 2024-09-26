<?php
session_set_cookie_params([
    'httponly' => true, 
    'samesite' => 'Lax' // Cross-site 요청에 대한 보호(Lax, Strict, None)
]);
session_start();

// 로그아웃 처리
if (isset($_GET['logout'])) {
    session_destroy(); // 모든 세션 데이터 삭제
    // header("Location: index.php"); // 메인 페이지로 리다이렉트
	echo "<script>
	window.location.href = document.referrer; // 이전 페이지로 리다이렉트
	</script>";
exit;
}
?>