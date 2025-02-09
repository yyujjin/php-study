<?php

//일정 배열
$dates = [
    ['start' => '2024-12-15',  'end' => '2025-01-05',  'label' => '일정1' ,  'repeat_type' => 'week',  'repeat_data' => '수'],
    ['start' => '2025-02-3',  'end' => '2025-02-29',  'label' => '일정2' ,  'repeat_type' => 'week',  'repeat_data' => '목'],
    ['start' => '2025-03-15',  'end' => '2025-04-05',  'label' => '일정3' ,  'repeat_type' => 'week',  'repeat_data' => '금'],
    ['start' => '2023-09-10',  'end' => '2023-09-20',  'label' => '일정4' ,  'repeat_type' => 'week',  'repeat_data' => '월'],
];


createCalendarsFromDates($dates);


//날짜 범위에 따라 달력을 생성하는 함수
function createCalendarsFromDates($dates){
    $startArr = array_column($dates, 'start');
    $endArr = array_column($dates, 'end');

    array_multisort($startArr,$endArr); //시작 날짜 기준으로 오름차순으로 정렬. 

    //중복 날짜 체크하기위해 만든 배열
    $createdPeriod=[];

    //dates의 배열크기만큼 돌아 
    for($i=0; $i<count($dates); $i++){
        //시작 년, 원 빼기
        $startPlanDay = new DateTime($startArr[$i]);
        $endPlanDay = new DateTime($endArr[$i]);

        //날짜 객체를 만들어서 문자열 포맷은 해도 비교는 문자열 순으로 하니까 2랑 09 하면 2가 큼
        while( $startPlanDay->format('Y-m') <= $endPlanDay->format('Y-m')){
            if(!in_array($startPlanDay->format('Y-m'), $createdPeriod)){
                $createdPeriod[] = $startPlanDay->format('Y-m');
                //달력만들때는 01 이런형식 필요없으니 비교할때만 00으로 
                createCalendar($startPlanDay->format('Y'),$startPlanDay->format('n'));
                
            }      
            //modify('+1 month')를 사용하면 자동으로 연도(year)가 증가하고 월(month)이 1로 변경됨
            //수동으로 if ($month == 12) { $year++; $month = 1; } 같은 코드 필요 없음!
            $startPlanDay->modify('+1 month');
        }
    }
}





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


