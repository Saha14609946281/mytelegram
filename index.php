<?php
require_once 'vendor/autoload.php';

use danog\MadelineProto\API;
use danog\MadelineProto\Settings;
use danog\MadelineProto\Settings\AppInfo;

// 1. Твои API данные
$settings = new Settings();
$appInfo = new AppInfo();
$appInfo->setApiId(26241381); 
$appInfo->setApiHash('fe1046e04b4a0196b0e5efcbc4d62093');
$settings->setAppInfo($appInfo);

$MadelineProto = new API('session.madeline', $settings);

// 2. Веб-интерфейс (заходи на https://твой-адрес.koyeb.app/)
if (PHP_SAPI !== 'cli') {
    echo '<div style="font-family:sans-serif; text-align:center; padding:50px;">';
    if ($MadelineProto->getAuthorization() === \danog\MadelineProto\API::NOT_LOGGED_IN) {
        if (!isset($_POST['phone']) && !isset($_POST['code'])) {
            echo '<form method="POST"><h2>Вход</h2><input name="phone" placeholder="+7..."><button>Далее</button></form>';
        } elseif (isset($_POST['phone']) && !isset($_POST['code'])) {
            $MadelineProto->phoneLogin($_POST['phone']);
            echo '<form method="POST"><input type="hidden" name="phone" value="'.$_POST['phone'].'"><h2>Код из Telegram</h2><input name="code"><button>Войти</button></form>';
        } elseif (isset($_POST['code'])) {
            $MadelineProto->completePhoneLogin($_POST['code']);
            echo '<h2 style="color:green;">Готово! Перезапустите APK.</h2>';
        }
    } else {
        echo '<h2 style="color:green;">Сервер работает!</h2><p>Прокси активен с твоим ключом.</p>';
    }
    echo '</div>';
}

$MadelineProto->start();

// 3. Запуск прокси с ФИКСИРОВАННЫМ секретом (32 символа)
$mySecret = '00112233445566778899aabbccddeeff';
$MadelineProto->proxy->start(['port' => 8080, 'secret' => $mySecret]);

$MadelineProto->loop();
