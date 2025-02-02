<?php
include __DIR__ . '/../database/oauthDB.php';

if (strlen($_POST['kakaoId']) > 0) {
    $provider = 'kakaoId';
    $providerId=$_POST['kakaoId'];
}
if (strlen($_POST['naverId']) > 0) {
    $provider = 'naverId';
    $providerId=$_POST['naverId'];
}

//여기 디비가 안됨
$result = findUserByUserId($userId);

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc(); // 단일 행 가져오기
    $hashedPw = $data['userPw'];   // 데이터베이스에 저장된 해시 비밀번호

    // 비밀번호 검증
    if (password_verify($_POST['userPw'], $hashedPw)) {

        $userId = $data['userId'];
        //소셜 아이디가 db에 없다면 저장
        if (is_null($data["$provider"])) {
            linkSocialAccountIfNotExists($provider,$providerId,$userId);
        }

        echo '
        <form id="userData" method="POST" action="/oauth-project/success.php">
            <input type="hidden" name="userName" value="' . htmlspecialchars($data['userName']) . '">
        </form>
        <script>
            alert("로그인 성공! 메인 페이지로 이동합니다.");
            document.getElementById("userData").submit();
        </script>
        ';
    } else {

        echo "<script>
            alert('로그인 실패! 아이디와 비밀번호를 확인해주세요.');
            window.location.href = '/oauth-project/index.php';
        </script>";
    }
} else {
    echo "<script>
            alert('로그인 실패! 아이디와 비밀번호를 확인해주세요.ddddddd');
            window.location.href = '/oauth-project/index.php';
        </script>";
}

