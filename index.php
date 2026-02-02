<?php
// Максимальная выживаемость
set_time_limit(0);
ini_set('memory_limit', '512M');

require_once 'vendor/autoload.php';

use danog\MadelineProto\API;
use danog\MadelineProto\Settings;

// 1. Быстрый ответ для Koyeb Health Check
if (PHP_SAPI !== 'cli' && $_SERVER['REQUEST_URI'] === '/') {
    echo "Proxy Server is Running"; 
    exit;
}

$settings = new Settings();
$settings->getAppInfo()->setApiId(26241381); 
$settings->getAppInfo()->setApiHash('fe1046e04b4a0196b0e5efcbc4d62093');

try {
    // Храним сессию в локальном файле (session.madeline)
    $MadelineProto = new API('session.madeline', $settings);
    
    // Запускаем прокси на порту 8000 (внутренний порт Koyeb)
    // Секрет делаем простым и фиксированным
    $MadelineProto->start();
    $MadelineProto->proxy->start([
        'port' => 8000, 
        'secret' => '00112233445566778899aabbccddeeff'
    ]);
    
    $MadelineProto->loop();
} catch (\Exception $e) {
    file_put_contents('php://stderr', $e->getMessage());
}
