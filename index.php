<?php

require_once 'vendor/autoload.php';

use danog\MadelineProto\API;
use danog\MadelineProto\Settings;
use danog\MadelineProto\Settings\Database\Postgres;
use danog\MadelineProto\Settings\AppInfo;

// 1. Получаем URL базы из Koyeb
$dbUri = getenv('DATABASE_URL');

// 2. Создаем объект настроек (вместо массива)
$settings = new Settings();

// Настройка приложения (API ID и Hash)
$appInfo = new AppInfo();
$appInfo->setApiId(26241381);     // ЗАМЕНИ НА СВОЙ
$appInfo->setApiHash('fe1046e04b4a0196b0e5efcbc4d62093'); // ЗАМЕНИ НА СВОЙ
$settings->setAppInfo($appInfo);

// Настройка базы данных PostgreSQL
if ($dbUri) {
    $postgres = new Postgres();
    $postgres->setUri($dbUri);
    $settings->setDb($postgres);
}

try {
    // Теперь передаем объект $settings, а не массив
    $MadelineProto = new API('session.madeline', $settings);
    
    // Ответ для Koyeb Health Check
    if (PHP_SAPI !== 'cli') {
        echo "OK - Server is running";
        exit;
    }

    $MadelineProto->start();
    $MadelineProto->loop();
} catch (\Exception $e) {
    file_put_contents('php://stderr', "Error: " . $e->getMessage() . "\n");
}
