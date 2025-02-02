
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>연동 페이지</title>
    <link rel="stylesheet" href="index.css">
</head>

<body>
    <div class="login-container">
        <h1>accountLinking</h1>

        <form action="/oauth-project/login.php" method="post">
            아이디 : <input id="idInput" type="text" required name="userId">
            비밀번호 : <input id="pwInput" type="password" required name="userPw">
            <input type="hidden" name="kakaoId" value="<?= $_POST['kakaoId'] ?>">
            <input type="hidden" name="naverId" value="<?= $_POST['naverId'] ?>">
            <button>로그인</button>
        </form>
    </div>
</body>

</html>
