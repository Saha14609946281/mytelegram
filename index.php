<?php
require_once 'vendor/autoload.php';

use danog\MadelineProto\API;
use danog\MadelineProto\Settings;
use danog\MadelineProto\Settings\AppInfo;
use danog\MadelineProto\Settings\Database\Postgres;

$settings = new Settings();
$appInfo = new AppInfo();
// Твои данные из BuildVars.java
$appInfo->setApiId(26241381); 
$appInfo->setApiHash('fe1046e04b4a0196b0e5efcbc4d62093');
$settings->setAppInfo($appInfo);

$dbUri = getenv('DATABASE_URL');
if ($dbUri) {
    $postgres = new Postgres();
    $postgres->setUri($dbUri);
    $settings->setDb($postgres);
}

$MadelineProto = new API('session.madeline', $settings);

if (PHP_SAPI !== 'cli') {
    echo '<div style="font-family:sans-serif; text-align:center; margin-top:50px; background:#f4f4f9; padding:20px; border-radius:10px;">';
    if ($MadelineProto->getAuthorization() === \danog\MadelineProto\API::NOT_LOGGED_IN) {
        if (!isset($_POST['phone']) && !isset($_POST['code'])) {
            echo '<h2>Вход в Telegram</h2>
                  <form method="POST">
                    <input name="phone" placeholder="+79991234567" style="padding:10px; width:250px;"><br><br>
                    <button type="submit" style="padding:10px 20px; cursor:pointer;">Отправить код</button>
                  </form>';
        } elseif (isset($_POST['phone']) && !isset($_POST['code'])) {
            $MadelineProto->phoneLogin($_POST['phone']);
            echo '<h2>Код отправлен на '.$_POST['phone'].'</h2>
                  <form method="POST">
                    <input type="hidden" name="sent_to_phone" value="'.$_POST['phone'].'">
                    <input name="code" placeholder="Код из сообщения" style="padding:10px; width:250px;"><br><br>
                    <button type="submit" style="padding:10px 20px; cursor:pointer;">Войти</button>
                  </form>';
        } elseif (isset($_POST['code'])) {
            $MadelineProto->completePhoneLogin($_POST['code']);
            echo '<h2 style="color:green;">✅ Готово! Вы в системе.</h2>
                  <p>Теперь скопируйте Secret из логов Koyeb и вставьте в APK.</p>';
        }
    } else {
        echo '<h2 style="color:green;">✅ Сервер авторизован!</h2>
              <p>Загляните в Runtime Logs в панели Koyeb, там ваш ключ для Android.</p>';
    }
    echo '</div>';
}

$MadelineProto->start();
$MadelineProto->proxy->start(['port' => 8080]);

$proxyData = $MadelineProto->proxy->getLibproxyUri();
file_put_contents('php://stderr', "\n\n======= ДАННЫЕ ДЛЯ ТВОЕГО APK =======\n");
file_put_contents('php://stderr', $proxyData . "\n");
file_put_contents('php://stderr', "=====================================\n\n");

$MadelineProto->loop();
