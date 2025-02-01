<?php

//__DIR__ → calendarDB.php가 위치한 /project-root/database 디렉토리를 가리킴
//실행 위치와 상관없이 항상 올바른 파일을 불러올 수 있음
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


function addEvent($events,$createdDate){
    $conn = connectDB();
    $stmt = $conn->prepare("INSERT INTO yujin.calendar (events, createdDate) VALUES (?, ?)");

    $stmt->bind_param("ss", $events, $createdDate);

    if (!$stmt->execute()) {
        echo "오류: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

function getEvents($createdDate){
    $conn = connectDB();
    $stmt = $conn->prepare("SELECT * FROM yujin.calendar WHERE createdDate = ?");
    $stmt->bind_param("s", $createdDate);

    if (!$stmt->execute()) {
        echo "오류: " . $stmt->error;
    }
    $result = $stmt->get_result();

    // 결과 배열로 반환
    $events = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    $conn->close();

    return $events;
}

?>
