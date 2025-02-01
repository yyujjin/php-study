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
