<?php

$mode = isset($_GET['mode'])  && $_GET['mode'] !="" ? trim($_GET['mode']) : "사업일자"; //기본 사업일자
$monthsAgo = isset($_GET['monthsAgo']) && $_GET['monthsAgo'] !="" ? (int) trim($_GET['monthsAgo']) : null; // 기본값 null
$endDate =isset($_GET['endDate']) && $_GET['endDate'] !=""  ? trim($_GET['endDate']) : date('Ymd'); // 기본값: 오늘 날짜
$startDate = isset($_GET['startDate']) && $_GET['startDate'] !=""  ? trim($_GET['startDate']) : null; // startDate는 기본적으로 null
$tableData = getTableContents( loadHtmlAsDom("nara.html")); //테이블 데이터
$filteredDateData = filterDataByDateMode($tableData);

// //검색어
// $searchedData = searchByKeyword($paginatedData);

//페이징
// $paginatedData = paginate($formattedData);





//결과 출력 TEST
echo "<pre>";
//print_r($filteredDateData);
echo "</pre>";

//날짜필터 
function filterDataByDateMode($data){

    global $mode;

    // 날짜 범위 가져오기
    list($startDate, $endDate) = explode("-", getDateRange());

    echo "필터링 모드 : {$mode} <br>날짜 범위: {$startDate} - {$endDate} <br>";

    // 필터링 로직
    $filteredData = array_filter($data, function ($data) use ($startDate, $endDate, $mode) {

        if($mode =="사업일자"){
            $formattedDate = str_replace("/", "", $data[6]);
           // echo "배열날짜는 이거나옴 $data[6]<br>";
           
            $tt = $formattedDate >= $startDate && $formattedDate <= $endDate;
            //echo "여기서 리턴되는건 $tt";
            return ($formattedDate >= $startDate && $formattedDate <= $endDate);
        }

        if($mode =="공고일자"){
            $formattedDate = str_replace("/", "", $data[9]);
            return ($formattedDate >= $startDate && $formattedDate <= $endDate);
        }
    });

    return $filteredData;

}



//페이징 함수 
function paginate($array){

    //한페이지에 10개씩 출력
    $limit = 10;

    $totalDatas = count($array);
    $totalPages = ceil($totalDatas / $limit);

    //현재 페이지 (GET 파라미터에서 page 값 가져오기, 기본값: 1)
    $page = isset($_GET['page']) && $_GET['page'] !=""  ? (int)$_GET['page'] : 1;
    echo "페이지 : $page <br>";

    if ($page < 1) $page = 1; // 잘못된 페이지 방지

    //페이지 범위 설정 (배열 자르기)
    $offset = ($page - 1) * $limit;
    $paginatedArray = array_slice($array, $offset, $limit); //배열, 시작인덱스, 개수

    return $paginatedArray;
}

//검색어 기능 함수
function searchByKeyword($data){

    $keyword = isset($_GET['search']) ? trim($_GET['search']) : '';
    echo "키워드  : $keyword <br>";

    $searchData = [];

    // 검색어가 있을 경우 필터링 실행
    if ($keyword !== '') {
        $searchData = array_filter($data, function ($row) use ($keyword) {
            //사업명에 키워드가 포함되어있다면 추출
            return stripos($row["사업명"], $keyword) !== false;
        });
    } else {
        $searchData = $data; // 검색어가 없으면 전체 데이터 유지
    }
    return $searchData;
}





//날짜 범위 가져오기
function getDateRange() {

    global $monthsAgo, $endDate, $startDate;

    // `monthsAgo` 모드가 활성화된 경우 (startDate, endDate를 직접 설정할 수 없음)
    if ($monthsAgo !== null) {
        if (!in_array($monthsAgo, [1, 3, 6])) {
            return "monthsAgo는 1, 3, 6만 가능합니다.";
        }
        $startDate = date('Ymd', strtotime("-{$monthsAgo} months"));
        $endDate = date('Ymd');
    }

    // `monthsAgo` 모드가 아닐 때 startDate 설정
    if ($startDate === null) {
        $startDate = date('Ymd', strtotime("-1 months", strtotime($endDate))); // 기본: 1개월 전
    }

    // 날짜 검증 (startDate는 endDate 이후가 될 수 없음)
    if ($startDate > $endDate) {
        return "startDate는 endDate 이후가 될 수 없습니다.";
    }

    // endDate 검증 (startDate보다 이전이 될 수 없음)
    if ($endDate < $startDate) {
        return "endDate는 startDate보다 이전이 될 수 없습니다.";
    }

    return "$startDate-$endDate";
}



//html 가져와서 dom으로 만드는 함수
function loadHtmlAsDom($htmlFile){
    $filename = $htmlFile; // 읽어올 파일명
    if (!file_exists($filename)) {
        die("오류: 파일이 존재하지 않습니다. 파일 경로를 확인하세요.");
    }
    
    $html = file_get_contents($filename);
    if ($html === false) {
        $error = error_get_last(); // 마지막 오류 가져오기
        die("오류: 파일을 읽을 수 없습니다. 상세 오류: " . $error['message']);
    }
    
    $dom = new DOMDocument;
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();

    return $dom;
    
}

//table 태그의 데이터 가져오기
function getTableContents($dom){

    $xpath = new DOMXPath($dom);
    $table = $xpath->query("//table[@id='mf_wfm_container_testTable']")->item(0);
    if (!$table) {
        die("오류: ID가 'mf_wfm_container_testTable'인 테이블을 찾을 수 없습니다.");
    }

    $data = [];

    $rows = $table->getElementsByTagName('tr');
    foreach ($rows as $row) {
        if (!($row instanceof DOMElement)) {
            continue;
        }
        $rowData = [];
        $cells = $row->getElementsByTagName('td');

        foreach ($cells as $cell) {
            $rowData[] = trim($cell->nodeValue); 
        }
        if (!empty($rowData)) {
            $data[] = $rowData;
        }
    }

    $tableData = [];
    foreach ($data as $index => $row) {
        if($index%2==0){
            // 배열 크기가 다를 경우 대비하여 `array_slice()` 사용
            $tableData[] = $row;
        }
    }

    return $tableData;
}

?>