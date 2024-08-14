<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>글 목록</title>
    <link rel="stylesheet" href="css/shared.css">
</head>
<body>
    <h1 id="list-header">자유 게시판 &gt; 글 목록</h1>
    <table>
        <thead>
            <tr>
                <th>번호</th>
                <th>제목</th>
                <th>글쓴이</th>
                <th>작성일</th>
            </tr>
        </thead>
        <tbody>
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

            // 글 목록 가져오기
            $sql = "SELECT id, name, title, regist_date FROM freeboard ORDER by id DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
				$counter = $result->num_rows;
                while($row = $result->fetch_assoc()) {
					$formatted_date = date('Y-m-d H:i', strtotime($row['regist_date']));
                    echo "<tr>";
                    echo "<td>".$counter."</td>";
                    echo "<td><a href='view.php?id=".$row['id']."'>".$row['title']."</a></td>";  // 제목에 링크 추가
                    echo "<td>".$row['name']."</td>";
                    echo "<td>".$formatted_date."</td>";
                    echo "</tr>";
					$counter -= 1;
                }
            } else {
                echo "<tr><td colspan='4'>등록된 글이 없습니다.</td></tr>";
            }

            // 연결 종료
            $conn->close();
            ?>
        </tbody>
    </table>
    <div class="button-group">
        <button type="button" onclick="location.href='write.php'">글쓰기</button>
    </div>
</body>
</html>
