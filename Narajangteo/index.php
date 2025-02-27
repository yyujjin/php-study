<?php

$mode = isset($_GET['mode']) && $_GET['mode'] !== "" ? trim($_GET['mode']) : "사업일자"; // 기본 사업일자
$monthsAgo = isset($_GET['monthsAgo']) && $_GET['monthsAgo'] !== "" ? (int) trim($_GET['monthsAgo']) : null; 
$endDate = isset($_GET['endDate']) && $_GET['endDate'] !== "" ? str_replace("-", "", trim($_GET['endDate'])) : date('Ymd'); 
$startDate = isset($_GET['startDate']) && $_GET['startDate'] !== "" ? str_replace("-", "", trim($_GET['startDate'])) : null; 
$page = isset($_GET['page']) && $_GET['page'] !== "" ? (int)$_GET['page'] : 1;

$tableData = getTableContents(loadHtmlAsDom("nara.html"));
$filteredData = filterDataByDateMode($tableData);
$searchedData = searchByKeyword($filteredData);
$paginatedData = paginate($searchedData);

// HTML 테이블 출력
makeTable($paginatedData);

function makeTable($data) {

    global $mode, $startDate, $endDate;

    echo '<form method="GET" style="margin-bottom: 15px; text-align: center;">';

    echo '<label><input type="radio" name="mode" value="사업일자" ' . ($mode === '사업일자' ? 'checked' : '') . '> 사업일자</label>';
    echo '<label><input type="radio" name="mode" value="공고일자" ' . ($mode === '공고일자' ? 'checked' : '') . '> 공고일자</label>';
    echo '<br><br>';

    echo '시작 날짜: <input type="date" name="startDate" value="' . $startDate . '">';
    echo '끝 날짜: <input type="date" name="endDate" value="' . $endDate . '">';
    echo '<br><br>';

    echo '<input type="text" name="search" placeholder="검색어 입력">';
    echo '<button type="submit">검색</button>';
    echo '</form>';

    echo "<table border=1>";
    echo "<tr><th>No</th><th>단계구분</th><th>업무구분</th><th>사업명</th><th>사업번호</th><th>사업일자</th><th>공고일자</th></tr>";

    foreach ($data as $row) {
        echo "<tr>";
        echo "<td>" . ($row[0] ?? '') . "</td>";
        echo "<td>" . ($row[1] ?? '') . "</td>";
        echo "<td>" . ($row[2] ?? '') . "</td>";
        echo "<td>" . ($row[3] ?? '') . "</td>";
        echo "<td>" . ($row[5] ?? '') . "</td>";
        echo "<td>" . ($row[6] ?? '') . "</td>";
        echo "<td>" . ($row[9] ?? '') . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    pageNavigation();
}

//페이징 함수
function paginate($array) {
    global $page, $totalPages;
    $limit = 3;
    $totalDatas = count($array);
    $totalPages = ceil($totalDatas / $limit);

    if ($page < 1) $page = 1;
    $offset = ($page - 1) * $limit;
    return array_slice($array, $offset, $limit);
}

//페이징 UI 함수
function pageNavigation() {
    global $page, $totalPages;

    if ($totalPages <= 1) return;

    echo "<div style='text-align:center; margin-top:15px;'>";
    if ($page > 1) {
        echo "<a href='?page=" . ($page - 1) . "'>이전</a> ";
    }

    for ($i = 1; $i <= $totalPages; $i++) {
        if ($i == $page) {
            echo "<strong>[$i]</strong> ";
        } else {
            echo "<a href='?page=$i'>$i</a> ";
        }
    }

    if ($page < $totalPages) {
        echo "<a href='?page=" . ($page + 1) . "'>다음</a> ";
    }
    echo "</div>";
}

//검색 기능
function searchByKeyword($data) {
    $keyword = isset($_GET['search']) ? trim($_GET['search']) : '';

    if ($keyword !== '') {
        return array_filter($data, function ($row) use ($keyword) {
            return stripos($row[3], $keyword) !== false; // 사업명(3번 인덱스)에서 검색
        });
    }
    return $data;
}

//날짜 필터링
function filterDataByDateMode($data) {
    global $mode;
    list($startDate, $endDate) = explode("-", getDateRange());

    return array_filter($data, function ($data) use ($startDate, $endDate, $mode) {
        $formattedDate = ($mode == "사업일자") ? str_replace("/", "", $data[6]) : str_replace("/", "", $data[9]);
        return ($formattedDate >= $startDate && $formattedDate <= $endDate);
    });
}

//날짜 범위 가져오기
function getDateRange() {
    global $monthsAgo, $endDate, $startDate;

    if ($monthsAgo !== null) {
        if (!in_array($monthsAgo, [1, 3, 6])) {
            return "monthsAgo는 1, 3, 6만 가능합니다.";
        }
        $startDate = date('Ymd', strtotime("-{$monthsAgo} months"));
        $endDate = date('Ymd');
    }

    if ($startDate === null) {
        $startDate = date('Ymd', strtotime("-1 months", strtotime($endDate)));
    }

    if ($startDate > $endDate) {
        return "startDate는 endDate 이후가 될 수 없습니다.";
    }
    if ($endDate < $startDate) {
        return "endDate는 startDate보다 이전이 될 수 없습니다.";
    }

    return "$startDate-$endDate";
}

//HTML을 DOM으로 변환
function loadHtmlAsDom($htmlFile) {
    if (!file_exists($htmlFile)) {
        die("오류: 파일이 존재하지 않습니다.");
    }
    $html = file_get_contents($htmlFile);
    if ($html === false) {
        die("오류: 파일을 읽을 수 없습니다.");
    }

    $dom = new DOMDocument;
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();

    return $dom;
}

//테이블 데이터 가져오기
function getTableContents($dom) {
    $xpath = new DOMXPath($dom);
    $table = $xpath->query("//table[@id='mf_wfm_container_testTable']")->item(0);
    if (!$table) {
        die("오류: 테이블을 찾을 수 없습니다.");
    }

    $data = [];
    $rows = $table->getElementsByTagName('tr');
    foreach ($rows as $row) {
        if (!($row instanceof DOMElement)) continue;
        $rowData = [];

        foreach ($row->getElementsByTagName('td') as $cell) {
            $rowData[] = trim($cell->nodeValue);
        }
        if (!empty($rowData)) {
            $data[] = $rowData;
        }
    }

    $tableData = [];
    foreach ($data as $index => $row) {
        if ($index % 2 == 0) {
            $tableData[] = $row;
        }
    }
    return $tableData;
}
?>
