<?php

// 일정 배열
$planArr = [
    ['start' => '2024-12-15', 'end' => '2025-02-15', 'label' => '일정1', 'repeatType' => 'week', 'repeatData' => '수'],
    ['start' => '2025-02-03', 'end' => '2025-02-20', 'label' => '일정2', 'repeatType' => 'week', 'repeatData' => '목'],
    ['start' => '2023-09-15', 'end' => '2023-09-20', 'label' => '일정3', 'repeatType' => 'week', 'repeatData' => '금'],
    ['start' => '2023-09-10', 'end' => '2023-09-20', 'label' => '일정4', 'repeatType' => 'week', 'repeatData' => '월'],
];


//달력만들기 
createCalendar($planArr);





//일정 배열의 기간에 맞게 달력을 만드는 함수
function createCalendar($planArr)
{

    $startArr = array_column($planArr, 'start');
    $endArr = array_column($planArr, 'end');
    array_multisort($startArr, $endArr);
    $madeCalendar = [];

    for ($i = 0; $i < count($planArr); $i++) {

        $startTime = new DateTime($startArr[$i]);
        $endTime = new DateTime($endArr[$i]);

        while ($startTime->format('Y-m') <= $endTime->format('Y-m')) {

            if (!in_array($startTime->format('Y-m'), $madeCalendar)) {
                $madeCalendar[] = $startTime->format('Y-m');
                calendarForm($planArr, $startTime->format('Y'), $startTime->format('m'));
            }
            $startTime->modify('+1 month');
        }
    }
}



//파라미터로 들어오는 현재 날짜의 일정을 모두 가져와서 출력하는 함수 
function getLabel($planArr, $currentDay)
{
    $labels = [];

    foreach ($planArr as $plan) {
        if ($currentDay >= $plan['start'] && $currentDay <= $plan['end']) {
            $labels[] = $plan['label'];
        }
    }

    return implode("<br>", $labels);
}



//년, 월을 파라미터로 넣으면 달력을 만들어주는 함수
function calendarForm($planArr, $year, $month)
{
    $dateTime = new DateTime("$year-$month-01");
    $day = 1;
    $lastDay = $dateTime->format('t');
    $startDate = $dateTime->format('w');
    $totalWeek = ceil(($startDate + $lastDay) / 7);

    echo "<h3>$year 년 " . $dateTime->format('n') . "월</h3>";
    echo "<table border=1>";
    echo "<tr><th>일</th><th>월</th><th>화</th><th>수</th><th>목</th><th>금</th><th>토</th></tr>";

    for ($i = 0; $i < $totalWeek; $i++) {
        echo "<tr>";
        for ($j = 0; $j <= 6; $j++) {
            if ($i == 0 && $j < $startDate) {
                echo "<td></td>";
            } elseif ($day <= $lastDay) {
                $currentDay = sprintf("%04d-%02d-%02d", $year, $month, $day);
                $labels = getLabel($planArr, $currentDay); //날짜에 해당하는 라벨 가져오기
                echo "<td" . getStyle($planArr, $currentDay) . ">$day<br>$labels</td>"; //스타일 적용
                $day++;
            } else {
                echo "<td></td>";
            }
        }
        echo "</tr>";
    }
    echo "</table>";
}



//넘어오는 현재 날짜와 일정 배열에 있는 조건을 검사해서 스타일 입히는 함수
function getStyle($planArr, $currentDay)
{

    $dates = []; //요일 저장
    $duplicateDay = 0;
    $isBold = "";
    $bgColor = "";
    foreach ($planArr as $plan) {
        if ($currentDay >= $plan['start'] && $currentDay <= $plan['end']) {

            $duplicateDay++;

            if ($plan['repeatType'] == 'week') {

                $date = convertNumTheDate($plan['repeatData']);

                if (!in_array($date, $dates)) {
                    $dates[] = $date;
                }
            }
        }
    }

    if ($duplicateDay > 1) {
        $bgColor = "background-color: yellow;";
    }
    if ($duplicateDay == 1) {
        $bgColor = "background-color: red;";
    }

    //볼드체 적용 (현재 날짜의 요일이 요일 배열에 있다면)
    if (in_array(date('w', strtotime($currentDay)), $dates)) {
        $isBold = "font-weight: bold;";
    }

    return ' style="' . $bgColor . $isBold . '"';
}




//텍스트 요일을 숫자로 바꾸는 함수
function convertNumTheDate($date)
{

    switch ($date) {
        case '일':
            return 0;
        case '월':
            return 1;
        case '화':
            return 2;
        case '수':
            return 3;
        case '목':
            return 4;
        case '금':
            return 5;
        case '토':
            return 6;
    }
}
