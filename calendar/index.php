<?php
$year = $_GET['year']??date('Y');
$month = $_GET['month']??date('m');
?>

<a href="<?=prevButton($year,$month)?>">이전 달</a>
<span><?=$year?>년 <?=$month?>월</span>
<a href="<?=nextButton($year,$month)?>">다음 달</a>
<a href="<?=todayButton()?>">오늘로 돌아가기</a>

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
    <tr>
        <?=createDateCells($year,$month)?>
    </tr>
</table>

<?php

//이후
function nextButton($year,$month){
    if($month==12){
        return "?year=".($year+1)."&month=1";
    }
    return "?year=".$year."&month=".($month+1);
}

//이전
function prevButton($year,$month){
    if($month==1){
        return "?year=".($year-1)."&month=12";
    }
    return "?year=".$year."&month=".($month-1);
}

//오늘로 돌아가기
function todayButton(){
    return "?year=".date('Y')."&month=".date('m');
}

//토요일은 파란색 일요일은 빨간색
function getDayColor($j){
    if($j==0){
        echo "일욜 ";
        return 'style="color: red;"';
    }
    if($j==6){
        echo "토욜";
        return 'style="color: blue;"';
        
    }
}

//달력 칸 만드는 함수
function createDateCells($year,$month){
    $lastDay = date("t", strtotime("$year-$month-01"));
    $startMonth = date("w", strtotime("$year-$month-01"));
    $totalWeek = ceil(($lastDay+$startMonth)/7);
    $day=1;
    for($i=0; $i<$totalWeek; $i++){ 
        echo "<tr>";
        for($j=0; $j<7&&$day<=$lastDay; $j++){
            if($i===0&&$j<$startMonth){
                echo '<td></td>';
            }else{
                //echo '<td'.getDayColor($j).'>'.$day.'</td>';
                echo "<td ".getDayColor($j).">".$day."</td>";
                $day++;
            }
        }
        echo "</tr>";
    }
}

?>