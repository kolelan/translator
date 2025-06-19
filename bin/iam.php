<?php
define('YANDEX_OAUTH_TOKEN','y0...');


function getIamTokenFromYandexOAuth($oauthToken)
{
    $url = 'https://iam.api.cloud.yandex.net/iam/v1/tokens';

    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $oauthToken
    ];

    $data = [
        'yandexPassportOauthToken' => $oauthToken
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Отключение проверки SSL (только для тестов!)

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception('Curl error: ' . curl_error($ch));
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($httpCode !== 200) {
        throw new Exception('API request failed with HTTP code: ' . $httpCode . '. Response: ' . $response);
    }

    curl_close($ch);

    $responseData = json_decode($response, true);

    if (!isset($responseData['iamToken'])) {
        throw new Exception('Failed to get IAM token from response');
    }

    return $responseData['iamToken'];
}


try {
    $iamToken = getIamTokenFromYandexOAuth(YANDEX_OAUTH_TOKEN);
    echo 'IAM Token: ' . $iamToken;
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
