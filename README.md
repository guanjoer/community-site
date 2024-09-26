# GuanJoer' Community

해당 프로젝트는 `PHP`, `MySQL`을 주요 기술 스택으로 사용하여 만든 **커뮤니티 웹 사이트**입니다.

---

**주요 기능:**

- 회원 가입 및 로그인(세션 기반 인증)
- 게시글 CRUD(Create/Read/Update/Delete)
- 파일 업로드 기능
- 파일 다운로드 기능
- 댓글 기능
- 게시판 기능
- 프로필 수정 기능(프로필 사진/비밀번호/아이디/이메일)
- 게시글 검색 기능
- 관리자 대시보드 기능(사용자/게시판/게시글 관리)

**공격에 대한 대응 로직:** 

- `htmlspecialchars` 함수를 사용하여 사용자 입력값을 이스케이프 처리하여 XSS 공격에 대한 대응 로직 존재
<!-- - **XSS**, **NoSQLi** 공격에 대한 대응 로직 존재
	- 동적 HTML 생성을 담당하는 `ejs`엔진을 사용하는 파일에서 `<%= %>` 사용
	- `express-session` 라이브러리를 통해 세션 관리 및 `httpOnly` 플래그 적용
	- 사용자 아이디에 `@`을 포함해야 하며, 비밀번호는 `bcrypt.compare(this.password, hashedPassword)`를 통해 검증
	- `express-mongo-sanitize` 혹은 `mongo-sanitize` 라이브러리를 사용하여 `$`, `.`과 같이 악의적인 사용자 입력값에 대해 **Sanitize**
- 물품 장바구니 추가, 수정, 삭제 시 `Ajax` 처리 및 `DOM` 업데이트
- **ROLE** 기반 접근 제어

	- 관리자 ROLE인 사용자의 경우에만 **관리자 페이지에 접근** 가능
	- 관리자인 경우에만 **물품의 추가, 수정, 삭제** 가능.
	- 관리자인 경우에만 개인 정보가 포함된 **모든 사용자의 주문 정보 열람** 가능
	- 관리자인 경우에만 **주문 정보 수정** 가능
	- 비 로그인 시에도 장바구니의 물품 추가 및 저장이 가능하나, **로그인을 해야만 물품 구매 진행이 가능**하도록 구현
	- 물품을 주문한 사용자 본인만이 자신의 물품 주문 정보 확인 가능 -->