<?php

//일정 배열
$dates = [
    ['start' => '2024-12-15',  'end' => '2025-01-05',  'label' => '일정1' ,  'repeat_type' => 'week',  'repeat_data' => '수'],
    ['start' => '2025-02-3',  'end' => '2025-02-29',  'label' => '일정2' ,  'repeat_type' => 'week',  'repeat_data' => '목'],
    ['start' => '2025-03-15',  'end' => '2025-04-05',  'label' => '일정3' ,  'repeat_type' => 'week',  'repeat_data' => '금'],
    ['start' => '2023-09-10',  'end' => '2025-09-20',  'label' => '일정4' ,  'repeat_type' => 'week',  'repeat_data' => '월'],
];


createCalendar(2025,12);

//먼저 단순히 달력만드는 함수
function createCalendar($year,$month){

$day = 1;
$lastDay = date('t',strtotime("$year-$month-01"));
$startDate = date('w',strtotime("$year-$month-01"));
$totalWeek = ceil(($lastDay+$startDate)/7);

echo "<h3>$year 년 $month 월</h3>";

    echo "<table border=1;>";
    echo "<tr>";
    echo "<th>일</th>";
    echo "<th>월</th>";
    echo "<th>화</th>";
    echo "<th>수</th>";
    echo "<th>목</th>";
    echo "<th>금</th>";
    echo "<th>토</th>";
    echo "</tr>";

    for($i=0; $i<$totalWeek; $i++){
        echo "<tr>";
        for($j=0; $j<=6; $j++){
            echo "<td>";
            if($i==0&&$j<$startDate){
                echo "";
            }elseif($day<=$lastDay){
                echo $day;
                $day++;
            }else{
                echo "";
            }
            echo "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}


