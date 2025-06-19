<?php
header('Content-Type: text/html; charset=utf-8');

// Конфигурация
define('DB_HOST', '10...');
define('DB_PORT', '54..');
define('DB_NAME', 'tr...');
define('DB_USER', 'postgres');
define('DB_PASSWORD', '');
define('YANDEX_FOLDER_ID', 'b1...');
define('YANDEX_IAM_TOKEN', 't1...');

// Функция для подключения к PostgreSQL
function connectToDatabase() {
    $connectionString = "host=" . DB_HOST . " port=" . DB_PORT . " dbname=" . DB_NAME . " user=" . DB_USER . " password=" . DB_PASSWORD;
    $db = pg_connect($connectionString);

    if (!$db) {
        die("Ошибка подключения к базе данных: " . pg_last_error());
    }

    return $db;
}

// Функция для перевода текста через Яндекс.API
function translateText($text, $sourceLang, $targetLang) {
    $url = 'https://translate.api.cloud.yandex.net/translate/v2/translate';

    $data = [
        'sourceLanguageCode' => $sourceLang,
        'targetLanguageCode' => $targetLang,
        'texts' => [$text],
        'folderId' => YANDEX_FOLDER_ID, // Todo описать как получить folderId
    ];

    $headers = [
        // 'Content-Type: application/json',
        'Authorization: Bearer ' . YANDEX_IAM_TOKEN
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        die('Ошибка cURL: ' . curl_error($ch));
    }

    curl_close($ch);

    $result = json_decode($response, true);

    if (isset($result['translations'][0]['text'])) {
        return $result['translations'][0]['text'];
    } else {
        error_log("Ошибка перевода: " . print_r($result, true));
        return false;
    }
}

// Основная логика приложения
try {
    // Подключаемся к базе данных
    $db = connectToDatabase();

    // Получаем фразы, которые нужно перевести (где translated_text IS NULL)
    $query = "SELECT id, source_text, source_language, target_language FROM translations WHERE translated_text IS NULL LIMIT 10";
    $result = pg_query($db, $query);

    if (!$result) {
        die("Ошибка выполнения запроса: " . pg_last_error($db));
    }

    $rows = pg_fetch_all($result);

    if (empty($rows)) {
        echo "Нет фраз для перевода.";
        exit;
    }

    // Переводим каждую фразу
    foreach ($rows as $row) {
        $translatedText = translateText(
            $row['source_text'],
            $row['source_language'],
            $row['target_language']
        );

        if ($translatedText !== false) {
            // Обновляем запись в базе данных
            $updateQuery = "UPDATE translations SET translated_text = $1, updated_at = NOW() WHERE id = $2";
            $updateResult = pg_query_params(
                $db,
                $updateQuery,
                [$translatedText, $row['id']]
            );

            if (!$updateResult) {
                error_log("Ошибка обновления записи ID {$row['id']}: " . pg_last_error($db));
            } else {
                echo "Переведено: {$row['source_text']} => $translatedText",PHP_EOL;
            }
        }
    }

    echo "Готово! Переведено " . count($rows) . " фраз.";

} catch (Exception $e) {
    die("Ошибка: " . $e->getMessage());
} finally {
    if (isset($db)) {
        pg_close($db);
    }
}