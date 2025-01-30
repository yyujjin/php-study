<?php

function connectDB(){
    $host = 'localhost:3306'; // 데이터베이스 서버 주소
    $username = 'root'; // 데이터베이스 사용자명
    $password = '1234'; // 데이터베이스 비밀번호
    
    $conn = new mysqli($host,$username,$password);

    if($conn->connect_error){
        die("DB연결에 실패했습니다. : ".$conn->connect_error);
    }
    
    return $conn;
}

function addEvent($events){
    $conn = connectDB();
    $stmt = $conn->prepare("INSERT INTO yujin.calendar (events, createdDate) VALUES (?, ?)");

    $createdDate = date('Y-m-d');
    $stmt->bind_param("ss", $events, $createdDate);

    if (!$stmt->execute()) {
        echo "오류: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}


?>
