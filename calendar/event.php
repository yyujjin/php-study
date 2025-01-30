<?php
include $_SERVER['DOCUMENT_ROOT'] . "/includes/DB.php";

$event = $_GET['event'];
$createdDate = $_GET['date'];

addEvent($event,$createdDate);

echo "success";