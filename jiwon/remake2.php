<?php

// 일정 배열
$planArr = [
    ['start' => '2024-12-15', 'end' => '2025-06-15', 'label' => '일정1', 'repeatType' => 'week', 'repeatData' => '수'],
    ['start' => '2025-02-03', 'end' => '2025-02-20', 'label' => '일정2', 'repeatType' => 'week', 'repeatData' => '목'],
    ['start' => '2023-09-15', 'end' => '2023-09-20', 'label' => '일정3', 'repeatType' => 'week', 'repeatData' => '금'],
    ['start' => '2023-09-10', 'end' => '2023-09-20', 'label' => '일정4', 'repeatType' => 'week', 'repeatData' => '월'],
];

//시작일 기준으로 오름차순 정렬
usort($planArr, function ($a, $b) {
    return strcmp($a['start'], $b['start']);
});

//달력 중복 생성 방지 배열
$madeCalendar = [];

//플랜배열 반복문 
foreach ($planArr as $plan) {

    //시작날자 끝날짜 빼서 시간 객체로 만들고
    $startDay = new DateTime($plan['start']);
    $endDay = new DateTime($plan['end']);

    //기간만큼 돌리기
    while ($startDay->format('Y-m') <= $endDay->format('Y-m')) {

        if (!in_array($startDay->format('Y-m'), $madeCalendar)) {

            $madeCalendar[] = $startDay->format('Y-m');

            //년, 달 빼기
            $year = $startDay->format('Y');
            $month = $startDay->format('n'); //일단 n으로 바꿈

            //달력만들기 준비
            $day = 1;
            $lastDay = $startDay->format('t');
            $startDate = $startDay->format('w');
            $totalWeek = ceil(($startDate + $lastDay) / 7);

            echo "<h3> $year 년 $month 월 </h3>";
            echo "<table border=1>";
            echo "<tr><th>일</th><th>월</th><th>화</th><th>수</th><th>목</th><th>금</th><th>토</th></tr>";

            for ($i = 0; $i < $totalWeek; $i++) {
                echo "<tr>";
                for ($j = 0; $j <= 6; $j++) {
                    if ($i == 0 && $j < $startDate) {
                        echo "<td></td>";
                    } elseif ($day <= $lastDay) {
                        echo "<td>$day</td>";
                        $day++;
                    } else {
                        echo "<td></td>";
                    }
                }
                echo "</tr>";
            }
            echo "</table>";
        }


        //마지막에 한달씩 추가하기
        $startDay->modify('+1 month');
    }
}
