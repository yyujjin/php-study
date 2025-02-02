<?php
include __DIR__ . '/../config/config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인 페이지</title>
    <link rel="stylesheet" href="index.css">
</head>

<body>
    <div class="login-container">
        <h1>Login</h1>

        <form action="/oauth-project/login.php" method="post">
            아이디 : <input id="idInput" type="text" required name="userId">
            비밀번호 : <input id="pwInput" type="password" required name="userPw">
            <button>로그인</button>
        </form>
        <br><br><br>

        <a href="/oauth-project/register.html" class="register-button">회원 가입</a><br><br><br>

        <!-- 카카오 -->
        <a
            href="https://kauth.kakao.com/oauth/authorize?response_type=code&client_id=<?= KAKAO_CLIENT_ID ?>&redirect_uri=<?= KAKAO_REDIRECT_URL?>" class="kakao-login-button">
            kakao login
        </a>
        <!-- 네이버 -->
        <a
            href="https://nid.naver.com/oauth2.0/authorize?response_type=code&client_id=<?= NAVER_CLIENT_ID ?>&redirect_uri=<?= KAKAO_REDIRECT_URL ?>&response_type=code&state=naver"
            class="naver-login-button">
            naver login
        </a>
    </div>
</body>


</html>
