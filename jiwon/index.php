<?php

// 일정 배열
$planArr = [
    ['start' => '2024-12-15', 'end' => '2025-02-15', 'label' => '일정1', 'repeatType' => 'week', 'repeatData' => '수'],
    ['start' => '2025-02-03', 'end' => '2025-02-20', 'label' => '일정2', 'repeatType' => 'week', 'repeatData' => '목'],
    ['start' => '2023-09-15', 'end' => '2023-09-20', 'label' => '일정3', 'repeatType' => 'week', 'repeatData' => '금'],
    ['start' => '2023-09-10', 'end' => '2023-09-20', 'label' => '일정4', 'repeatType' => 'week', 'repeatData' => '월'],
];

createCalendarsFromDates($planArr);

//스타일 적용 함수
function getStyle($planArr, $currentDay)
{
    $repeatDays = []; // 반복 요일 저장
    $isBold = ""; // 볼드 스타일
    $bgColor = ""; // 배경색
    $isDuplicate = 0; // 중복 일정 카운트

    $day = new DateTime($currentDay);
    $currentDate = $day->format('w'); // 현재 날짜의 요일을 숫자로 변환 (0: 일요일 ~ 6: 토요일)

    //배열을 순서대로 반복하면서 들어온 날짜와 일치하는 조건을 확인함
    foreach ($planArr as $plan) {
        $startPlanDay = new DateTime($plan['start']);
        $endPlanDay = new DateTime($plan['end']);

        // 일정이 해당 날짜에 포함되는지 확인
        if ($day >= $startPlanDay && $day <= $endPlanDay) {

            //일정의 요일을 빼내서 요일을 숫자로 바꿈
            if ($plan['repeatType'] == 'week') {
                $convertedDay = changeTextDateToNumberDate($plan['repeatData']);
                //요일이 배열에 없다면 저장
                if (!in_array($convertedDay, $repeatDays)) {
                    $repeatDays[] = $convertedDay;
                }
            }

            $isDuplicate++;
        }
    }

    //볼드체 적용 (현재 날짜의 요일이 요일 배열에 있다면)
    if (in_array($currentDate, $repeatDays)) {
        $isBold = "font-weight: bold;";
    }

    //중복 일정 확인하여 배경색 결정 (1보다 크면 중복됨)
    if ($isDuplicate > 1) {
        $bgColor = "background-color: yellow;";
    } elseif ($isDuplicate == 1) { //else로 해버리면 모든 날짜가 빨간색이됨 
        $bgColor = "background-color: red;";
    }

    return ' style="' . $bgColor . $isBold . '"';
    // 있다면 : style="background-color: yellow; font-weight: bold;"
    // 없다면 : style=""
}



//해당 날짜에 포함되는 일정 라벨 가져오는 함수
function getLabel($planArr, $currentDay)
{
    $labels = [];

    foreach ($planArr as $plan) {
        $startPlanDay = $plan['start'];
        $endPlanDay = $plan['end'];
        //$day = new DateTime($currentDate);

        // 일정이 해당 날짜에 포함되는지 확인라벨 저장하기 
        if ($currentDay >= $startPlanDay && $currentDay <= $endPlanDay) {
            $labels[] = $plan['label'];
        }
    }

    return implode("<br>", $labels);
}

//날짜 범위에 따라 달력을 생성하는 함수
function createCalendarsFromDates($planArr)
{
    $startArr = array_column($planArr, 'start');
    $endArr = array_column($planArr, 'end');

    array_multisort($startArr, $endArr); // 시작 날짜 기준으로 정렬

    // 중복 날짜 체크 배열
    $createdPeriod = [];

    for ($i = 0; $i < count($planArr); $i++) {
        $startPlanDay = new DateTime($startArr[$i]);
        $endPlanDay = new DateTime($endArr[$i]);

        //날짜 비교할 때 `Y-m`으로 변환하여 비교
        while ($startPlanDay->format('Y-m') <= $endPlanDay->format('Y-m')) {
            if (!in_array($startPlanDay->format('Y-m'), $createdPeriod)) {
                $createdPeriod[] = $startPlanDay->format('Y-m');
                createCalendar($planArr, $startPlanDay->format('Y'), $startPlanDay->format('n'));
            }
            $startPlanDay->modify('+1 month');
        }
    }
}

//달력 생성 함수
function createCalendar($planArr, $year, $month)
{
    $day = 1;
    $dateTime = new DateTime("$year-$month-01");
    $lastDay = $dateTime->format('t');
    $startDate = $dateTime->format('w');
    $totalWeek = ceil(($lastDay + $startDate) / 7);

    echo "<h3>$year 년 $month 월</h3>";
    echo "<table border='1'>";
    echo "<tr>";
    echo "<th>일</th><th>월</th><th>화</th><th>수</th><th>목</th><th>금</th><th>토</th>";
    echo "</tr>";

    for ($i = 0; $i < $totalWeek; $i++) {
        echo "<tr>";
        for ($j = 0; $j <= 6; $j++) {
            if ($i == 0 && $j < $startDate) {
                echo "<td></td>";
            } elseif ($day <= $lastDay) {
                //파라미터로 YYYY-MM-DD 형식으로 넘겨야해서 포맷해주기
                $currentDate = sprintf("%04d-%02d-%02d", $year, $month, $day);

                $labels = getLabel($planArr, $currentDate); //날짜에 해당하는 라벨 가져오기
                echo "<td" . getStyle($planArr, $currentDate) . ">$day<br>$labels</td>"; //스타일 적용
                $day++;
            } else {
                echo "<td></td>";
            }
        }
        echo "</tr>";
    }
    echo "</table>";
}

//한글 요일을 숫자로 변환
function changeTextDateToNumberDate($textDate)
{
    switch ($textDate) {
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
        default:
            return null;
    }
}
