# GuanJoer' Community

해당 프로젝트는 `PHP`, `MySQL`을 주요 기술 스택으로 사용하여 만든 **커뮤니티 웹 사이트**입니다.

### 주요 기능

- 회원 가입 및 로그인(세션 기반 인증)
- 게시글 CRUD(Create/Read/Update/Delete)
- 파일 업로드 기능
- 파일 다운로드 기능
- 댓글 기능
- 게시판 기능
- 프로필 수정 기능(프로필 사진/비밀번호/아이디/이메일)
- 게시글 검색 기능
- 관리자 대시보드 기능(사용자/게시판/게시글 관리)


### 공격에 대한 대응 로직

#### XSS

- `htmlspecialchars` 함수를 사용하여 사용자 입력값의 입력과 출력을 이스케이프 처리하여 **XSS** 공격에 대한 대응 로직 구현

#### SQL Injection

- **Prepared Statements**를 사용하여 SQL 쿼리와 사용자 입력 값을 분리하여 **SQL Injection** 공격에 대한 대응 로직 구현

```php
$stmt = $pdo->prepare("INSERT INTO posts (user_id, board_id, title, content) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $board_id, $title, $content]);
```

#### CSRF Attack

- `CSRF Token`을 사용하여 **CSRF** 공격에 대한 대응 로직 구현

```php
// CSRF Token 생성
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// form 태그 내 CSRF Token 추가
<?php echo '<input type="hidden" name="_csrf" value="' . $_SESSION['csrf_token'] . '">'; ?>

// CSRF Token 검증 진행
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if($_SESSION['csrf_token'] != $_POST['_csrf']) {
        echo "<script>alert('잘못된 접근입니다.'); history.back();</script>";
    }
	// 이후 로직...
}
?>
```

#### File Upload Vulnerability

- 파일 업로드 시, 파일 이름 **난수화**, **화이트리스트 기반**의 파일 **확장자**, **MIME type**의 검증 및 **.htaccess** 설정을 통해 **php** 파일이 **실행** 되지 않도록 하여 **파일 업로드 취약점**에 대한 대응 로직 구현

```php
$upload_success = true;
    if (isset($_FILES['uploaded_file']) && $_FILES['uploaded_file']['error'] == 0) {
        // 화이트리스트 기반 파일 확장자, MIME type 검증
        $allowed_extensions = ['png', 'jpg', 'pdf', 'xlsx'];
        $allowed_mime_types = ['image/png', 'image/jpeg', 'application/pdf', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

        // 파일 정보 추출
        $file_extension = pathinfo($_FILES['uploaded_file']['name'], PATHINFO_EXTENSION);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $_FILES['uploaded_file']['tmp_name']);

        if (in_array($file_extension, $allowed_extensions) && in_array($mime_type, $allowed_mime_types)) {
            $upload_dir = 'uploads/';
            $file_name =  uniqid() . '.' . $file_extension; // 파일 이름 난수화
            $file_path = $upload_dir . $file_name;

            if (!move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $file_path)) {
                $upload_success = false;
                echo "<script>alert('파일 업로드 중 오류가 발생했습니다.'); history.back();</script>";
            }
        } else {
            $upload_success = false;
            echo "<script>alert('허용되지 않은 파일 형식입니다.'); history.back();</script>";
        }
    }
```

```bash
# .htaccess
<FilesMatch "\.(php|phtml|php3|php4|php5)$">
    Deny from all
</FilesMatch>
```
<!-- - **ROLE** 기반 접근 제어

	- 관리자 ROLE인 사용자의 경우에만 **관리자 페이지에 접근** 가능
	- 관리자인 경우에만 **물품의 추가, 수정, 삭제** 가능.
	- 관리자인 경우에만 개인 정보가 포함된 **모든 사용자의 주문 정보 열람** 가능
	- 관리자인 경우에만 **주문 정보 수정** 가능
	- 비 로그인 시에도 장바구니의 물품 추가 및 저장이 가능하나, **로그인을 해야만 물품 구매 진행이 가능**하도록 구현
	- 물품을 주문한 사용자 본인만이 자신의 물품 주문 정보 확인 가능 -->