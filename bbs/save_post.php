<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>글 저장</title>
    <link rel="stylesheet" href="css/shared.css">
</head>
<body>
    <h1>자유 게시판 &gt; 글 저장</h1>
    <div class="message-box">
        <?php
        $servername = getenv('DB_HOST');
		$username = getenv('DB_USER');
		$password = getenv('DB_PASS');
		$dbname = getenv('DB_NAME'); 

        // MySQL 연결
        $conn = new mysqli($servername, $username, $password, $dbname);

        // 연결 확인
        if ($conn->connect_error) {
            die("<p class='error'>Connection failed: " . $conn->connect_error . "</p>");
        }

        // 폼 데이터 가져오기
        $name = $_POST['name'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // 비밀번호 해시(brypt)
        $title = $_POST['title'];
        $content = $_POST['content'];

		$title = htmlspecialchars($title, ENT_QUOTES);
		$content = htmlspecialchars($content, ENT_QUOTES);

        // 데이터베이스에 삽입
        $sql = "INSERT INTO freeboard (name, password, title, content) VALUES ('$name', '$password', '$title', '$content')";

        if ($conn->query($sql) === TRUE) {
            echo "<p class='success'>새 글이 성공적으로 저장되었습니다.</p>";
        } else {
            echo "<p class='error'>Error: " . $sql . "<br>" . $conn->error . "</p>";
        }

        // 연결 종료
        $conn->close();
        ?>
        <div class="button-group">
            <button type="button" onclick="location.href='write.php'">글쓰기 페이지로 돌아가기</button>
            <button type="button" onclick="location.href='list.php'">목록보기</button>
        </div>
    </div>
</body>
</html>
