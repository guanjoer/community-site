<?php
session_start();

// 로그인 여부 확인
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// DB 연결 설정
$host = getenv('DB_HOST');
$db = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// 사용자 정보 가져오기
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username, email, profile_image, password FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// 프로필 업데이트 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $profile_image = $user['profile_image']; // 기존 이미지

    // 프로필 이미지 업로드 처리
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $imageFileType = strtolower(pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        // 파일 유형 체크
        if (in_array($imageFileType, $allowed_types)) {
            // 기존 이미지 파일 삭제
            if (!empty($profile_image) && file_exists($target_dir . $profile_image)) {
                unlink($target_dir . $profile_image);
            }

            // 새로운 이미지 파일 저장
            $new_filename = uniqid() . "." . $imageFileType;
            $target_file = $target_dir . $new_filename;
            if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                $profile_image = $new_filename;
            } else {
                echo "파일 업로드 중 오류가 발생했습니다.";
            }
        } else {
            echo "허용되지 않는 파일 형식입니다.";
        }
    }

    // 비밀번호 변경 처리
    if (!empty($_POST['current_password']) && !empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // 현재 비밀번호 검증
        if (password_verify($current_password, $user['password'])) {
            // 새로운 비밀번호 확인
            if ($new_password === $confirm_password) {
                // 비밀번호 해시화
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

                // 비밀번호 업데이트
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$new_password_hash, $user_id]);

                echo "<script>alert('비밀번호가 성공적으로 변경되었습니다.');</script>";
            } else {
                echo "<script>alert('새 비밀번호가 일치하지 않습니다.');</script>";
            }
        } else {
            echo "<script>alert('현재 비밀번호가 올바르지 않습니다.');</script>";
        }
    }

    // 사용자 정보 업데이트
    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, profile_image = ? WHERE id = ?");
    $stmt->execute([$username, $email, $profile_image, $user_id]);

    // 업데이트된 정보를 세션에 반영
    $_SESSION['username'] = $username;

    // 사용자에게 성공 메시지 표시
    echo "<script>alert('프로필이 성공적으로 업데이트되었습니다.'); window.location.href='mypage.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>마이페이지</title>
    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function(){
                const output = document.getElementById('profile-preview');
                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</head>
<body>
    <h1>마이페이지</h1>

    <form method="post" action="mypage.php" enctype="multipart/form-data">
        <label for="username">아이디</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required><br>

        <label for="email">이메일</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br>

        <label for="profile_image">프로필 이미지</label><br>
        <img id="profile-preview" src="uploads/<?php echo !empty($user['profile_image']) ? htmlspecialchars($user['profile_image']) : 'default.png'; ?>" alt="프로필 이미지" width="100" height="100"><br>
        <input type="file" id="profile_image" name="profile_image" accept="image/*" onchange="previewImage(event)"><br>

        <h2>비밀번호 변경</h2>
        <label for="current_password">현재 비밀번호</label>
        <input type="password" id="current_password" name="current_password"><br>

        <label for="new_password">새 비밀번호</label>
        <input type="password" id="new_password" name="new_password"><br>

        <label for="confirm_password">새 비밀번호 확인</label>
        <input type="password" id="confirm_password" name="confirm_password"><br>

        <button type="submit">정보 수정</button>
    </form>

    <button onclick="location.href='index.php'">홈으로 돌아가기</button>
</body>
</html>
