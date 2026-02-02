<?php
require_once 'vendor/autoload.php';

use danog\MadelineProto\API;
use danog\MadelineProto\Settings;

$settings = new Settings();
$settings->getAppInfo()->setApiId(26241381); 
$settings->getAppInfo()->setApiHash('fe1046e04b4a0196b0e5efcbc4d62093');

try {
    $MadelineProto = new API('session.madeline', $settings);
    
    // ВАЖНО: Сначала запускаем веб-часть для Koyeb Health Check
    if (PHP_SAPI !== 'cli') {
        echo "Server is running!"; 
        // Если авторизация нужна - покажем форму
        if ($MadelineProto->getAuthorization() === \danog\MadelineProto\API::NOT_LOGGED_IN) {
            // ... (тут твой код формы входа, который я давал выше)
        }
    }

    $MadelineProto->start();
    
    // Запускаем прокси ТОЛЬКО если порт свободен
    $MadelineProto->proxy->start(['port' => 8080, 'secret' => '00112233445566778899aabbccddeeff']);
    
    $MadelineProto->loop();
} catch (\Exception $e) {
    file_put_contents('php://stderr', $e->getMessage());
}
