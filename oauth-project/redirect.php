<?php
include __DIR__ . '/../config/config.php';
include __DIR__ . '/../database/oauthDB.php';

if (isset($_GET['code'])) {
   
    $authCode = $_GET['code'];
    $tokenRequestUrl;
    $userInfoRequestUrl;
    $data;
    $provider;

    //네이버
    if(isset($_GET['state'])=='naver'){
        $tokenRequestUrl = "https://nid.naver.com/oauth2.0/token";
        $userInfoRequestUrl = "https://openapi.naver.com/v1/nid/me";
        $provider="naverId";
        $data = [
            'grant_type' => 'authorization_code',
            'client_id' => NAVER_CLIENT_ID,
            'client_secret' =>  NAVER_CLIENT_SECRET,
            'code' => $authCode,
            'state' => $state
        ];
    }else{//카카오
        $tokenRequestUrl = "https://kauth.kakao.com/oauth/token";
        $userInfoRequestUrl = "https://kapi.kakao.com/v2/user/me";
        $provider="kakaoId";
        $data = [
            'grant_type' => 'authorization_code',
            'client_id' => KAKAO_CLIENT_ID,
            'redirect_uri' => KAKAO_REDIRECT_URL,
            'code' => $authCode,
        ];
    }

    // cURL을 사용하여 Access Token 요청
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tokenRequestUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // JSON 응답 데이터 처리
    $tokenData = json_decode($response, true);

    if (isset($tokenData['access_token'])) {
        $accessToken = $tokenData['access_token'];
        // 사용자 정보 요청 로직으로 이동
        getUserInfo($accessToken,$userInfoRequestUrl,$provider);
    } else {
        echo "Access Token 요청 실패: ";
    }
  
} else {
    echo "Authorization Code가 전달되지 않았습니다.";
}

// 사용자 정보 가져오기
function getUserInfo($accessToken,$userInfoRequestUrl,$provider)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $userInfoRequestUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $accessToken,
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    $userInfo = json_decode($response, true);

    //네이버 고유 아이디
    if($provider=='naverId'){
        $providerId = $userInfo['response']['id'];
    }
    //카카오 고유 아이디
    if($provider=='kakaoId'){
        $providerId = $userInfo['id'];
    }

    $result = findUserByProviderId($provider,$providerId);
  

    if ($row = $result->fetch_assoc()) { //있으면 t/  없으면 f
        $userName = $row['userName'];
        echo '
        <form id="userData" method="POST" action="/auth-project/success.php">
            <input type="hidden" name="userName" value="' . htmlspecialchars($userName) . '">
        </form>
        <script>
            alert("로그인 성공! 메인 페이지로 이동합니다.");
            document.getElementById("userData").submit();
        </script>
        ';
    } else {

        if($provider=='naverId'){
            echo '
            <form id="userData" method="POST" action="/oauth-project/accountLinking.php">
                <input type="hidden" name="naverId" value="' . htmlspecialchars($providerId) . '">
            </form>';
        }
        if($provider=='kakaoId'){
            echo '
            <form id="userData" method="POST" action="/oauth-project/accountLinking.php">
                <input type="hidden" name="kakaoId" value="' . htmlspecialchars($providerId) . '">
            </form>';
        }

        echo '
        <script>
            alert("기존 계정과 연동하세요!");
            document.getElementById("userData").submit();
        </script>
        ';
    }

    
}
