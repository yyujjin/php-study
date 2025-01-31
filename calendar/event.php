<?php
include $_SERVER['DOCUMENT_ROOT'] . "/includes/DB.php";


$data=json_decode(file_get_contents("php://input"),true);
$event = $data['event'];
$createdDate = $date['createdDate'];

if(isset($event)&&isset($createdDate)){
    addEvent($event,$createdDate);
}

echo "success";