<?php
include $_SERVER['DOCUMENT_ROOT'] . "/includes/DB.php";

//일정 추가
$data=json_decode(file_get_contents("php://input"),true);

if(!empty($data)){
    
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




