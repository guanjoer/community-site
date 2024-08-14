<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>글쓰기</title>
    <link rel="stylesheet" href="css/shared.css">
    <script>
        function validateForm() {
            const nameField = document.getElementById('name');
            const passwordField = document.getElementById('password');
            const titleField = document.getElementById('title');
            const contentField = document.getElementById('content');

            const name = nameField.value.trim();
            const password = passwordField.value.trim();
            const title = titleField.value.trim();
            const content = contentField.value.trim();

            if (!name) {
                alert('이름을 입력하세요.');
                nameField.focus();
                return false;
            }

            if (!password) {
                alert('비밀번호를 입력하세요.');
                passwordField.focus();
                return false;
            }

            if (password.length < 8) {
                alert('비밀번호는 최소 8자 이상이어야 합니다.');
                passwordField.focus();
                return false;
            }

            if (!title) {
                alert('제목을 입력하세요.');
                titleField.focus();
                return false;
            }

            if (!content) {
                alert('내용을 입력하세요.');
                contentField.focus();
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <h1>자유 게시판 &gt; 글쓰기</h1>
    <form action="save_post.php" method="POST" onsubmit="return validateForm()">
        <div class="form-group">
            <label for="name">이름</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="password">비밀번호</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="title">제목</label>
            <input type="text" id="title" name="title" required>
        </div>
        <div class="form-group">
            <label for="content">내용</label>
            <textarea id="content" name="content" rows="10" required></textarea>
        </div>
        <div class="button-group">
            <input type="submit" value="저장하기">
            <button type="button" onclick="location.href='list.php'">목록보기</button>
        </div>
    </form>
</body>
</html>
