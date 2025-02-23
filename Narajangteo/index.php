<?php

//html파일가져오기
$htmlDom = loadHtmlAsDom("index.html");

//테이블 데이터 가져오기
$tableData = getTableContents($htmlDom);

//table 태그 데이터 가져와서 가공하기
$formattedData = formatData($tableData);

//페이징
$paginatedData = paginate($formattedData);

//검색어
$searchedData = searchByKeyword($paginatedData);

//사업일자, 공고일자 모드에 맞게 선택된 날짜 데이터 가져오기
$filteredDateData = filterDataByDateMode($searchedData);


//결과 출력 TEST
echo "<pre>";
print_r($filteredDateData);
echo "</pre>";


//html 가져와서 dom으로 만드는 함수
function loadHtmlAsDom($htmlFile){
    $filename = $htmlFile; // 읽어올 파일명

    // 파일이 존재하는지 확인
    if (!file_exists($filename)) {
        die("오류: 파일이 존재하지 않습니다. 파일 경로를 확인하세요.");
    }
    
    // 파일을 읽기 시도
    $html = file_get_contents($filename);
    
    // 파일 읽기 실패 시 원인 출력
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

//table 태그의 데이터 가져오는 함수
function getTableContents($dom){
    // XPath를 사용해 특정 ID 가진 <table> 찾기
    $xpath = new DOMXPath($dom);
    $table = $xpath->query("//table[@id='mf_wfm_container_testTable']")->item(0);

    if (!$table) {
        die("오류: ID가 'mf_wfm_container_testTable'인 테이블을 찾을 수 없습니다.");
    }

    // 테이블 데이터 저장 배열
    $data = [];

    $rows = $table->getElementsByTagName('tr'); // ✅ 모든 <tr> 가져오기
    foreach ($rows as $row) {
        if (!($row instanceof DOMElement)) {
            continue; // <tr>가 아니면 무시
        }

        $rowData = [];
        $cells = $row->getElementsByTagName('td'); // ✅ <td> 가져오기

        foreach ($cells as $cell) {
            $rowData[] = trim($cell->nodeValue); // 텍스트만 가져와서 배열에 저장
        }

        if (!empty($rowData)) {
            $data[] = $rowData;
        }
    }
    return $data;
}

// 배열 가공 함수
function formatData($data) {
    $newKeys = ["No", "단계구분", "업무구분", "사업명", "","사업번호","사업일자","공고/계약 기관","수요기관","공고일자","계약구분","계약방법","계약금액","참조번호","투찰"];
    $formattedData = [];

    foreach ($data as $index => $row) {
        //짝수 인덱스만 추출해서 불필요 배열 제거
        if($index%2==0){
            // 배열 크기가 다를 경우 대비하여 `array_slice()` 사용
            $formattedData[] = array_combine(array_slice($newKeys, 0, count($row)), $row);
        }
    }

    return $formattedData;
}

//페이징 함수 
function paginate($array){

    //한페이지에 5개 배열씩 출력
    $limit = 5;

    $totalDatas = count($array);
    $totalPages = ceil($totalDatas / $limit);

    //현재 페이지 (GET 파라미터에서 page 값 가져오기, 기본값: 1)
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1 || $page > $totalPages) $page = 1; // 잘못된 페이지 방지

    //이지 범위 설정 (배열 자르기)
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



//사업일자, 공고일자 모드에 맞게 날짜 필터해서 데이터 가져오는 함수  
function filterDataByDateMode($data){

    $mode = isset($_GET['mode']) ? trim($_GET['mode']) : "사업일자"; //기본 사업일자

    // 날짜 범위 가져오기
    list($startDate, $endDate) = explode("-", getDateRange());

    echo "필터링 기준: 모드 : {$mode}, 날짜 범위: {$startDate} - {$endDate} <br>";

    // 필터링 로직
    $filteredData = array_filter($data, function ($data) use ($startDate, $endDate, $mode) {

        if($mode =="사업일자"){
            $formattedDate = str_replace("/", "", $data["사업일자"]);
            return ($formattedDate >= $startDate && $formattedDate <= $endDate);
        }
        if($mode =="공고일자"){
            $formattedDate = str_replace("/", "", $data["공고일자"]);
            return ($formattedDate >= $startDate && $formattedDate <= $endDate);
        }
    });

    return $filteredData;

}


//날짜 범위 가져오기
function getDateRange() {
    // GET 요청에서 값 가져오기
    $monthsAgo = isset($_GET['monthsAgo']) ? (int) trim($_GET['monthsAgo']) : 1; // 기본값: 1개월
    $endDate = isset($_GET['endDate']) ? trim($_GET['endDate']) : date('Ymd'); // 기본값: 오늘 날짜
    $startDate = isset($_GET['startDate']) ? trim($_GET['startDate']) : null; // startDate는 기본적으로 null

    // `monthsAgo` 모드가 활성화된 경우 (startDate, endDate를 직접 설정할 수 없음)
    if (isset($_GET['monthsAgo'])) {
        if (!in_array($monthsAgo, [1, 3, 6])) {
            return "ERROR: monthsAgo는 1, 3, 6만 가능합니다.";
        }
        $startDate = date('Ymd', strtotime("-{$monthsAgo} months", strtotime($endDate)));
    }

    // `monthsAgo` 모드가 아닐 때 startDate 설정
    if ($startDate === null) {
        $startDate = date('Ymd', strtotime("-1 months", strtotime($endDate))); // 기본: 1개월 전
    }

    // 날짜 검증 (startDate는 endDate 이후가 될 수 없음)
    if ($startDate > $endDate) {
        return "ERROR: startDate는 endDate 이후가 될 수 없습니다.";
    }

    // endDate 검증 (startDate보다 이전이 될 수 없음)
    if ($endDate < $startDate) {
        return "ERROR: endDate는 startDate보다 이전이 될 수 없습니다.";
    }

    return "$startDate-$endDate";
}


?>