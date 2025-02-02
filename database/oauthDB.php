<?php
include __DIR__ . '/../config/config.php';

function connectDB(){
    $host = HOST; // 데이터베이스 서버 주소
    $username = USERNAME; // 데이터베이스 사용자명
    $password = PASSWORD; // 데이터베이스 비밀번호
    
    $conn = new mysqli($host,$username,$password);

    if($conn->connect_error){
        die("DB연결에 실패했습니다. : ".$conn->connect_error);
    }
    
    return $conn;
}

 //소셜 고유아이디로 기존 유저인지 조회
function findUserByProviderId($provider,$providerId){
    


    $conn = connectDB();
    $stmt = $conn->prepare("SELECT userName FROM yujin.user WHERE $provider = ?");
    $stmt->bind_param("s", $providerId);
    $stmt->execute();
    $result = $stmt->get_result();
   
    //연결 종료
   $stmt->close();
   $conn->close();

   return $result;

}

function findUserByUserId($userId){

    $conn = connectDB();
    $stmt = $conn->prepare("SELECT userName, userPw, kakaoId, naverId FROM yujin.user WHERE userId = ?");
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $stmt->close();
    $conn->close();

    return $result;
}
 
//기존 유저일 때 소셜 아이디가 db에 없을 경우 저장
function linkSocialAccountIfNotExists($provider,$providerId,$userId){
    $conn = connectDB();
    $stmt = $conn->prepare("UPDATE yujin.user SET $provider = ? WHERE userId = ?");
    $stmt->bind_param("ss", $providerId, $userId);

    if (!$stmt->execute()) {
        die("쿼리 실행 실패: " . $stmt->error);
    } else {
        echo "쿼리 실행 성공!";
    }

    $stmt->close();
    $conn->close();
}

//중복 아이디 검사
function isExistUserId($userId){
    $conn = connectDB();

    //중복 아이디 검사
    $stmt = $conn->prepare("SELECT * FROM yujin.user WHERE userId = ?");
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result =  $stmt->get_result();
    $stmt->close();
    $conn->close();
    return $result;
}

//회원가입
function createUser($userId, $userPw, $userName, $email, $birthday, $createdDate){
    $conn = connectDB();
    $stmt = $conn->prepare("INSERT INTO  (userId,userPw,userName,email,birthday,createdDate) VALUES (?,?,?,?,?,?)");

    $stmt->bind_param("ssssss", $userId, $userPw, $userName, $email, $birthday, $createdDate);
    $stmt->execute(); 
    $stmt->close();
    $conn->close();
}