<?php

$dates = [
    ['start' => '2024-12-15',  'end' => '2025-01-05',  'label' => '일정1' ,  'repeat_type' => 'week',  'repeat_data' => '수'],
    ['start' => '2025-01-31',  'end' => '2025-02-29',  'label' => '일정2' ,  'repeat_type' => 'week',  'repeat_data' => '목'],
    ['start' => '2023-09-15',  'end' => '2025-11-05',  'label' => '일정3' ,  'repeat_type' => 'week',  'repeat_data' => '금'],
    ['start' => '2023-09-10',  'end' => '2023-09-20',  'label' => '일정4' ,  'repeat_type' => 'week',  'repeat_data' => '월'],
];

$chkArr = []; // 달력 중복출력 방지용
$chkArr2 = []; // 해당 기간날짜 확인용

$startArr = array_column($dates, 'start');
$endArr = array_column($dates, 'end');

array_multisort($startArr,$endArr);

for($iz=0; $iz<count($dates); $iz++){

list($startYear, $startMonth) = explode('-', $startArr[$iz]);
list($endYear, $endMonth) = explode('-', $endArr[$iz]);

while(sprintf('%04d-%02d', $startYear, $startMonth) <= sprintf('%04d-%02d', $endYear, $endMonth)){
$date = "$startYear-$startMonth-01";
$time = strtotime($date);
$start_week = date('w',$time);
$total_days = date('t',$time);
$total_weeks = ceil(($start_week + $total_days) / 7);

$startYearMonth = sprintf('%04d-%02d', $startYear, $startMonth);

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
   for($n=1, $i=0; $i<$total_weeks; $i++){
      echo "<tr>";
      for($k=0; $k<7; $k++){
         echo "<td>";
         if($i==0 && $k<$start_week){
            echo "";
         } else if($n>=1 && $n<=$total_days){

            $currentDate = sprintf('%04d-%02d-%02d', $startYear, $startMonth, $n);

            $isPeriod = false;
            $isSame = false;
            $isBold = false;
            $labels = [];
            $bold_days = [];

            foreach($dates as $date){
               if($currentDate >= $date['start'] && $currentDate <= $date['end']){
                  $isPeriod = true;
                  $label = $date['label'];
                  $repeatType = $date['repeat_type'];
                  $repeatData = $date['repeat_data'];

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

                  if(!in_array($bold_day, $bold_days)){
                     $bold_days[] = $bold_day;
                  }

                  if(!in_array($label, $labels)){
                     $labels[] = $label;
                  }

                  if(in_array($currentDate, $chkArr2)){
                     $isSame = true;
                  } else {
                     $chkArr2[] = $currentDate;
                  }

               }
            }

            if($isPeriod && $isSame){
               foreach($bold_days as $bold_day){
                  if($k==$bold_day){
                     $isBold = true;
                     echo "<span style='background-color:yellow; font-weight:700;'>$n</span>";
                  }
               }

               if(!$isBold){
                  echo "<span style='background-color:yellow;'>$n</span>";
               }

            } else if($isPeriod) {
               foreach($bold_days as $bold_day){
                  if($k==$bold_day){
                     $isBold = true;
                     echo "<span style='background-color:red; font-weight:700;'>$n</span>";
                  }
               }

               if(!$isBold){
                  echo "<span style='background-color:red;'>$n</span>";
               }

            } else {
               echo "<span>$n</span>";
            }

            foreach($labels as $label){
               echo "<br><span>$label</span><br>";
            }

            $n++;
         }
         echo "</td>";
      }
      echo "</tr>";
   }
   ?>

</table>

<?php
   }

   if($startMonth==12){
      $startYear++;
      $startMonth = 1;
   } else {
      $startMonth++;
   }
}
}
?>

