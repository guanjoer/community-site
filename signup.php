<?php
// 세션 시작
session_start();

// DB 연결 설정
$host = getenv('DB_HOST');
$db = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$charset = 'utf8mb4';

// Data Source Name
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
// 에러 처리, 쿼리 결과 연관 배열로 반환, Prepared Staments 사용
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

// 회원가입 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $pass_confirm = $_POST['pass-confirm'];

    // 비밀번호 확인
    if ($password !== $pass_confirm) {
        echo "<script>alert('비밀번호가 일치하지 않습니다. 다시 확인해주세요.'); window.history.back();</script>";
        exit();
    }

    $password_hashed = password_hash($password, PASSWORD_BCRYPT);

    // 중복 체크 - 이메일
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        echo "<script>alert('이미 존재하는 이메일입니다. 다른 이메일을 사용해주세요.'); window.history.back();</script>";
        exit();
    }

    // 중복 체크 - 사용자명
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetchColumn() > 0) {
        echo "<script>alert('이미 존재하는 아이디입니다. 다른 아이디를 사용해주세요.'); window.history.back();</script>";
        exit();
    }

    // 사용자 등록
    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username, $email, $password_hashed]);

    // 세션에 사용자 이름 저장
    $_SESSION['username'] = $username;

    // 회원가입 후 리다이렉트
    header("Location: signup_success.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>회원가입</title>
    <script>
        function checkUsername() {
            const username = document.getElementById('username').value;
            if (username === '') {
                alert('아이디를 입력해주세요.');
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'check_username.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.available) {
                        alert('사용 가능한 아이디입니다.');
                    } else {
                        alert('이미 사용 중인 아이디입니다. 다른 아이디를 입력해주세요.');
                        document.getElementById('username').focus();
                    }
                }
            };
            xhr.send('username=' + encodeURIComponent(username));
        }

        function validateForm() {
            const password = document.getElementById('password').value;
            const passConfirm = document.getElementById('pass-confirm').value;

            if (password !== passConfirm) {
                alert('비밀번호가 일치하지 않습니다. 다시 확인해주세요.');
                document.getElementById('pass-confirm').focus();
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <h2>회원가입</h2>
    <form method="post" action="register.php" onsubmit="return validateForm();">
        <label for="email">이메일</label>
        <input type="email" id="email" name="email" required><br>

        <label for="username">아이디</label>
        <input type="text" id="username" name="username" required>
        <button type="button" onclick="checkUsername()">중복 확인</button><br>

        <label for="password">비밀번호</label>
        <input type="password" id="password" name="password" required><br>

        <label for="pass-confirm">비밀번호 확인</label>
        <input type="password" id="pass-confirm" name="pass-confirm" required><br>

        <button type="submit">가입하기</button>
    </form>
    <p>이미 회원이신가요? <a href="login.php">로그인</a></p>
</body>
</html>
