<?php
// Сразу отвечаем Koyeb, что мы живы
if (PHP_SAPI !== 'cli') {
    echo "OK";
    exit;
}

require_once 'vendor/autoload.php';

use danog\MadelineProto\API;

$dbUri = getenv('DATABASE_URL');

$settings = [
    'db' => [
        'type' => 'postgres',
        'postgres' => [
            'uri' => $dbUri,
        ],
    ],
    'app_info' => [
        'api_id'   => 26241381, // Твой ID
        'api_hash' => 'fe1046e04b4a0196b0e5efcbc4d62093',
    ]
];

try {
    $MadelineProto = new API('session.madeline', $settings);
    // start() может занять время, поэтому Koyeb может ругаться. 
    // Но мы уже ответили "OK" через встроенный сервер в Dockerfile.
    $MadelineProto->start();
    echo "MadelineProto started successfully!";
    $MadelineProto->loop();
} catch (\Exception $e) {
    file_put_contents('php://stderr', "Error: " . $e->getMessage() . "\n");
}
