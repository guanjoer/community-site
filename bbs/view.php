<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>글 보기</title>
    <link rel="stylesheet" href="css/shared.css">
</head>
<body>
    <h1 id="view-header">자유 게시판 &gt; 글 보기</h1>
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

    // 글 ID 가져오기
    $id = intval($_GET['id']);

    // 글 정보 가져오기
    $sql = "SELECT name, title, content, regist_date FROM freeboard WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($name, $title, $content, $regist_date);
    $stmt->fetch();

    if ($title) {
        $formatted_date = date('Y-m-d H:i', strtotime($regist_date));
        echo "<h2>" . htmlspecialchars($title) . "</h2>";
        echo "<p><strong>글쓴이:</strong> " . htmlspecialchars($name) . "</p>";
        echo "<p><strong>작성일:</strong> " . $formatted_date . "</p>";
        echo "<hr>";
        echo "<p>" . nl2br(htmlspecialchars($content)) . "</p>";
    } else {
        echo "<p class='error'>글을 찾을 수 없습니다.</p>";
    }

    // 연결 종료
    $stmt->close();
    $conn->close();
    ?>
    <div class="button-group">
        <button type="button" onclick="location.href='list.php'">목록으로</button>
    </div>
</body>
</html>
