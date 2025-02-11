<?php

// 일정 배열
$planArr = [
    ['start' => '2024-12-15', 'end' => '2025-06-15', 'label' => '일정1', 'repeatType' => 'week', 'repeatData' => '수'],
    ['start' => '2025-02-03', 'end' => '2025-02-20', 'label' => '일정2', 'repeatType' => 'week', 'repeatData' => '목'],
    ['start' => '2023-09-15', 'end' => '2023-09-20', 'label' => '일정3', 'repeatType' => 'week', 'repeatData' => '금'],
    ['start' => '2023-09-15', 'end' => '2023-09-20', 'label' => '일정 화', 'repeatType' => 'week', 'repeatData' => '화'],
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

                        $currentDate = date('w', strtotime("$year-$month-$day")); //요일
                        $currentDay = "$year-$month-$day"; //지금 날짜

                        //일정이 중복되는지 확인
                        $duplicationCount = 0; //1이상이면 중복, 1이면 한번

                        //중복에 맞게 배경색 다르게 하기
                        $bgColor = "";

                        //해당하는 라벨 다 모으기
                        $labels = [];

                        //반복 요일인지 확인하고 굵게 처리
                        $isBold = "";

                        foreach ($planArr as $plan) {
                            //기간에 해당하는지
                            if (strtotime($currentDay) >= strtotime($plan['start']) && strtotime($currentDay) <= strtotime($plan['end'])) {
                                $duplicationCount++;
                                $labels[] = $plan['label'];

                                //중복 요일확인하기
                                //지금 들어온 요일이 plan의 요일에 해당되는게 있다면 굵게
                                if ($plan['repeatType'] == 'week') {
                                    //요일 숫자로바꾸기 
                                    $convertNumTheDate = array_search($plan['repeatData'], ['일', '월', '화', '수', '목', '금', '토']);

                                    //중복되는게 있다면 굵게 바꾸기
                                    if ($convertNumTheDate == $currentDate) {
                                        $isBold = 'font-weight: bold;';
                                    }
                                }
                            }
                        }

                        //스타일 입히기
                        if ($duplicationCount > 1) { //중복
                            $bgColor = 'background-color: yellow;';
                        }
                        if ($duplicationCount == 1) { //한번
                            $bgColor = 'background-color: red;';
                        }

                        //스타일 적용
                        $style = 'style = "' . $bgColor . ' ' . $isBold . '"';

                        //라벨 출력
                        $label = implode("<br>", $labels);

                        echo "<td " . $style . ">$day<br>$label</td>";

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
