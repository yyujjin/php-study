<?php
include $_SERVER['DOCUMENT_ROOT'] . "/includes/DB.php";


$data=json_decode(file_get_contents("php://input"),true);

//body 데이터가 있을때만 실행하게
if(!empty($data)){
    //일정 추가
    $event = $data['event'];
    $createdDate = $data['createdDate'];

    if(isset($event)&&isset($createdDate)){
        try {
            addEvent($event,$createdDate);
            echo "success";
        } catch (Exception $e) {
            echo $e; //예외를 어떻게 해줘야하지, 그냥 메시지 띄우면 되나?
        }
    }

    $id = $data['id'];
    $event = $data['event']; //s 붙여야하나

    //일정 수정
    if(isset($id)&&isset($event)){
        try {
            editEvent($id,$event);
            echo "success";
        } catch (Exception $e) {
            echo $e; //예외를 어떻게 해줘야하지, 그냥 메시지 띄우면 되나?
        }
    }
}

//일정 조회
if(isset($_GET['createdDate'])){ 
    try{
        $events = getEvents($_GET['createdDate']);
        echo json_encode($events);
    } catch(Exception $e) {
        echo $e;
    }
}


//일정 수정 test




