<?php
$filename = "index.html"; // 읽어올 파일명

// 파일이 존재하는지 확인
if (!file_exists($filename)) {
    die("❌ 오류: 파일이 존재하지 않습니다. 파일 경로를 확인하세요.");
}

// 파일을 읽기 시도
$html = file_get_contents($filename);

// 파일 읽기 실패 시 원인 출력
if ($html === false) {
    $error = error_get_last(); // 마지막 오류 가져오기
    die("❌ 오류: 파일을 읽을 수 없습니다. 상세 오류: " . $error['message']);
}

// 파일이 정상적으로 읽혔는지 확인
echo "✅ HTML 파일이 정상적으로 불러와졌습니다!";

$dom = new DOMDocument;
libxml_use_internal_errors(true);
$dom->loadHTML($html);
libxml_clear_errors();

$tables = $dom->getElementsByTagName('table');
echo "테이블 개수: " . $tables->length . "<br>";

if ($tables->length > 0) {
    echo "✅ 첫 번째 테이블을 찾았습니다!<br>";
} else {
    echo "❌ 테이블을 찾을 수 없습니다.";
}

// XPath를 사용해 특정 ID 가진 <table> 찾기
$xpath = new DOMXPath($dom);
$table = $xpath->query("//table[@id='mf_wfm_container_testTable']")->item(0);

if (!$table) {
    die("❌ 오류: ID가 'mf_wfm_container_testTable'인 테이블을 찾을 수 없습니다.");
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



//배열가공
$tableData = formatData($data);


// 배열 가공 함수
function formatData($data) {
    $newKeys = ["No", "단계구분", "업무구분", "사업명", "","사업번호","사업일자","공고/계약 기관","수요기관","공고일자","계약구분","계약방법","계약금액","참조번호","투찰"];
    $formattedData = [];

    foreach ($data as $row) {
        // 배열 크기가 다를 경우 대비하여 `array_slice()` 사용
        $formattedData[] = array_combine(array_slice($newKeys, 0, count($row)), $row);
    }

    return $formattedData;
}


//결과 출력
echo "<pre>";
print_r($tableData);
echo "</pre>";

?>
