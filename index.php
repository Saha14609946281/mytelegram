<?php

require_once 'vendor/autoload.php';

use danog\MadelineProto\API;
use danog\MadelineProto\Settings;
use danog\MadelineProto\Settings\AppInfo;
use danog\MadelineProto\Settings\Database\Postgres;

// 1. Настройки API (твои данные)
$settings = new Settings();
$appInfo = new AppInfo();
$appInfo->setApiId(26241381); 
$appInfo->setApiHash('fe1046e04b4a0196b0e5efcbc4d62093');
$settings->setAppInfo($appInfo);

// 2. Настройка базы данных (Supabase)
$dbUri = getenv('DATABASE_URL');
if ($dbUri) {
    $postgres = new Postgres();
    $postgres->setUri($dbUri);
    $settings->setDb($postgres);
}

try {
    $MadelineProto = new API('session.madeline', $settings);

    // Веб-интерфейс для проверки и авторизации
    if (PHP_SAPI !== 'cli') {
        if ($MadelineProto->getAuthorization() === \danog\MadelineProto\API::NOT_LOGGED_IN) {
            echo "Сервер запущен. Зайдите в Runtime Logs в Koyeb, чтобы отсканировать QR-код или ввести номер.";
        } else {
            echo "Статус: Авторизован и работает!";
        }
        // Не закрываем скрипт, чтобы прокси продолжал работать
    }

    $MadelineProto->start();

    // 3. Запуск MTProto прокси для APK
    // Порт 8080 должен быть открыт в настройках Koyeb
    $MadelineProto->proxy->start(['port' => 8080]);

    // Выводим секретные данные в логи Koyeb
    $proxyData = $MadelineProto->proxy->getLibproxyUri();
    file_put_contents('php://stderr', "\n\n======= ДАННЫЕ ДЛЯ ТВОЕГО APK =======\n");
    file_put_contents('php://stderr', $proxyData . "\n");
    file_put_contents('php://stderr', "=====================================\n\n");

    $MadelineProto->loop();

} catch (\Exception $e) {
    file_put_contents('php://stderr', "Ошибка: " . $e->getMessage() . "\n");
}
