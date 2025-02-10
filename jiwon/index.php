<?php

// 일정 배열
$dates = [
    ['start' => '2024-12-15', 'end' => '2025-09-20', 'label' => '일정1', 'repeatType' => 'week', 'repeatData' => '수'],
    ['start' => '2025-02-03', 'end' => '2025-02-29', 'label' => '일정2', 'repeatType' => 'week', 'repeatData' => '목'],
    ['start' => '2025-03-15', 'end' => '2025-04-05', 'label' => '일정3', 'repeatType' => 'week', 'repeatData' => '금'],
    ['start' => '2023-09-10', 'end' => '2023-09-20', 'label' => '일정4', 'repeatType' => 'week', 'repeatData' => '월'],
];

createCalendarsFromDates($dates);
//스타일 적용 함수
function getStyle($dates, $yearMonthDay)
{
    $repeatDays = []; // 반복 요일 저장
    $isBold = ""; // 볼드 스타일
    $bgColor = ""; // 배경색
    $isDuplicate = 0; // 중복 일정 카운트

    $day = new DateTime($yearMonthDay);
    $inputDate = $day->format('w'); // 현재 날짜의 요일을 숫자로 변환 (0: 일요일 ~ 6: 토요일)

    foreach ($dates as $date) {
        $startPlanDay = new DateTime($date['start']);
        $endPlanDay = new DateTime($date['end']);

        // 일정이 해당 날짜에 포함되는지 확인
        if ($day >= $startPlanDay && $day <= $endPlanDay) {
            // 반복 요일이 있으면 변환 후 저장
            if (!empty($date['repeatType'])) {
                $convertedDay = changeTextDateToNumberDate($date['repeatdData']);
                if ($convertedDay !== null) {
                    $repeatDays[] = $convertedDay;
                }
            }
            $isDuplicate++;
        }
    }

    //볼드체 적용 (현재 날짜의 요일이 반복 일정과 일치하는지 확인)
    if (in_array($inputDate, $repeatDays)) {
        $isBold = "font-weight: bold;";
    }

    //중복 일정 확인하여 배경색 결정
    if ($isDuplicate > 1) {
        $bgColor = "background-color: yellow;";
    } elseif ($isDuplicate == 1) {
        $bgColor = "background-color: red;";
    }

    return ' style="' . $bgColor . $isBold . '"';
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
            return null; // 예외 처리
    }
}

//해당 날짜에 포함되는 일정 라벨 가져오는 함수
function getLabelsForDate($dates, $yearMonthDay)
{
    $labels = [];

    foreach ($dates as $date) {
        $startPlanDay = new DateTime($date['start']);
        $endPlanDay = new DateTime($date['end']);
        $day = new DateTime($yearMonthDay);

        // 일정이 해당 날짜에 포함되는지 확인
        if ($day >= $startPlanDay && $day <= $endPlanDay) {
            $labels[] = $date['label'];
        }
    }

    return implode("<br>", $labels); // ✅ 일정이 여러 개일 경우 줄바꿈 추가
}

//날짜 범위에 따라 달력을 생성하는 함수
function createCalendarsFromDates($dates)
{
    $startArr = array_column($dates, 'start');
    $endArr = array_column($dates, 'end');

    array_multisort($startArr, $endArr); // 시작 날짜 기준으로 정렬

    // 중복 날짜 체크 배열
    $createdPeriod = [];

    for ($i = 0; $i < count($dates); $i++) {
        $startPlanDay = new DateTime($startArr[$i]);
        $endPlanDay = new DateTime($endArr[$i]);

        //날짜 비교할 때 `Y-m`으로 변환하여 비교
        while ($startPlanDay->format('Y-m') <= $endPlanDay->format('Y-m')) {
            if (!in_array($startPlanDay->format('Y-m'), $createdPeriod)) {
                $createdPeriod[] = $startPlanDay->format('Y-m');
                createCalendar($dates, $startPlanDay->format('Y'), $startPlanDay->format('n'));
            }
            $startPlanDay->modify('+1 month');
        }
    }
}

//달력 생성 함수
function createCalendar($dates, $year, $month)
{
    $day = 1;
    $lastDay = date('t', strtotime("$year-$month-01"));
    $startDate = date('w', strtotime("$year-$month-01"));
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
                $currentDate = sprintf("%04d-%02d-%02d", $year, $month, $day);
                $labels = getLabelsForDate($dates, $currentDate); //날짜에 해당하는 라벨 가져오기
                echo "<td" . getStyle($dates, $currentDate) . ">$day<br>$labels</td>";
                $day++;
            } else {
                echo "<td></td>";
            }
        }
        echo "</tr>";
    }

    echo "</table>";
}
