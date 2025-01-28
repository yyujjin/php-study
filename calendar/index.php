<?php
$year = date('Y');
$month = date('m'); 
?>

<a>이전 달</a>
<span>2025년 1월</span>
<a>다음 달</a>
<a>오늘로 돌아가기</a>

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
function createDateCells($year,$month){

    $lastDay = date("t", strtotime("$year-$month-01"));
    $totalWeek = ceil($lastDay/7);
    $startMonth = date("w", strtotime("$year-$month-01"));
    $day=1;
    for($i=0; $i<$totalWeek; $i++){ 
        echo "<tr>";
        for($j=0; $j<7&&$day<=$lastDay; $j++){
            if($i===0&&$j<$startMonth){
                echo "<td> </td>";
            }else{
                echo "<td>".$day."</td>";
                $day++;
            }
        }
        echo "</tr>";
    }
}
?>

