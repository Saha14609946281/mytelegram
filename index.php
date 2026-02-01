<?php

if (!file_exists('vendor/autoload.php')) {
    die("Run composer install first!");
}

require_once 'vendor/autoload.php';

use danog\MadelineProto\API;

// 1. Настройки БД из переменной окружения Koyeb
$dbUri = getenv('DATABASE_URL');

$settings = [
    'db' => [
        'type' => 'postgres',
        'postgres' => [
            'uri' => $dbUri,
        ],
    ],
    'app_info' => [
        'api_id'   => 26241381, // ЗАМЕНИ НА СВОЙ
        'api_hash' => 'fe1046e04b4a0196b0e5efcbc4d62093', // ЗАМЕНИ НА СВОЙ
    ]
];

// 2. Инициализация
$MadelineProto = new API('session.madeline', $settings);

// 3. Простейший HTTP-ответ для Koyeb (чтобы не выключал сервер)
echo "Server is running!";

// 4. Запуск цикла (например, просто держим соединение)
$MadelineProto->start();
$MadelineProto->loop();
