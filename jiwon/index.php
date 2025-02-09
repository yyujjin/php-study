<?php

//일정 배열
$dates = [
    ['start' => '2024-12-15',  'end' => '2025-01-05',  'label' => '일정1' ,  'repeat_type' => 'week',  'repeat_data' => '수'],
    ['start' => '2025-02-3',  'end' => '2025-02-29',  'label' => '일정2' ,  'repeat_type' => 'week',  'repeat_data' => '목'],
    ['start' => '2025-03-15',  'end' => '2025-04-05',  'label' => '일정3' ,  'repeat_type' => 'week',  'repeat_data' => '금'],
    ['start' => '2023-09-10',  'end' => '2023-09-20',  'label' => '일정4' ,  'repeat_type' => 'week',  'repeat_data' => '월'],
];


//이건 뭔지 일단 모르겠음 
$chkArr = []; // 달력 중복출력 방지용
$chkArr2 = []; // 해당 기간날짜 확인용



//일정시작 값만 추출
$startArr = array_column($dates, 'start');
//일정 끝의 값만 추출
$endArr = array_column($dates, 'end');

//일정시작 일의 오름차순을 기준으로 뒤에일정도 정렬
array_multisort($startArr,$endArr);


//일정 배열만큼 돌리기
for($iz=0; $iz<count($dates); $iz++){

    //시작
    //시작 : 2023-09-10  , 끝 : 2023-09-20 이렇게 첫번째 배열이 들어온다면
    //여기서 년, 달을 빼내고
    list($startYear, $startMonth) = explode('-', $startArr[$iz]);
    list($endYear, $endMonth) = explode('-', $endArr[$iz]);

    //2023-09  <=    2023-09  일정 시작달부터 일정끝달까지 달력만들기
    //여기서 YYYY-MM으로 붙여서 해당되는 달력만 만들기 
    while(sprintf('%04d-%02d', $startYear, $startMonth) <= sprintf('%04d-%02d', $endYear, $endMonth)){
        $date = "$startYear-$startMonth-01"; //2023-09-01 형식으로 만들어서 
        $time = strtotime($date);//❓❓ 이게 굳이 필요한가.그냥 밑에 $time대신 date로 쓰면 되는거아닌가
        $start_week = date('w',$time); //시작요일
        $total_days = date('t',$time); //마지막 일
        $total_weeks = ceil(($start_week + $total_days) / 7); //총 주 수 

        //2023-09 이 형식으로 시작 년,달만 문자포맷만들고
        $startYearMonth = sprintf('%04d-%02d', $startYear, $startMonth);

        //1. 2023-09이 깂이 $chkArr에 있는지 검사했을 때 없다면 넣고=>🧐🧐 왜 하는거지    2. 달력만들기 시작 
        if(!in_array($startYearMonth, $chkArr)){
            $chkArr[] = $startYearMonth;
?>
            <span><?=$startYear?>년 <?=$startMonth?>월</span>
            <table border="1">
                <tr>
                    <th>일</th>
                    <th>월</th>
                    <th>화</th>
                    <th>수</th>
                    <th>목</th>
                    <th>금</th>
                    <th>토</th>
                </tr>
<?php
            //$n => day
            //주 수 만큼 돌리기
            for($n=1, $i=0; $i<$total_weeks; $i++){
                echo "<tr>";
                //한주에 7칸 만들기
                for($k=0; $k<7; $k++){
                    echo "<td>";

                    //시작 요일 전까지 빈칸 만들기
                    if($i==0 && $k<$start_week){
                        echo "";
                        //1일부터 마지막날 의 숫자를 출력하기 위한 day개념인거같은데 
                        //내가 day를=1로 정의하고 day++ 한것처럼

                        //1-> 마지막일까지 칸만들기
                    } else if($n>=1 && $n<=$total_days){
                        //현재 달력의 날짜를 구하기
                        //2023-09-01 부터 2023-09-31까지 day를 변수 n으로 잡고 1씩 키워가면서 현재 날짜를 구하고 있음
                        $currentDate = sprintf('%04d-%02d-%02d', $startYear, $startMonth, $n);

                        //일정 기간에 해당되는지
                        $isPeriod = false;
                        $isSame = false;
                        $isBold = false;
                        $labels = [];
                        $bold_days = [];


                        //일정 배열에 있는 일정들을 하나씩 빼내옴
                        //❓❓근데 맨 위에서 dates배열 만큼 for문을 돌리고 있으니 그냥 dates[iz]으로 빼내오면 안되나?
                        foreach($dates as $date){
                            //현재날짜가 일정에 포함되는지 확인
                            if($currentDate >= $date['start'] && $currentDate <= $date['end']){
                                //일정기간에 해당되니 ture로 바꾸고
                                $isPeriod = true;
                                //해당되는 일정을 가져와서
                                $label = $date['label'];
                                //반복되게 설정해놓은 요일추출
                                $repeatType = $date['repeat_type'];
                                $repeatData = $date['repeat_data'];

                                //만약 반복타입이 요일(week)이라면
                                //해당 요일을 빼고 그 날은 볼드체로 설정
                                if($repeatType == 'week'){
                                    switch($repeatData){
                                        case '일':
                                        $bold_day = 0;
                                        break;
                                        case '월':
                                        $bold_day = 1;
                                        break;
                                        case '화':
                                        $bold_day = 2;
                                        break;
                                        case '수':
                                        $bold_day = 3;
                                        break;
                                        case '목':
                                        $bold_day = 4;
                                        break;
                                        case '금':
                                        $bold_day = 5;
                                        break;
                                        case '토':
                                        $bold_day = 6;
                                        break;
                                    }
                                }


                                
                                //빼내온 요일을 볼드체로 해야할 요일을 담는 배열에 넣기 (없으면)
                                if(!in_array($bold_day, $bold_days)){
                                    $bold_days[] = $bold_day;
                                }

                                //빼내온 라벨이 라벨을 담아놓는 배열에 없다면 추가
                                if(!in_array($label, $labels)){
                                    $labels[] = $label;
                                }
                                //다른 일정과 중복되는 일정인지 확인하는 코드
                                //현재 날짜가 일정에 해당되면 일정을 담는 배열 ($chkArr2)에 있다면 중복되는거니 same을 true로 바꿈
                                if(in_array($currentDate, $chkArr2)){
                                    $isSame = true;
                                } else {
                                    //그렇지 않으면 넣기
                                    $chkArr2[] = $currentDate;
                                }
                            }
                        }

                        //지금 날짜가 일정에도 포함되고 중복이 된다면 => 배경색 노랑색으로 바꾸기 기능
                        if($isPeriod && $isSame){

                            //k-> 요일이 bold배열에 해당된다면 true로 바꾸기
                            foreach($bold_days as $bold_day){
                                if($k==$bold_day){
                                    $isBold = true;
                                    echo "<span style='background-color:yellow; font-weight:700;'>$n</span>";
                                }
                            }

                            if(!$isBold){
                                echo "<span style='background-color:yellow;'>$n</span>";
                            }

                        } else if($isPeriod) {//지금 날짜가 일정에만 포함된다면 => 배경 빨간색으로 바꾸기 기능
                            //k-> 요일이 bold배열에 해당된다면 true로 바꾸기
                            foreach($bold_days as $bold_day){
                                if($k==$bold_day){
                                    $isBold = true;
                                    echo "<span style='background-color:red; font-weight:700;'>$n</span>";
                                }
                            }

                            if(!$isBold){
                                echo "<span style='background-color:red;'>$n</span>";
                            }

                        } else {//이외에는 일정 기간에 해당되지도 않고 중복되지도 않으니 일반적으로 출력
                            echo "<span>$n</span>";
                        }
                        //라벨 출력
                        foreach($labels as $label){
                            echo "<br><span>$label</span><br>";
                        }
                        //day 1씩 증가
                        $n++;
                    }
                    echo "</td>";
                }
                echo "</tr>";
            }
?>

            </table>

<?php
        } //if끝

        if($startMonth==12){
            $startYear++;
            $startMonth = 1;
        } else {
            $startMonth++;
        }
    }//while 끝
}//for문 끝
?>

