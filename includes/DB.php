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

//일정 수정
function editEvent($id,$event){
    $conn = connectDB();
    $stmt = $conn->prepare("UPDATE yujin.calendar SET events = ? WHERE id = ?");
    $stmt->bind_param("sd", $event,$id);
    if (!$stmt->execute()) {
        echo "오류: " . $stmt->error;
    }

    // 변경된 행 개수 확인
    $updatedRows = $stmt->affected_rows;

    if ($updatedRows > 0) {
        echo "$updatedRows 개의 행이 업데이트되었습니다!";
    } else {
        echo "업데이트된 행이 없습니다.";
    }
}
//일정 삭제
?>
