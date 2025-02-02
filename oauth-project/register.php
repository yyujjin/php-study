<?php
include __DIR__ . '/../database/oauthDB.php';

$userId = $_POST['userId'] ?? ''; //$_POST['userId']가 존재하고 null이 아닐 때 //빈 문자열은 false로 평가됨
$userPw = password_hash($_POST['userPw'], PASSWORD_DEFAULT);
$userName = $_POST['userName'] ?? '';
$email = $_POST['email'] ?? '';
$birthday = $_POST['birthday'] ?? '';
$createdDate = date('Y-m-d');


//유저아이디 중복인지 확인
$result = isExistUserId($userId);

if ($result->num_rows > 0) {
    echo "<script>
        alert('이미 사용 중인 아이디입니다. 다른 아이디를 사용해주세요. ');
        window.location.href = '/oauth-project/register.html';
    </script>";
}

createUser($userId, $userPw, $userName, $email, $birthday, $createdDate);

echo "<script>
        alert('회원가입이 완료되었습니다. 로그인 페이지로 이동합니다.');
        window.location.href = '/oauth-project/index.php';
    </script>";